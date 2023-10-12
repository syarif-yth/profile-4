<?php

namespace Modules\Example\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamplesTable extends Migration
{
    public function up()
    {
        $this->forge->addField('id');
        $this->forge->addField([
            // 'id'            => ['type' => 'varbinary', 'constraint' => 36],
            'active'        => ['type' => 'tinyint', 'constraint' => 1, 'default' => 1],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 255,],
            'slug'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description'   => ['type' => 'TEXT', 'null' => true],
            // more fields
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
        ]);

        // $this->forge->addPrimaryKey('id');
        $this->forge->createTable('examples', true);
    }

    public function down()
    {
        $this->forge->dropTable('examples', true);
    }
}
