<?php

namespace App\Controllers;

use App\Models\PurchaseOrderModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use TCPDF;

class PurchaseOrderController extends BaseController
{
    protected $purchaseOrderModel;
    protected $session;
    protected $validation;

    public function __construct()
    {
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->session = session();
        $this->validation = \Config\Services::validation();
    }

    // public function index()
    // {
    //     $userId = $this->session->get('user_id');
        
    //     $stats = [
    //         'total' => $this->purchaseOrderModel->where('requestor_id', $userId)->countAllResults(),
    //         'pending' => $this->purchaseOrderModel->where('requestor_id', $userId)
    //                                             ->where('status', 'pending')
    //                                             ->countAllResults(),
    //         'approved' => $this->purchaseOrderModel->where('requestor_id', $userId)
    //                                              ->where('status', 'approved')
    //                                              ->countAllResults(),
    //         'rejected' => $this->purchaseOrderModel->where('requestor_id', $userId)
    //                                              ->where('status', 'rejected')
    //                                              ->countAllResults(),
    //     ];

    //     $data = [
    //         'purchase_orders' => $this->purchaseOrderModel->where('requestor_id', $userId)
    //                                                     ->orderBy('created_at', 'DESC')
    //                                                     ->paginate(10),
    //         'pager' => $this->purchaseOrderModel->pager,
    //         'stats' => $stats
    //     ];

    //     return view('purchase_orders/index', $data);
    // }

    public function new()
    {
        $data = [
            'title' => 'Dashboard | DANA TEST',
            'user' => [
                'userId' => session()->get('id'),
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'division' => session()->get('division')
            ]
        ];

        return view('purchase_orders/create', $data);
    }

