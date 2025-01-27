<?php

namespace App\Controllers;

use App\Models\PurchaseOrderModel;

class Dashboard extends BaseController
{

    protected $purchaseOrderModel;

    public function __construct()
    {
        $this->purchaseOrderModel = new PurchaseOrderModel();
    }

    // public function index()
    // {
    //     if (!session()->get('logged_in')) {
    //         session()->setFlashdata('error', 'Please login first');
    //         return redirect()->to(base_url('login'));
    //     }

    //     if (empty(session()->get('name')) || 
    //         empty(session()->get('email')) || 
    //         empty(session()->get('role'))) {
            
    //         session()->destroy();
    //         session()->setFlashdata('error', 'Your session has expired');
    //         return redirect()->to(base_url('login'));
    //     }

    //     $userId = session()->get('id');

    //     $data = [
    //         'title' => 'Dashboard | DANA TEST',
    //         'user' => [
    //             'name' => session()->get('name'),
    //             'email' => session()->get('email'),
    //             'role' => session()->get('role'),
    //             'department' => session()->get('department'),
    //             'division' => session()->get('division')
    //         ]
    //     ];

    //     switch(session()->get('role')) {
    //         case 'manager':
    //             $currentUserDivision = session()->get('division');
    //             $data['request_status'] = $this->getManagerRequestStatus();
    //             $data['purchase_orders'] = $this->purchaseOrderModel->select('
    //                     purchase_orders.*,
    //                     users.name as requestor_name,
    //                     users.division
    //                 ')
    //                 ->join('users', 'users.id = purchase_orders.requestor_id')
    //                 ->where('users.division', $currentUserDivision)
    //                 ->orderBy('purchase_orders.created_at', 'DESC')
    //                 ->paginate(10);
    //             break;

    //         case 'management':
    //             $currentUserDepartment = session()->get('department');
    //             $data['request_status'] = $this->getManagementRequestStatus();
    //             $data['purchase_orders'] = $this->purchaseOrderModel->select('
    //                     purchase_orders.*,
    //                     users.name as requestor_name,
    //                     users.division
    //                 ')
    //                 ->join('users', 'users.id = purchase_orders.requestor_id')
    //                 ->where('users.department', $currentUserDepartment)
    //                 ->orderBy('purchase_orders.created_at', 'DESC')
    //                 ->paginate(10);
    //             break;
            
    //         case 'executive':
    //             $data['request_status'] = $this->getAllRequestStatus();
    //             $data['purchase_orders'] = $this->purchaseOrderModel->select('
    //                     purchase_orders.*,
    //                     users.name as requestor_name
    //                 ')
    //                 ->join('users', 'users.id = purchase_orders.requestor_id')
    //                 ->orderBy('purchase_orders.created_at', 'DESC')
    //                 ->paginate(10);
    //             break;

    //         case 'procurement':
    //             $data['request_status'] = $this->getAllRequestStatus();
    //             $data['purchase_orders'] = $this->purchaseOrderModel->select('
    //                     purchase_orders.*,
    //                     users.name as requestor_name
    //                 ')
    //                 ->join('users', 'users.id = purchase_orders.requestor_id')
    //                 ->orderBy('purchase_orders.created_at', 'DESC')
    //                 ->paginate(10);
    //             break;

    //         case 'requestor':
    //             $data['request_status'] = $this->getRequestStatus();
    //             $data['purchase_orders'] = $this->purchaseOrderModel->select('
    //                     purchase_orders.*,
    //                     users.name as requestor_name
    //                 ')
    //                 ->join('users', 'users.id = purchase_orders.requestor_id')
    //                 ->where('purchase_orders.requestor_id', $userId)
    //                 ->orderBy('purchase_orders.created_at', 'DESC')
    //                 ->paginate(10);
    //             break;

    //         default:
    //             session()->destroy();
    //             session()->setFlashdata('error', 'Role tidak valid');
    //             return redirect()->to(base_url('login'));
    //     }

