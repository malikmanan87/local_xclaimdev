<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewApplicationsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'application_no'  => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'specialist_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'staff_number'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'department'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'position'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'status'          => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'submitted', 'under_review', 'approved', 'rejected'],
                'default'    => 'draft',
            ],
            'submitted_by'    => ['type' => 'INT', 'null' => true],
            'submitted_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('submitted_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('new_applications');
    }

    public function down(): void
    {
        $this->forge->dropTable('new_applications', true);
    }
}
