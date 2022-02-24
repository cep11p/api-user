<?php

use yii\db\Migration;

/**
 * Class m220221_152544_new_table_modulo
 */
class m220221_152544_new_table_modulo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = 'modulo';
        $this->createTable($table, [
            'id' => $this->primaryKey(),
            'nombre' => $this->string()->notNull()->unique(),
            'servicio' => $this->string()->notNull(),
            'sigla' => $this->string(5)->unique(),
            'componente' => $this->string(),
        ]);

        $this->insert($table, [
            'nombre' => 'Registral',
            'sigla' => 'REG',
            'servicio' => 'registral'
        ]);

        $this->insert($table, [
            'nombre' => 'Lugar',
            'sigla' => 'LUG',
            'servicio' => 'lugar'
        ]);

        $this->insert($table, [
            'nombre' => 'Gestor Cuentas Bancarias',
            'sigla' => 'GCB',
            'servicio' => 'gcb'
        ]);

        $this->insert($table, [
            'nombre' => 'Inventario',
            'sigla' => 'INV',
            'servicio' => 'inventario'
        ]);

        $this->insert($table, [
            'nombre' => 'Nucleo',
            'sigla' => 'NUC',
            'servicio' => 'nucleo'
        ]);

        $this->insert($table, [
            'nombre' => 'Pril',
            'sigla' => 'PRIL',
            'servicio' => 'pril'
        ]);

        $this->insert($table, [
            'nombre' => 'Prestaciones Sociales',
            'sigla' => 'GPS',
            'servicio' => 'recurso-social'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220221_152544_new_table_modulo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220221_152544_new_table_modulo cannot be reverted.\n";

        return false;
    }
    */
}
