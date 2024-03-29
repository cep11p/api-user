<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "modulo".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $servicio
 * @property string $sigla
 * @property string $componente
 *
 * @property \app\models\UsuarioModulo[] $usuarioModulos
 * @property \app\models\User[] $users
 * @property string $aliasModel
 */
abstract class Modulo extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modulo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'servicio'], 'required'],
            [['nombre', 'servicio', 'componente'], 'string', 'max' => 255],
            [['sigla'], 'string', 'max' => 5],
            [['nombre'], 'unique'],
            [['sigla'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'servicio' => 'Servicio',
            'sigla' => 'Sigla',
            'componente' => 'Componente',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioModulos()
    {
        return $this->hasMany(\app\models\UsuarioModulo::className(), ['moduloid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(\app\models\User::className(), ['id' => 'userid'])->viaTable('usuario_modulo', ['moduloid' => 'id']);
    }




}
