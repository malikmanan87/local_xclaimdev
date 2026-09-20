<?php

namespace App\Controllers;

use App\Models\MmaProcedureModel;
use App\Traits\LoggableTrait;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProceduresController extends BaseController
{
    use LoggableTrait;

    protected MmaProcedureModel $procedureModel;

    public function __construct()
    {
        $this->procedureModel = new MmaProcedureModel();
        helper(['form', 'url']);
    }

    // ----------------------------------------------------------------
    // GET /procedures (Senarai Prosedur & Statistik)
    // ----------------------------------------------------------------
    public function index()
    {
        $procedures = $this->procedureModel->getAllProcedures();
        $stats      = $this->procedureModel->getStatistics();
        $sections   = $this->procedureModel->getDistinctSections();
        $categories = $this->procedureModel->getDistinctCategories();

        return view('procedures/index', [
            'pageTitle'  => 'Pengurusan Prosedur MMA',
            'breadcrumb' => ['Prosedur MMA'],
            'procedures' => $procedures,
            'stats'      => $stats,
            'sections'   => $sections,
            'categories' => $categories,
        ]);
    }

    // ----------------------------------------------------------------
    // GET /procedures/create (Borang Tambah Prosedur)
    // ----------------------------------------------------------------
    public function create()
    {
        $sections   = $this->procedureModel->getDistinctSections();
        $categories = $this->procedureModel->getDistinctCategories();

        return view('procedures/form', [
            'pageTitle'  => 'Tambah Prosedur MMA Baharu',
            'breadcrumb' => [['label' => 'Prosedur MMA', 'url' => base_url('procedures')], 'Tambah Prosedur'],
            'sections'   => $sections,
            'categories' => $categories,
            'procedure'  => null,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /procedures/store (Simpan Prosedur Baharu)
    // ----------------------------------------------------------------
    public function store()
    {
        $rules = [
            'code' => [
                'rules'  => 'required|min_length[2]|max_length[10]|is_unique[mma_procedures.code]',
                'errors' => [
                    'required'   => 'Kod prosedur MMA wajib diisi.',
                    'min_length' => 'Kod prosedur mestilah sekurang-kurangnya 2 aksara.',
                    'max_length' => 'Kod prosedur tidak boleh melebihi 10 aksara.',
                    'is_unique'  => 'Kod prosedur ini sudah wujud dalam sistem. Sila gunakan kod yang berbeza.',
                ],
            ],
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required'   => 'Nama prosedur wajib diisi.',
                    'min_length' => 'Nama prosedur mestilah sekurang-kurangnya 3 aksara.',
                ],
            ],
            'section' => [
                'rules'  => 'permit_empty|max_length[255]',
            ],
            'category' => [
                'rules'  => 'permit_empty|max_length[255]',
            ],
            'surgeon_fee' => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'              => 'Fi surgeri wajib diisi (letak 0.00 jika tiada).',
                    'numeric'               => 'Fi surgeri mestilah dalam bentuk angka.',
                    'greater_than_equal_to' => 'Fi surgeri tidak boleh bernilai negatif.',
                ],
            ],
            'anaesthetist_fee' => [
                'rules'  => 'permit_empty|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'numeric'               => 'Fi bius mestilah dalam bentuk angka.',
                    'greater_than_equal_to' => 'Fi bius tidak boleh bernilai negatif.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code            = trim($this->request->getPost('code'));
        $name            = trim($this->request->getPost('name'));
        $section         = trim($this->request->getPost('section')) ?: 'General Section';
        $category        = trim($this->request->getPost('category')) ?: 'Uncategorized';
        $surgeonFee      = (float) $this->request->getPost('surgeon_fee');
        $anaesthetistFee = (float) ($this->request->getPost('anaesthetist_fee') ?? 0);

        $data = [
            'code'             => $code,
            'name'             => $name,
            'section'          => $section,
            'category'         => $category,
            'surgeon_fee'      => $surgeonFee,
            'anaesthetist_fee' => $anaesthetistFee,
        ];

        $insertId = $this->procedureModel->insert($data);

        $this->logActivity(
            'Tambah Prosedur MMA',
            "Mendaftar prosedur MMA baharu: [{$code}] {$name} (Fi Surgeri: RM " . number_format($surgeonFee, 2) . ", Fi Bius: RM " . number_format($anaesthetistFee, 2) . ")"
        );

        return redirect()->to(base_url('procedures'))
            ->with('success', "Prosedur MMA <strong>[{$code}] {$name}</strong> berjaya ditambah ke dalam sistem.");
    }

    // ----------------------------------------------------------------
    // GET /procedures/edit/(:num) (Borang Kemaskini Prosedur)
    // ----------------------------------------------------------------
    public function edit($id)
    {
        $procedure = $this->procedureModel->find($id);

        if (!$procedure) {
            throw PageNotFoundException::forPageNotFound("Prosedur ID {$id} tidak dijumpai.");
        }

        $sections   = $this->procedureModel->getDistinctSections();
        $categories = $this->procedureModel->getDistinctCategories();

        return view('procedures/form', [
            'pageTitle'  => 'Kemaskini Prosedur MMA',
            'breadcrumb' => [['label' => 'Prosedur MMA', 'url' => base_url('procedures')], 'Kemaskini #' . $procedure['code']],
            'sections'   => $sections,
            'categories' => $categories,
            'procedure'  => $procedure,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /procedures/update/(:num) (Simpan Kemaskini Prosedur)
    // ----------------------------------------------------------------
    public function update($id)
    {
        $procedure = $this->procedureModel->find($id);

        if (!$procedure) {
            throw PageNotFoundException::forPageNotFound("Prosedur ID {$id} tidak dijumpai.");
        }

        $rules = [
            'code' => [
                'rules'  => "required|min_length[2]|max_length[10]|is_unique[mma_procedures.code,id,{$id}]",
                'errors' => [
                    'required'   => 'Kod prosedur MMA wajib diisi.',
                    'min_length' => 'Kod prosedur mestilah sekurang-kurangnya 2 aksara.',
                    'max_length' => 'Kod prosedur tidak boleh melebihi 10 aksara.',
                    'is_unique'  => 'Kod prosedur ini telah digunakan oleh prosedur lain. Sila semak semula.',
                ],
            ],
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required'   => 'Nama prosedur wajib diisi.',
                    'min_length' => 'Nama prosedur mestilah sekurang-kurangnya 3 aksara.',
                ],
            ],
            'section' => [
                'rules'  => 'permit_empty|max_length[255]',
            ],
            'category' => [
                'rules'  => 'permit_empty|max_length[255]',
            ],
            'surgeon_fee' => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'              => 'Fi surgeri wajib diisi (letak 0.00 jika tiada).',
                    'numeric'               => 'Fi surgeri mestilah dalam bentuk angka.',
                    'greater_than_equal_to' => 'Fi surgeri tidak boleh bernilai negatif.',
                ],
            ],
            'anaesthetist_fee' => [
                'rules'  => 'permit_empty|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'numeric'               => 'Fi bius mestilah dalam bentuk angka.',
                    'greater_than_equal_to' => 'Fi bius tidak boleh bernilai negatif.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code            = trim($this->request->getPost('code'));
        $name            = trim($this->request->getPost('name'));
        $section         = trim($this->request->getPost('section')) ?: 'General Section';
        $category        = trim($this->request->getPost('category')) ?: 'Uncategorized';
        $surgeonFee      = (float) $this->request->getPost('surgeon_fee');
        $anaesthetistFee = (float) ($this->request->getPost('anaesthetist_fee') ?? 0);

        $data = [
            'code'             => $code,
            'name'             => $name,
            'section'          => $section,
            'category'         => $category,
            'surgeon_fee'      => $surgeonFee,
            'anaesthetist_fee' => $anaesthetistFee,
        ];

        $this->procedureModel->update($id, $data);

        $this->logActivity(
            'Kemaskini Prosedur MMA',
            "Mengemas kini prosedur MMA ID #{$id}: [{$code}] {$name} (Fi Surgeri: RM " . number_format($surgeonFee, 2) . ", Fi Bius: RM " . number_format($anaesthetistFee, 2) . ")"
        );

        return redirect()->to(base_url('procedures'))
            ->with('success', "Prosedur MMA <strong>[{$code}] {$name}</strong> berjaya dikemas kini.");
    }

    // ----------------------------------------------------------------
    // GET /procedures/delete/(:num) (Padam Prosedur)
    // ----------------------------------------------------------------
    public function delete($id)
    {
        $procedure = $this->procedureModel->find($id);

        if (!$procedure) {
            return redirect()->to(base_url('procedures'))
                ->with('error', 'Prosedur tidak dijumpai.');
        }

        $this->procedureModel->delete($id);

        $this->logActivity(
            'Padam Prosedur MMA',
            "Memadam prosedur MMA ID #{$id}: [{$procedure['code']}] {$procedure['name']}"
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Prosedur [{$procedure['code']}] {$procedure['name']} berjaya dipadam."
            ]);
        }

        return redirect()->to(base_url('procedures'))
            ->with('success', "Prosedur <strong>[{$procedure['code']}] {$procedure['name']}</strong> telah berjaya dipadam.");
    }
}
