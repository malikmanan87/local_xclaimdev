<?php

namespace App\Libraries;

/**
 * Perkhidmatan Integrasi API Inpersonel UniSZA (Senarai Pakar)
 * 
 * Menggunakan pengesahan token HMAC-SHA1 berasaskan tarikh:
 * key   = "Inpersonel@Un1sza" + tarikh_hari_ini (format Ymd, cth: 20260922)
 * token = HMAC-SHA1( key = key, message = "aplikasi" )
 */
class InpersonelService
{
    protected static string $secretKeyBase = 'Inpersonel@Un1sza';
    protected static string $message       = 'aplikasi';

    /**
     * Jana token HMAC-SHA1 mengikut tarikh semasa (format Ymd)
     */
    public static function generateToken(?string $dateYmd = null): string
    {
        $date = $dateYmd ?: date('Ymd');
        $key  = self::$secretKeyBase . $date;
        return hash_hmac('sha1', self::$message, $key);
    }

    /**
     * Dapatkan senarai semua pakar daripada API Inpersonel (dengan caching CI4)
     *
     * @return array Senarai pakar daripada API
     */
    public static function getAllSpecialists(): array
    {
        $cache = function_exists('service') ? service('cache') : (class_exists(\Config\Services::class) ? \Config\Services::cache() : null);
        $cacheKey = 'inpersonel_specialists_' . date('Ymd');

        if ($cache) {
            try {
                if ($cached = $cache->get($cacheKey)) {
                    return $cached;
                }
            } catch (\Throwable $e) {}
        }

        $token = self::generateToken();
        $urls = [
            'https://dataint.unisza.edu.my/api/senarai-pakar/info?token=' . $token,
            'http://10.0.20.17/api/senarai-pakar/info?token=' . $token,
        ];

        foreach ($urls as $url) {
            try {
                if (class_exists(\Config\Services::class)) {
                    $client = \Config\Services::curlrequest();
                    $response = $client->get($url, [
                        'timeout'     => 8,
                        'http_errors' => false,
                        'verify'      => false,
                    ]);
                    $statusCode = $response->getStatusCode();
                    $rawBody    = $response->getBody();
                } else {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                    $rawBody    = curl_exec($ch);
                    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                }

                if ($statusCode === 200) {
                    $body = json_decode($rawBody, true);
                    if (is_array($body) && !empty($body)) {
                        if ($cache) {
                            try {
                                $cache->save($cacheKey, $body, 3600); // Simpan dalam cache selama 1 jam
                            } catch (\Throwable $e) {}
                        }

                        return $body;
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Inpersonel API connect error (' . $url . '): ' . $e->getMessage());
            }
        }

        return [];
    }

    /**
     * Cari maklumat pakar mengikut Emel, No. Staf, Username, atau No. Kad Pengenalan
     *
     * @param string $identifier Emel / ID Staf / Username / No KP
     * @return array|null Maklumat pakar berserta jawatan_gred
     */
    public static function findSpecialist(string $identifier): ?array
    {
        $cleanId = trim($identifier);
        if (empty($cleanId)) {
            return null;
        }

        $specialists = self::getAllSpecialists();
        if (empty($specialists)) {
            return null;
        }

        $cleanLower = strtolower($cleanId);
        $usernamePrefix = strpos($cleanLower, '@') !== false ? explode('@', $cleanLower)[0] : $cleanLower;

        foreach ($specialists as $p) {
            $pEmail = strtolower(trim($p['emel'] ?? ''));
            $pStaff = strtolower(trim($p['nostaf'] ?? ''));
            $pIc    = trim($p['nopengenalan'] ?? '');

            // Pemadanan fleksibel: emel penuh, username emel, no staf, atau no kad pengenalan
            $matched = false;

            if ($pEmail === $cleanLower) {
                $matched = true;
            } elseif (!empty($pEmail) && strpos($pEmail, $usernamePrefix . '@') === 0) {
                $matched = true;
            } elseif ($pStaff === $cleanLower || (!empty($pStaff) && stripos($pStaff, $cleanLower) !== false)) {
                $matched = true;
            } elseif (!empty($pIc) && str_replace(['-', ' '], '', $pIc) === str_replace(['-', ' '], '', $cleanId)) {
                $matched = true;
            }

            if ($matched) {
                $pos = trim($p['jawatan'] ?? '');
                $grd = trim($p['gred'] ?? '');

                // Bentuk gabungan Jawatan & Gred (Jawatan + Gred)
                if (!empty($pos) && !empty($grd)) {
                    $posGrade = (stripos($pos, $grd) !== false) ? $pos : trim($pos . ' ' . $grd);
                } else {
                    $posGrade = $pos ?: $grd;
                }

                $p['jawatan_gred'] = $posGrade;

                return $p;
            }
        }

        return null;
    }
}