    public function create()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'currency' => 'required|in_list[IDR,USD]',
            'total_amount' => 'required|numeric|greater_than[0]',
            'attachment' => [
                'rules' => 'permit_empty|max_size[attachment,5120]|ext_in[attachment,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]',
                'errors' => [
                    'max_size' => 'File size cannot exceed 5MB',
                    'ext_in' => 'Invalid file type. Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG'
                ]
            ],
            'attachment2' => [
                'rules' => 'permit_empty|max_size[attachment2,5120]|ext_in[attachment2,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]',
                'errors' => [
                    'max_size' => 'File size cannot exceed 5MB',
                    'ext_in' => 'Invalid file type. Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG'
                ]
            ],
            'due_date' => 'required|valid_date[Y-m-d]',
            'statement_accepted' => 'required|in_list[on,1]'
        ];                

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->with('error', $this->validator->getErrors())
                            ->withInput();
        }

        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->back()
                            ->with('error', 'User session not found')
                            ->withInput();
        }
        
        // $date = date('ym');
        // $random = sprintf('%04d', mt_rand(1, 9999));
        // $poNumber = "PO{$date}{$random}";

        $data = [
            'po_number' => $this->request->getPost('po_number') ?: null,
            'requestor_id' => $userId,
            'title' => trim($this->request->getPost('title')),
            'currency' => trim($this->request->getPost('currency')),
            'total_amount' => (float)$this->request->getPost('total_amount'),
            'notes' => trim($this->request->getPost('notes')),
            'attachment' => null,
            'attachment_type' => null,
            'notes2' => trim($this->request->getPost('notes2')),
            'due_date' => date('Y-m-d', strtotime($this->request->getPost('due_date'))),
            'status' => 'pending',
            'statement_accepted' => 1,
            'attachment2' => null,
            'attachment_type2' => null
        ];        

        if ($this->request->getFile('attachment')->isValid()) {
            $file = $this->request->getFile('attachment');
            try {
                $uploadPath = FCPATH . 'uploads/purchase_orders';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                chmod($uploadPath, 0777);

                $newName = $file->getRandomName();
                
                if ($file->move($uploadPath, $newName)) {
                    $data['attachment'] = $newName;
                    $data['attachment_type'] = $file->getClientMimeType();
                    $data['attachment_size'] = $file->getSize();
                } else {
                    throw new \RuntimeException('Failed to move uploaded file');
                }

            } catch (\Exception $e) {
                log_message('error', '[FileUpload] Failed: ' . $e->getMessage());
                return redirect()->back()
                                ->with('error', 'File upload failed: ' . $e->getMessage())
                                ->withInput();
            }
        }

        if ($this->request->getFile('attachment2')->isValid()) {
            $file2 = $this->request->getFile('attachment2');
            try {
                $uploadPath = FCPATH . 'uploads/purchase_orders';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                chmod($uploadPath, 0777);

                $newName2 = $file2->getRandomName();
                
                if ($file2->move($uploadPath, $newName2)) {
                    $data['attachment2'] = $newName2;
                    $data['attachment_type2'] = $file2->getClientMimeType();
                    $data['attachment_size2'] = $file2->getSize();
                } else {
                    throw new \RuntimeException('Failed to move second uploaded file');
                }

            } catch (\Exception $e) {
                log_message('error', '[FileUpload2] Failed: ' . $e->getMessage());
                return redirect()->back()
                                ->with('error', 'Second file upload failed: ' . $e->getMessage())
                                ->withInput();
            }
        }

        try {
            $inserted = $this->purchaseOrderModel->insert($data);
            if (!$inserted) {
                throw new \RuntimeException('Failed to insert data into database');
            }

            return redirect()->to('dashboard')
                            ->with('success', 'Purchase created successfully');

        } catch (\Exception $e) {
            log_message('error', '[PurchaseOrder] Create failed: ' . $e->getMessage());
            
            if (isset($newName) && file_exists($uploadPath . '/' . $newName)) {
                unlink($uploadPath . '/' . $newName);
            }

            return redirect()->back()
                            ->with('error', 'Failed to create Purchase')
                            ->withInput();
        }
    }

    public function view($id)
    {
        $currentUserRole = session()->get('role');
        $currentUserId = session()->get('id');

        $po = $this->purchaseOrderModel->select('
            purchase_orders.*,
            users.name as requestor_name,
            users.email as requestor_email
        ')
            ->join('users', 'users.id = purchase_orders.requestor_id')
            ->find($id);
        
        if (!$po) {
            return redirect()->to('dashboard')
                ->with('error', 'Purchase not found');
        }

        $isAuthorized = false;
        
        switch ($currentUserRole) {
            case 'executive':
            case 'management':
            case 'manager':
            case 'procurement':
                $isAuthorized = true;
                break;
            case 'requestor':
                $isAuthorized = ($po['requestor_id'] === $currentUserId);
                break;
            default:
                $isAuthorized = false;
        }

        if (!$isAuthorized) {
            return redirect()->to('dashboard')
                ->with('error', 'You are not authorized to view this Purchase');
        }

        $po['manager_name'] = $this->getApproverName($po['approved_by_manager']);
        $po['management_name'] = $this->getApproverName($po['approved_by_management']);
        $po['executive_name'] = $this->getApproverName($po['approved_by_executive']);

        $checkValuePurchase = ($po['currency'] === 'IDR' && $po['total_amount'] < 200000001) || 
                     ($po['currency'] === 'USD' && $po['total_amount'] < 12368);

        switch ($checkValuePurchase) {
            case true:
                $isValuePurchase = true;
                break;
            
            default:
                $isValuePurchase = false;
                break;
        }

        $data = [
            'title' => 'Exception Paper | DANA TEST',
            'user' => [
                'userId' => session()->get('id'),
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'division' => session()->get('division')
            ],
            'po' => $po,
            'status_badge_class' => $this->getStatusBadgeClass($po['status']),
            'is_value_purchase' => $isValuePurchase,
            'is_executive' => $currentUserRole === 'executive',
            'is_manager' => $currentUserRole === 'manager',
            'is_management' => $currentUserRole === 'management',
            'is_procurement' => $currentUserRole === 'procurement'
        ];

        return view('purchase_orders/view', $data);
    }

    private function getApproverName(?int $approverId): string
    {
        if (empty($approverId)) {
            return '';
        }

        $userModel = new UserModel();
        $user = $userModel->select('name')->find($approverId);
        
        return $user ? $user['name'] : '';
    }

    private function getStatusBadgeClass($status)
    {
        return [
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'cancelled' => 'bg-secondary',
        ][$status] ?? 'bg-primary';
    }

    public function accept($id)
    {
        try {
            if (session()->get('role') !== 'manager') {
                throw new \RuntimeException('Unauthorized access');
            }

            $po = $this->purchaseOrderModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Purchase not found');
            }
            
            if ($po['status'] !== 'pending') {
                throw new \RuntimeException('Purchase is not in pending status');
            }

            $updateData = [
                'status' => 'approved',
                'approved_by_manager' => session()->get('id'),
                'approved_manager_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->purchaseOrderModel->update($id, $updateData);

            if (
                (($po['currency'] === 'IDR' && $po['total_amount'] < 200000001) || 
                 ($po['currency'] === 'USD' && $po['total_amount'] < 12368))
            ) {
               $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                
               $pdf->SetCreator('DANA Test');
               $pdf->SetAuthor('Teguh Arief');
               $pdf->SetTitle('Exception Paper - ' . $po['title']);
               
               $pdf->SetMargins(15, 35, 15);
               $pdf->SetHeaderMargin(10);
               $pdf->setPrintHeader(false);
               $pdf->setPrintFooter(false);
               
               $pdf->AddPage();

               $image_file = $_SERVER['DOCUMENT_ROOT'] . '/logo.jpeg';
               $pdf->Image($image_file, 12, 10, 190);

               $userModel = new UserModel();
               $requestor = $userModel->find($po['requestor_id']);
               
               $html = '
                <style>
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 15px;
                    }
                    td { 
                        padding: 8px;
                    }
                    h1 {
                        color: #333;
                        margin-bottom: 20px;
                    }
                </style>

                <h1>Exception Paper</h1>

                <p><strong>Date of request:</strong> ' . date('d M Y', strtotime($po['created_at'])) . '</p>';

                $html = '<p><strong>Purchase Title:</strong> ' . esc($po['title']);
                if (!empty($po['po_number'])) {
                    $html .= '<br><strong>PR Number:</strong> ' . esc($po['po_number']);
                }
                $html .= '</p>';

                if (!empty($po['notes'])) {
                    // $notes = strip_tags($po['notes'], '<br>');
                    $notes = html_entity_decode($po['notes']);
                    $html .= '
                        <p><strong>The reason for not following tender/pitching process:</strong><br>' . $notes . '</p>
                    ';

                    if (!empty($po['attachment'])) {
                        if (strpos($po['attachment_type'], 'image/') === 0) {
                            $imagePath1 = FCPATH . 'uploads/purchase_orders/' . $po['attachment'];
                            if (file_exists($imagePath1)) {
                                $imageData1 = base64_encode(file_get_contents($imagePath1));
                                $html .= '
                                    <div class="mb-3">
                                        <img src="@' . $imageData1 . '" 
                                            style="width: 300px;" 
                                            alt="Attachment 1">
                                    </div>
                                ';
                            }
                        } else {
                            $html .= '
                                <div class="mb-3">
                                    <a href="' . base_url('uploads/purchase_orders/' . $po['attachment']) . '" 
                                       class="btn btn-sm btn-primary" 
                                       target="_blank">
                                        <i class="bi bi-file-earmark"></i> 
                                        ' . base_url('uploads/purchase_orders/' . $po['attachment']) . '
                                    </a>
                                </div>
                            ';
                        }
                    }
                }
                
                if (!empty($po['notes2'])) {
                    // $notes2 = strip_tags($po['notes2'], '<br>');
                    $notes2 = html_entity_decode($po['notes2']);
                    $html .= '
                        <p><strong>Impact to DANA as (Organization) if we are failed to perform the agreement as soon as possible:</strong><br>' . $notes2 . '</p>
                    ';

                    if (!empty($po['attachment2'])) {
                        if (strpos($po['attachment_type2'], 'image/') === 0) {
                            $imagePath2 = FCPATH . 'uploads/purchase_orders/' . $po['attachment2'];
                            if (file_exists($imagePath2)) {
                                $imageData2 = base64_encode(file_get_contents($imagePath2));
                                $html .= '
                                    <div class="mb-3">
                                        <img src="@' . $imageData2 . '" 
                                            style="width: 300px;" 
                                            alt="Attachment 2">
                                    </div>
                                ';
                            }
                        } else {
                            $html .= '
                                <div class="mb-3">
                                    <a href="' . base_url('uploads/purchase_orders/' . $po['attachment2']) . '" 
                                       class="btn btn-sm btn-primary" 
                                       target="_blank">
                                        <i class="bi bi-file-earmark"></i> 
                                        ' . base_url('uploads/purchase_orders/' . $po['attachment2']) . '
                                    </a>
                                </div>
                            ';
                        }
                    }
                }              

                $html .= '
                <p><strong>Due date for the ordering confirmation:</strong> ' . date('d M Y', strtotime($po['due_date'])) . '</p>
                <p><strong>Cost to proceed the order:</strong> ' . $po['currency'] . '' . number_format($po['total_amount'], 2) . '</p>
                <p><em><strong>Requestor Statement:</strong><br>Hereby I declare that this request to justified the urgency, without any conflict of interest to the selected vendor.</em></p>
                ';

                $html .= '
                <table style="margin-top: 30px;">
                    <tr>
                        <td class="label" style="border: 1px solid #ddd; height: 80px; width:25%; vertical-align: top;"><strong>Requestor:</strong>
                            <br><br>
                            <div>' . esc($requestor['name']) . '<br>Date: ' . date('d M Y', strtotime($po['created_at'])) . '</div>
                        </td>
                        <td class="label" style="height: 80px; width:25%; vertical-align: top;">
                        </td>
                        <td class="label" style="border: 1px solid #ddd; height: 80px; width:25%; vertical-align: top;"><strong>Line Manager:</strong>
                            <br><br>
                            <div>' . session()->get('name') . '<br>Date: ' . date('d M Y') . '</div>
                        </td>
                    </tr>
                </table>';

               $pdf->writeHTML($html, true, false, true, false, '');
               
               $filename = 'PO_' . $po['id'] . '.pdf';
               $filepath = FCPATH . 'uploads/purchase_orders/' . $filename;
               
               if (!is_dir(dirname($filepath))) {
                   mkdir(dirname($filepath), 0777, true);
               }
               
               $pdf->Output($filepath, 'F');

               $this->sendApprovalNotification($po, $requestor);
            }

            return redirect()->to('purchase-orders/view/' . $id)
                ->with('success', 'Purchase has been approved successfully');

        } catch (\Exception $e) {
            log_message('error', '[PurchaseOrder] Approval failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve purchase: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $userRole = session()->get('role');
            if ($userRole !== 'manager' && $userRole !== 'management' && $userRole !== 'executive') {
                throw new \RuntimeException('Unauthorized access');
            }

            $po = $this->purchaseOrderModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Purchase not found');
            }

            if ($po['status'] === 'approved') {
                $updateData = [
                    'status' => 'rejected',
                    'approved_by_management' => session()->get('id'),
                    'approved_management_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }elseif($po['status'] === 'management'){
                $updateData = [
                    'status' => 'rejected',
                    'approved_by_executive' => session()->get('id'),
                    'approved_executive_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }else{
                $updateData = [
                    'status' => 'rejected',
                    'approved_by_manager' => session()->get('id'),
                    'approved_manager_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            $updated = $this->purchaseOrderModel->update($id, $updateData);
            
            if (!$updated) {
                throw new \RuntimeException('Failed to update purchase: ' . implode(', ', $this->purchaseOrderModel->errors()));
            }

            $userModel = new UserModel();
            $requestor = $userModel->find($po['requestor_id']);

            $this->sendRejectionNotification($po, $requestor);

            return redirect()->to('purchase-orders/view/' . $id)
                ->with('success', 'Purchase has been rejected');

        } catch (\Exception $e) {
            log_message('error', '[PurchaseOrder] Rejection failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject purchase: ' . $e->getMessage());
        }
    }

    public function management($id)
    {
        try {
            $userRole = session()->get('role');
            if ($userRole !== 'management') {
                throw new \RuntimeException('Unauthorized access');
            }

            $po = $this->purchaseOrderModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Purchase not found');
            }

            $updateData = [
                'status' => 'management',
                'approved_by_management' => session()->get('id'),
                'approved_management_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $updated = $this->purchaseOrderModel->update($id, $updateData);
            
            if (!$updated) {
                throw new \RuntimeException('Failed to update purchase: ' . implode(', ', $this->purchaseOrderModel->errors()));
            }

            return redirect()->to('purchase-orders/view/' . $id)
                ->with('success', 'Purchase has been management');

        } catch (\Exception $e) {
            log_message('error', '[PurchaseOrder] Rejection failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject purchase: ' . $e->getMessage());
        }
    }

    public function complete($id)
    {
        try {
            if (session()->get('role') !== 'executive') {
                throw new \RuntimeException('Unauthorized access');
            }

            $po = $this->purchaseOrderModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Purchase not found');
            }
            
            if ($po['status'] !== 'management') {
                throw new \RuntimeException('Purchase is not in approved status');
            }

            $updateData = [
                'status' => 'completed',
                'approved_by_executive' => session()->get('id'),
                'approved_executive_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->purchaseOrderModel->update($id, $updateData);
            
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                
            $pdf->SetCreator('DANA Test');
            $pdf->SetAuthor('Teguh Arief');
            $pdf->SetTitle('Exception Paper - ' . $po['title']);
            
            $pdf->SetMargins(15, 35, 15);
            $pdf->SetHeaderMargin(10);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            $pdf->AddPage();

            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/logo.jpeg';
            $pdf->Image($image_file, 12, 10, 190);

            $userModel = new UserModel();
            $requestor = $userModel->find($po['requestor_id']);
            $manager = $userModel->find($po['approved_by_manager']);
            $management = $userModel->find($po['approved_by_management']);
            
            $html = '
            <style>
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 15px;
                }
                td { 
                    padding: 8px;
                }
                h1 {
                    color: #333;
                    margin-bottom: 20px;
                }
            </style>

            <h1>Exception Paper</h1>

            <p><strong>Date of request:</strong> ' . date('d M Y', strtotime($po['created_at'])) . '</p>';

            $html = '<p><strong>Purchase Title:</strong> ' . esc($po['title']);
            if (!empty($po['po_number'])) {
                $html .= '<br><strong>PR Number:</strong> ' . esc($po['po_number']);
            }
            $html .= '</p>';

            if (!empty($po['notes'])) {
                // $notes = strip_tags($po['notes'], '<br>');
                $notes = html_entity_decode($po['notes']);
                $html .= '
                    <p><strong>The reason for not following tender/pitching process:</strong><br>' . $notes . '</p>
                ';

                if (!empty($po['attachment'])) {
                    if (strpos($po['attachment_type'], 'image/') === 0) {
                        $imagePath1 = FCPATH . 'uploads/purchase_orders/' . $po['attachment'];
                        if (file_exists($imagePath1)) {
                            $imageData1 = base64_encode(file_get_contents($imagePath1));
                            $html .= '
                                <div class="mb-3">
                                    <img src="@' . $imageData1 . '" 
                                        style="width: 300px;" 
                                        alt="Attachment 1">
                                </div>
                            ';
                        }
                    } else {
                        $html .= '
                            <div class="mb-3">
                                <a href="' . base_url('uploads/purchase_orders/' . $po['attachment']) . '" 
                                   class="btn btn-sm btn-primary" 
                                   target="_blank">
                                    <i class="bi bi-file-earmark"></i> 
                                    ' . base_url('uploads/purchase_orders/' . $po['attachment']) . '
                                </a>
                            </div>
                        ';
                    }
                }
            }
            
            if (!empty($po['notes2'])) {
                // $notes2 = strip_tags($po['notes2'], '<br>');
                $notes2 = html_entity_decode($po['notes2']);
                $html .= '
                    <p><strong>Impact to DANA as (Organization) if we are failed to perform the agreement as soon as possible:</strong><br>' . $notes2 . '</p>
                ';

                if (!empty($po['attachment2'])) {
                    if (strpos($po['attachment_type2'], 'image/') === 0) {
                        $imagePath2 = FCPATH . 'uploads/purchase_orders/' . $po['attachment2'];
                        if (file_exists($imagePath2)) {
                            $imageData2 = base64_encode(file_get_contents($imagePath2));
                            $html .= '
                                <div class="mb-3">
                                    <img src="@' . $imageData2 . '" 
                                        style="width: 300px;" 
                                        alt="Attachment 2">
                                </div>
                            ';
                        }
                    } else {
                        $html .= '
                            <div class="mb-3">
                                <a href="' . base_url('uploads/purchase_orders/' . $po['attachment2']) . '" 
                                   class="btn btn-sm btn-primary" 
                                   target="_blank">
                                    <i class="bi bi-file-earmark"></i> 
                                    ' . base_url('uploads/purchase_orders/' . $po['attachment2']) . '
                                </a>
                            </div>
                        ';
                    }
                }
            }

            $html .= '
            <p><strong>Due date for the ordering confirmation:</strong> ' . date('d M Y', strtotime($po['due_date'])) . '</p>
            <p><strong>Cost to proceed the order:</strong> ' . $po['currency'] . '' . number_format($po['total_amount'], 2) . '</p>
            <p><em><strong>Requestor Statement:</strong><br>Hereby I declare that this request to justified the urgency, without any conflict of interest to the selected vendor.</em></p>
            ';

            $html .= '
            <table style="margin-top: 30px;">
                <tr>
                    <td class="label" style="border: 1px solid #ddd; height: 80px; width:25%; vertical-align: top;"><strong>Requestor:</strong>
                        <br><br>
                        <div>' . esc($requestor['name']) . '<br>Date: ' . date('d M Y', strtotime($po['created_at'])) . '</div>
                    </td>
                    <td class="label" style="border: 1px solid #ddd; height: 80px; width:25%; vertical-align: top;"><strong>Line Manager:</strong>
                        <br><br>
                        <div>' . esc($manager['name']) . '<br>Date: ' . date('d M Y', strtotime($po['approved_manager_at'])) . '</div>
                    </td>
                    <td class="label" style="border: 1px solid #ddd; height: 80px; width:25%; vertical-align: top;"><strong>Excom I:</strong>
                        <br><br>
                        <div>' . esc($management['name']) . '<br>Date: ' . date('d M Y', strtotime($po['approved_management_at'])) . '</div>
                    </td>
                    <td class="label" style="border: 1px solid #ddd; height: 80px; width:25%; vertical-align: top;"><strong>Excom II:</strong>
                        <br><br>
                        <div>' . session()->get('name') . '<br>Date: ' . date('d M Y') . '</div>
                    </td>
                </tr>
            </table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            
            $filename = 'PO_' . $po['id'] . '.pdf';
            $filepath = FCPATH . 'uploads/purchase_orders/' . $filename;
            
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0777, true);
            }
            
            $pdf->Output($filepath, 'F');

            $this->sendCompletedNotification($po, $requestor);

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to complete purchase');
            }

            return redirect()->to('purchase-orders/view/' . $id)
                ->with('success', 'Purchase has been completed successfully');

        } catch (\Exception $e) {
            log_message('error', '[PurchaseOrder] Complete failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete purchase: ' . $e->getMessage());
        }
    }

    // public function generatePdf($id = null)
    // {
    //     if ($id === null) {
    //         return redirect()->to('dashboard')
    //             ->with('error', 'Invalid Purchase ID');
    //     }

    //     $po = $this->purchaseOrderModel->select('
    //         purchase_orders.*,
    //         users.name as requestor_name,
    //         users.email as requestor_email
    //     ')
    //     ->join('users', 'users.id = purchase_orders.requestor_id')
    //     ->find($id);
        
    //     if (!$po) {
    //         return redirect()->to('dashboard')
    //             ->with('error', 'Purchase not found');
    //     }

    //     // if ($po['requestor_id'] !== session()->get('id')) {
    //     //     return redirect()->to('dashboard')
    //     //         ->with('error', 'You are not authorized to view this Purchase');
    //     // }

    //     $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
        
    //     $options = $dompdf->getOptions();
    //     $options->set('isPhpEnabled', true);
    //     $options->set('isRemoteEnabled', true);
    //     $dompdf->setOptions($options);

    //     $po['manager_name'] = $this->getApproverName($po['approved_by_manager']);
    //     $po['executive_name'] = $this->getApproverName($po['approved_by_executive']);

    //     $data = [
    //         'po' => $po,
    //         'status_badge_class' => $this->getStatusBadgeClass($po['status'])
    //     ];

    //     $html = view('purchase_orders/pdf_template', $data);
    //     $dompdf->loadHtml($html);

    //     $dompdf->setPaper('A4', 'portrait');

    //     $dompdf->render();

    //     $filename = 'PO_' . $po['po_number'] . '_' . date('Ymd') . '.pdf';

    //     $dompdf->stream($filename, ['Attachment' => true]);
    //     exit();
    // }

    private function sendApprovalNotification($po, $requestor)
    {
        $email = \Config\Services::email();

        $userModel = new UserModel();
        $procurementUsers = $userModel->where('role', 'procurement')->findAll();
        $arrayEmail = array_column($procurementUsers, 'email');
        
        $email->setFrom('teguh.arifudin@gmail.com');
        $email->setTo($requestor['email']);
        $email->setCC($arrayEmail);
        $email->setSubject('Purchase Approved - ' . $po['po_number']);
        
        $message = "
            <html>
            <head>
                <title>Purchase Approval Notification</title>
            </head>
            <body>
                <h2>Purchase Approval Notice</h2>
                <p>Dear " . htmlspecialchars($requestor['name']) . ",</p>
                <p>Your purchase <strong>" . htmlspecialchars($po['title'] ?? 'Untitled') . "</strong> " . 
                (!empty($po['po_number']) ? "/ <strong>" . htmlspecialchars($po['po_number']) . "</strong>" : "") . 
                " has been approved.</p>
                <p>Please find the approved purchase document attached.</p>
                <p>Best regards,<br>Purchase Team</p>
            </body>
            </html>
        ";
        
        $email->setMessage($message);

        $email->setMailType('html');

        $attachment_path = FCPATH . 'uploads/purchase_orders/PO_' . $po['id'] . '.pdf';
        if (file_exists($attachment_path)) {
            $email->attach($attachment_path);
        }
        
        try {
            if (!$email->send()) {
                log_message('error', 'Failed to send PO approval email: ' . $email->printDebugger(['headers']));
                return false;
            }
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Email sending exception: ' . $e->getMessage());
            return false;
        }
    }

    private function sendRejectionNotification($po, $requestor)
    {
        $email = \Config\Services::email();

        $userModel = new UserModel();
        $procurementUsers = $userModel->where('role', 'procurement')->findAll();
        $arrayEmail = array_column($procurementUsers, 'email');
        
        $email->setFrom('teguh.arifudin@gmail.com');
        $email->setTo($requestor['email']);
        $email->setCC($arrayEmail);
        $email->setSubject('Purchase Rejected - ' . $po['po_number']);
        
        $message = "
            <html>
            <head>
                <title>Purchase Rejected Notification</title>
            </head>
            <body>
                <h2>Purchase Rejected Notice</h2>
                <p>Dear " . htmlspecialchars($requestor['name']) . ",</p>
                <p>Your purchase <strong>" . htmlspecialchars($po['title'] ?? 'Untitled') . "</strong> " . 
                (!empty($po['po_number']) ? "/ <strong>" . htmlspecialchars($po['po_number']) . "</strong>" : "") . 
                " has been rejected.</p>
                <p>Best regards,<br>Purchase Team</p>
            </body>
            </html>
        ";
        
        $email->setMessage($message);

        $email->setMailType('html');

        try {
            if (!$email->send()) {
                log_message('error', 'Failed to send PO rejected email: ' . $email->printDebugger(['headers']));
                return false;
            }
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Email sending exception: ' . $e->getMessage());
            return false;
        }
    }

    private function sendCompletedNotification($po, $requestor)
    {
        $email = \Config\Services::email();

        $userModel = new UserModel();
        $procurementUsers = $userModel->where('role', 'procurement')->findAll();
        $arrayEmail = array_column($procurementUsers, 'email');
        
        $email->setFrom('teguh.arifudin@gmail.com');
        $email->setTo($requestor['email']);
        $email->setCC($arrayEmail);
        $email->setSubject('Purchase Completed - ' . $po['po_number']);
        
        $message = "
            <html>
            <head>
                <title>Purchase Completed Notification</title>
            </head>
            <body>
                <h2>Purchase Completed Notice</h2>
                <p>Dear " . htmlspecialchars($requestor['name']) . ",</p>
                <p>Your purchase <strong>" . htmlspecialchars($po['title'] ?? 'Untitled') . "</strong> " . 
                (!empty($po['po_number']) ? "/ <strong>" . htmlspecialchars($po['po_number']) . "</strong>" : "") . 
                " has been completed.</p>
                <p>Please find the completed purchase document attached.</p>
                <p>Best regards,<br>Purchase Team</p>
            </body>
            </html>
        ";
        
        $email->setMessage($message);

        $email->setMailType('html');

        $attachment_path = FCPATH . 'uploads/purchase_orders/PO_' . $po['id'] . '.pdf';
        if (file_exists($attachment_path)) {
            $email->attach($attachment_path);
        }
        
        try {
            if (!$email->send()) {
                log_message('error', 'Failed to send PO approval email: ' . $email->printDebugger(['headers']));
                return false;
            }
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Email sending exception: ' . $e->getMessage());
            return false;
        }
    }

    // public function uploadImage()
    // {
    //     helper(['form', 'filesystem']);
        
    //     try {
    //         $file = $this->request->getFile('upload');
            
    //         if (!$file || !$file->isValid()) {
    //             throw new \RuntimeException('Invalid file upload');
    //         }
    
    //         $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    //         $fileType = $file->getMimeType();
            
    //         if (!in_array($fileType, $allowedTypes)) {
    //             throw new \RuntimeException('Invalid file type. Only JPG, PNG and GIF are allowed.');
    //         }
    
    //         if ($file->getSizeByUnit('mb') > 2) {
    //             throw new \RuntimeException('File size exceeds 2MB limit');
    //         }
    
    //         $newName = $file->getRandomName();
            
    //         $yearMonth = date('Y/m');
    //         $uploadPath = FCPATH . 'uploads/images/' . $yearMonth;
            
    //         if (!file_exists($uploadPath)) {
    //             mkdir($uploadPath, 0777, true);
    //         }
    
    //         if (!$file->move($uploadPath, $newName)) {
    //             throw new \RuntimeException('Failed to move uploaded file');
    //         }
    
    //         $fileURL = base_url("uploads/images/{$yearMonth}/{$newName}");
    
    //         return $this->response->setJSON([
    //             'uploaded' => 1,
    //             'fileName' => $newName,
    //             'url' => $fileURL
    //         ]);
    
    //     } catch (\Exception $e) {
    //         log_message('error', '[Upload Error] ' . $e->getMessage());
            
    //         return $this->response->setJSON([
    //             'uploaded' => 0,
    //             'error' => ['message' => $e->getMessage()]
    //         ]);
    //     }
    // }
}
