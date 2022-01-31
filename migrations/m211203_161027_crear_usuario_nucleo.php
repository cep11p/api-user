<?php

use dektrium\user\models\User;
use yii\db\Migration;

class m211203_161027_crear_usuario_nucleo extends Migration
{
    public function up()
    {
        $this->insert('user', [
            'id' => '5',
            'username' => 'nucleo',
            'email' => 'nucleo@correo.com',
            'password_hash' => '$2y$10$qg22YgDd9rHlBAyNwrTPHurEWM4YXN9zszKKKCRonTf19nu9u.B7C',
            'auth_key' => '935GiomUIU5AYzDezV98MeC4moQpdB9t',
            'created_at' => '1638793919',
            'updated_at' => '1638793919',
        ]);

        $this->insert('user_persona', [
            'userid' => '5',
            'personaid' => 0,
            'localidadid' => 0
        ]);
    }

    public function down()
    {
        echo "m211203_161027_crear_usuario_nucleo cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
