<?php

use yii\db\Migration;

/**
 * Class m220221_162705_usuario_modulo
 */
class m220221_162705_usuario_modulo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = 'usuario_modulo';
        $this->createTable($table, [
            'userid' => $this->integer()->notNull(),
            'moduloid' => $this->integer()->notNull(),
            'create_at' => $this->timestamp()
        ]);

        $this->addPrimaryKey('usuario_modulo_pk', 'usuario_modulo', ['userid','moduloid']);

        $this->addForeignKey('user_fk',$table, 'userid', 'user','id');
        $this->addForeignKey('modulo_fk',$table, 'moduloid', 'modulo','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220221_162705_usuario_modulo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220221_162705_usuario_modulo cannot be reverted.\n";

        return false;
    }
    */
}
