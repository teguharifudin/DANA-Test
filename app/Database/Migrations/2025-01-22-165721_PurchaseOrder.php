<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PurchaseOrder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'po_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'requestor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'currency' => [
                'type'       => 'ENUM',
                'constraint' => ['IDR', 'USD'],
                'default'    => 'IDR',
                'null'       => false,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending', 'approved', 'rejected', 'management', 'completed'],
                'default'    => 'draft',
                'null'       => false,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'attachment_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'notes2' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attachment2' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'attachment_type2' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'statement_accepted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Statement acceptance flag',
            ],
            'approved_by_manager' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, 
                'comment'    => 'Manager approval is required',
            ],
            'approved_manager_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'comment' => 'Manager approval timestamp is required',
            ],
            'approved_by_management' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Executive approval is optional',
            ],
            'approved_management_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'comment' => 'Executive approval timestamp is optional',
            ],
            'approved_by_executive' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Executive approval is optional',
            ],
            'approved_executive_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'comment' => 'Executive approval timestamp is optional',
            ],
            'due_date' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'null'    => false,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'null'    => false,
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('po_number'); 
        $this->forge->addKey('requestor_id');
        $this->forge->addKey('status');
        
        $this->forge->createTable('purchase_orders', true);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders', true);
    }
}