    //     $data['pager'] = $this->purchaseOrderModel->pager;

    //     return view('dashboard', $data);
    // }

    public function index()
    {
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'Please login first');
            return redirect()->to(base_url('login'));
        }

        if (empty(session()->get('name')) || 
            empty(session()->get('email')) || 
            empty(session()->get('role'))) {
            
            session()->destroy();
            session()->setFlashdata('error', 'Your session has expired');
            return redirect()->to(base_url('login'));
        }

        $userId = session()->get('id');

        $search = $this->request->getGet('search');
        $currency = $this->request->getGet('currency');
        $status = $this->request->getGet('status');

        $data = [
            'title' => 'Dashboard | DANA TEST',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'department' => session()->get('department'),
                'division' => session()->get('division')
            ]
        ];

        $baseQuery = $this->purchaseOrderModel->select('
                purchase_orders.*,
                users.name as requestor_name,
                users.division
            ')
            ->join('users', 'users.id = purchase_orders.requestor_id');

        if (!empty($search)) {
            $baseQuery->groupStart()
                ->like('purchase_orders.title', $search)
                ->orLike('users.name', $search)
                ->orLike('purchase_orders.currency', $search)
                ->orLike('purchase_orders.status', $search)
                ->orLike('purchase_orders.total_amount', $search)
                ->groupEnd();
        }

        if (!empty($currency)) {
            $baseQuery->where('purchase_orders.currency', $currency);
        }
        if (!empty($status)) {
            $baseQuery->where('purchase_orders.status', $status);
        }

        switch(session()->get('role')) {
            case 'manager':
                $currentUserDivision = session()->get('division');
                $data['request_status'] = $this->getManagerRequestStatus();
                $data['purchase_orders'] = $baseQuery
                    ->where('users.division', $currentUserDivision)
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->paginate(10);
                break;

            case 'management':
                $currentUserDepartment = session()->get('department');
                $data['request_status'] = $this->getManagementRequestStatus();
                $data['purchase_orders'] = $baseQuery
                    ->where('users.department', $currentUserDepartment)
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->paginate(10);
                break;
            
            case 'executive':
            case 'procurement':
                $data['request_status'] = $this->getAllRequestStatus();
                $data['purchase_orders'] = $baseQuery
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->paginate(10);
                break;

            case 'requestor':
                $data['request_status'] = $this->getRequestStatus();
                $data['purchase_orders'] = $baseQuery
                    ->where('purchase_orders.requestor_id', $userId)
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->paginate(10);
                break;

            default:
                session()->destroy();
                session()->setFlashdata('error', 'Role tidak valid');
                return redirect()->to(base_url('login'));
        }

        $data['currencies'] = $this->purchaseOrderModel->distinct()
            ->select('currency')
            ->where('currency IS NOT NULL')
            ->findAll();

        $data['statuses'] = $this->purchaseOrderModel->distinct()
            ->select('status')
            ->where('status IS NOT NULL')
            ->findAll();

        $data['search'] = $search;
        $data['selectedCurrency'] = $currency;
        $data['selectedStatus'] = $status;
        
        $data['pager'] = $this->purchaseOrderModel->pager;

        return view('dashboard', $data);
    }

    private function getManagerRequestStatus()
    {
        try {
            $userId = session()->get('id');
            $userDivisionId = session()->get('division');
            
            if (!$userId || !$userDivisionId) {
                throw new \RuntimeException('User not logged in or division not set');
            }

            $purchaseOrderModel = new \App\Models\PurchaseOrderModel();
            
            $baseQuery = $purchaseOrderModel->select('purchase_orders.status, COUNT(*) as count')
                ->join('users', 'users.id = purchase_orders.requestor_id')
                ->where('users.division', $userDivisionId)
                ->groupBy('purchase_orders.status');

            $results = $baseQuery->get()->getResultArray();

            $counts = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
            ];

            foreach ($results as $row) {
                $counts['total'] += $row['count'];

                switch ($row['status']) {
                    case 'draft':
                    case 'pending':
                        $counts['pending'] += $row['count'];
                        break;
                    case 'approved':
                    case 'management':
                    case 'completed':
                        $counts['approved'] += $row['count'];
                        break;
                    case 'rejected':
                        $counts['rejected'] += $row['count'];
                        break;
                }
            }

            return $counts;
        } catch (\Exception $e) {
            log_message('error', '[Dashboard] Failed to get request status: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
        }
    }

    private function getManagementRequestStatus()
    {
        try {
            $userId = session()->get('id');
            $userDepartmentId = session()->get('department');
            
            if (!$userId || !$userDepartmentId) {
                throw new \RuntimeException('User not logged in or department not set');
            }

            $purchaseOrderModel = new \App\Models\PurchaseOrderModel();
            
            $baseQuery = $purchaseOrderModel->select('purchase_orders.status, COUNT(*) as count')
                ->join('users', 'users.id = purchase_orders.requestor_id')
                ->where('users.department', $userDepartmentId)
                ->groupBy('purchase_orders.status');

            $results = $baseQuery->get()->getResultArray();

            $counts = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
            ];

            foreach ($results as $row) {
                $counts['total'] += $row['count'];

                switch ($row['status']) {
                    case 'draft':
                    case 'pending':
                        $counts['pending'] += $row['count'];
                        break;
                    case 'approved':
                    case 'management':
                    case 'completed':
                        $counts['approved'] += $row['count'];
                        break;
                    case 'rejected':
                        $counts['rejected'] += $row['count'];
                        break;
                }
            }

            return $counts;
        } catch (\Exception $e) {
            log_message('error', '[Dashboard] Failed to get request status: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
        }
    }

    private function getAllRequestStatus()
    {
        try {
            $userId = session()->get('id');
            
            if (!$userId) {
                throw new \RuntimeException('User not logged in');
            }

            $purchaseOrderModel = new \App\Models\PurchaseOrderModel();
            
            $baseQuery = $purchaseOrderModel->select('purchase_orders.status, COUNT(*) as count')
                ->join('users', 'users.id = purchase_orders.requestor_id')
                ->groupBy('purchase_orders.status');

            $results = $baseQuery->get()->getResultArray();

            $counts = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
            ];

            foreach ($results as $row) {
                $counts['total'] += $row['count'];

                switch ($row['status']) {
                    case 'draft':
                    case 'pending':
                        $counts['pending'] += $row['count'];
                        break;
                    case 'approved':
                    case 'management':
                    case 'completed':
                        $counts['approved'] += $row['count'];
                        break;
                    case 'rejected':
                        $counts['rejected'] += $row['count'];
                        break;
                }
            }

            return $counts;
        } catch (\Exception $e) {
            log_message('error', '[Dashboard] Failed to get request status: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
        }
    }

    private function getRequestStatus()
    {
        try {
            $userId = session()->get('id');
            
            if (!$userId) {
                throw new \RuntimeException('User not logged in');
            }
            
            $purchaseOrderModel = new \App\Models\PurchaseOrderModel();
            
            $baseQuery = $purchaseOrderModel->select('purchase_orders.status, COUNT(*) as count')
                ->join('users', 'users.id = purchase_orders.requestor_id')
                ->where('users.id', $userId)
                ->groupBy('purchase_orders.status');

            $results = $baseQuery->get()->getResultArray();

            $counts = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
            ];

            foreach ($results as $row) {
                $counts['total'] += $row['count'];

                switch ($row['status']) {
                    case 'draft':
                    case 'pending':
                        $counts['pending'] += $row['count'];
                        break;
                    case 'approved':
                    case 'management':
                    case 'completed':
                        $counts['approved'] += $row['count'];
                        break;
                    case 'rejected':
                        $counts['rejected'] += $row['count'];
                        break;
                }
            }

            return $counts;
        } catch (\Exception $e) {
            log_message('error', '[Dashboard] Failed to get request status: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
        }
    }
}
