<?php

use yii\db\Migration;

/**
 * Class m220330_165429_borrar_modulos_innecesarios
 */
class m220330_165429_borrar_modulos_innecesarios extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('modulo',['id' => [1,2,6]]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220330_165429_borrar_modulos_innecesarios cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_165429_borrar_modulos_innecesarios cannot be reverted.\n";

        return false;
    }
    */
}
