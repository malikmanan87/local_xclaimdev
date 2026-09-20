<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Traits\LoggableTrait;

class AccessRequestsController extends BaseController
{
    use LoggableTrait;

    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    // ----------------------------------------------------------------
    // GET /access-requests — Senarai semua permohonan (Admin)
    // ----------------------------------------------------------------
    public function index(): string
    {
        $requests = $this->userModel->getAccessRequests();

        $data = [
            'pageTitle'  => 'Access Requests',
            'breadcrumb' => ['Access Requests'],
            'requests'   => $requests,
            'counts'     => [
                'pending'  => count(array_filter($requests, fn($r) => $r['access_status'] === 'pending')),
                'approved' => count(array_filter($requests, fn($r) => $r['access_status'] === 'approved')),
                'rejected' => count(array_filter($requests, fn($r) => $r['access_status'] === 'rejected')),
            ],
            'roles'      => $this->roleModel->findAll(),
        ];

        return view('access_requests/index', $data);
    }

    // ----------------------------------------------------------------
    // POST /access-requests/approve/:id — Luluskan permohonan
    // ----------------------------------------------------------------
    public function approve(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.']);
        }

        $roleId = (int) $this->request->getPost('role_id');
        $note   = $this->request->getPost('note') ?? '';

        $this->userModel->update($id, [
            'access_status'      => 'approved',
            'access_note'        => $note,
            'access_reviewed_by' => session('user_id'),
            'access_reviewed_at' => date('Y-m-d H:i:s'),
            'role_id'            => $roleId ?: $user['role_id'],
            'is_active'          => 1,
        ]);

        $this->logActivity('Approve Access', 'Approved access for: ' . $user['email'] . ' with role_id: ' . $roleId);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Access request approved successfully.',
        ]);
    }

    // ----------------------------------------------------------------
    // POST /access-requests/reject/:id — Tolak permohonan
    // ----------------------------------------------------------------
    public function reject(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found.']);
        }

        $note = $this->request->getPost('note') ?? '';

        $this->userModel->update($id, [
            'access_status'      => 'rejected',
            'access_note'        => $note,
            'access_reviewed_by' => session('user_id'),
            'access_reviewed_at' => date('Y-m-d H:i:s'),
            'is_active'          => 0,
        ]);

        $this->logActivity('Reject Access', 'Rejected access for: ' . $user['email'] . '. Reason: ' . $note);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Access request rejected.',
        ]);
    }
}
