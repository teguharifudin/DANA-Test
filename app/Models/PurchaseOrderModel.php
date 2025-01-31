<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'title',
        'po_number',
        'requestor_id',
        'currency',
        'total_amount',
        'status',
        'notes',
        'attachment',
        'attachment_type',
        'notes2',
        'attachment2',
        'attachment_type2',
        'statement_accepted',
        'approved_by_manager',
        'approved_manager_at',
        'approved_by_management',
        'approved_management_at',
        'approved_by_executive',
        'approved_executive_at',
        'due_date'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    // protected $validationRules = [
    //     'title' => 'required',
    //     'po_number' => 'permit_empty|max_length[50]|is_unique[purchase_orders.po_number,id,{id}]',
    //     'requestor_id' => 'required|numeric',
    //     'currency' => 'required|in_list[IDR,USD]',
    //     'total_amount' => 'required|numeric|greater_than[0]',
    //     'status' => 'required|in_list[draft,pending,approved,rejected,management,completed]',
    //     'due_date' => 'required'
    // ];

    // protected $validationMessages = [
    //     'title' => [
    //         'required' => 'Title is required'
    //     ],
    //     'po_number' => [
    //         'max_length' => 'PR Number cannot exceed 50 characters',
    //         'is_unique' => 'PO Number must be unique'
    //     ],
    //     'currency' => [
    //         'required' => 'Currency is required',
    //         'in_list' => 'Invalid currency selected'
    //     ],
    //     'total_amount' => [
    //         'required' => 'Total amount is required',
    //         'numeric' => 'Total amount must be a number',
    //         'greater_than' => 'Total amount must be greater than 0'
    //     ],
    //     'status' => [
    //         'required' => 'Status is required',
    //         'in_list' => 'Invalid status selected'
    //     ],
    //     'due_date' => [
    //         'required' => 'Due date is required'
    //     ]
    // ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
