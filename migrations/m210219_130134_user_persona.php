<?php

use yii\db\Migration;

/**
 * Class m210219_130134_user_persona
 */
class m210219_130134_user_persona extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = 'user_persona';
        $this->createTable($table,[
            'userid'=>$this->primaryKey(),
            'personaid'=>$this->integer()->notNull(),
            'localidadid'=>$this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_user_persona', $table, 'userid', 'user', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn($table,'fecha_baja',$this->date());
        $this->addColumn($table,'descripcion_baja',$this->string(100));
        $this->addColumn('user_persona', 'last_login_ip', $this->string('20'));
        $this->alterColumn('user_persona', 'descripcion_baja', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210219_130134_user_persona cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210219_130134_user_persona cannot be reverted.\n";

        return false;
    }
    */
}
