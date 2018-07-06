<?php

use yii\db\Migration;
use yii\db\Schema;
use syablonskiy\attachfile\ModuleTrait;
/**
 * Handles the creation of table `attachment`.
 */
class m180522_062505_create_attachment_table extends Migration
{
    use ModuleTrait;
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableName = $this->getModule()->tableName;
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable("{{%$tableName}}", [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'model' => Schema::TYPE_STRING,
            'itemId' => Schema::TYPE_INTEGER,
            'hash' => Schema::TYPE_STRING . ' NOT NULL',
            'size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'mime' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex("idx-$tableName-itemId", "{{%$tableName}}", 'itemId');
        
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable($tableName);
    }
}
