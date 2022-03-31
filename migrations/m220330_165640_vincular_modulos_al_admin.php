<?php

use yii\db\Migration;

/**
 * Class m220330_165640_vincular_modulos_al_admin
 */
class m220330_165640_vincular_modulos_al_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        #vinculamos los modulos exitentes al admin
        $this->insert('usuario_modulo',['userid' => 1, 'moduloid' => 3]);
        $this->insert('usuario_modulo',['userid' => 1, 'moduloid' => 5]);
        $this->insert('usuario_modulo',['userid' => 1, 'moduloid' => 7]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220330_165640_vincular_modulos_al_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_165640_vincular_modulos_al_admin cannot be reverted.\n";

        return false;
    }
    */
}
