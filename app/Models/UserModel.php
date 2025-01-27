<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['name', 'email', 'password', 'role', 'department', 'division'];

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

    protected $validationRules = [
        'name' => 'required',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[requestor,procurement,manager,management,executive]',
        'department' => 'permit_empty',
        // 'department' => 'permit_empty|required_with[role,manager,requestor,procurement,management]',
        'division' => 'permit_empty'
        // 'division' => 'permit_empty|required_with[role,manager,requestor,procurement]'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Name field is required'
        ],
        'email' => [
            'required' => 'Email field is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Sorry, That email has already been taken. Please choose another.'
        ],
        'password' => [
            'required' => 'Password field is required',
            'min_length' => 'Password must be at least 8 characters long'
        ],
        'role' => [
            'required' => 'Role field is required',
            'in_list' => 'Please select a valid role'
        ],
        // 'department' => [
        //     'required_with' => 'Department is required for this role'
        // ],
        // 'division' => [
        //     'required_with' => 'Division is required for this role'
        // ]
    ];    
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }
}
