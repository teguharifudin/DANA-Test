<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['requestor', 'procurement', 'manager', 'management', 'executive'],
                'null'       => false,
            ],
            'department' => [
                'type'       => 'ENUM',
                'constraint' => ['Business', 'General', 'Operational'],
                'null'       => true,
            ],
            'division' => [
                'type'       => 'ENUM',
                'constraint' => ['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Sales'],
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id', 'pk_users');
        $this->forge->addUniqueKey('email', 'email_unique');
        $this->forge->createTable('users', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
