<?php

namespace app\models;

use app\components\Help;
use app\components\ServicioInteroperable;
use app\models\ApiUser;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 */
class User extends ApiUser
{    
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            []
        );
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # vinculamos el audit
                'bedezign\yii2\audit\AuditTrailBehavior',
            ]
        );
    }


    /**
     * Se registra un usuario con su rol, perosonaid y localidadid
     *
     * @param [array] $params
     * @return int id
     */
    static function registrarUsuario($params){
        $id = '';
        $user = new Self();
        $user->scenario = 'create';
        $lista_error = [];
        #Chequeamos si la persona tiene usuario
        if(!empty($params['personaid']) && UserPersona::findOne(['personaid'=>$params['personaid']])!=NULL){
            throw new \yii\web\HttpException(400, 'La persona ya tiene un usuario');
        }

        #Chequeamos si el parametro usuario esta seteado
        if(!isset($params['usuario']) || empty($params['usuario'])){
            throw new \yii\web\HttpException(400, 'Falta el campo usuario con sus campos');
        }
        
        #Chequeamos si la contraseña esta vacia
        if(!isset($params['usuario']['password']) || empty($params['usuario']['password'])){
            $user->addError('password','La contraseña no debe estar vacia.');

        }
        
        #Registramos el usuario
        if ( $user->load(['User'=>$params['usuario']]) && $user->create()) {
            $id = $user->id;
        }
        
        #Chequeamos si se puede regitrar el usuario
        if($user->hasErrors() || count($lista_error)>0){
            $lista_error = ArrayHelper::merge($lista_error, $user->errors);
            throw new \yii\web\HttpException(400, json_encode(array($lista_error)));
        }
        
        #Registamos Nueva Persona
        if(!isset($params['usuario']['personaid']) || empty($params['usuario']['personaid'])){
            $params['usuario']['personaid'] = $user->registrarPersona($params);
        }

        #Vinculamos la persona
        $userPersona = new UserPersona();
        $userPersona->setAttributes($params['usuario']);
        $userPersona->userid = $id;

        if(!$userPersona->save()){
            throw new \yii\web\HttpException(400, json_encode(array($userPersona->errors)));
        }

        return $id;
    }

    /**
     * Se registrar los datos personales de un usuario en sistema registral(Interoperabilidad)
     *
     * @param [array] $params
     * @return [int] $personaid
     */
    private function registrarPersona($params){
        $errors = [];
        if(!isset($params['nro_documento']) || empty($params['nro_documento'])){
            $errors['nro_documento'] = ['Se requiere nro de documento']; 
        }
        if(!isset($params['cuil']) || empty($params['cuil'])){
            $errors['cuil'] = ['Se requiere cuil']; 
        }
        if(!isset($params['apellido']) || empty($params['apellido'])){
            $errors['apellido'] = ['Se requiere apellido']; 
        }
        if(!isset($params['nombre']) || empty($params['nombre'])){
            $errors['nombre'] = ['Se requiere nombre']; 
        }

        if(count($errors)>0){
            throw new \yii\web\HttpException(400, json_encode([$errors]));
        }

        $servicioInteroperable = new ServicioInteroperable();
        
        $resultado = $servicioInteroperable->crearRegistro('registral','persona',$params);

        if(!isset($resultado['data']['id'])){
            throw new \yii\web\HttpException(400, 'El servicio registral no esta respondiendo correctamente');
        }
        $personaid = $resultado['data']['id'];

        return $personaid;
    }

    public function modificarUsuario($params){
        $id = '';
        $this->scenario = 'update';

        #Registramos el usuario
        if ( $this->load(['User'=>$params]) && $this->save()) {
            $id = $this->id;
        }

        #Chequeamos si al modificar usuario hay errores
        if($this->hasErrors()){
            throw new \yii\web\HttpException(400, json_encode([$this->errors]));
        }

        #Vinculamos la persona
        $userPersona = UserPersona::findOne(['userid'=>$id]);
        $userPersona->setAttributes($params);

        if(!$userPersona->save()){
            throw new \yii\web\HttpException(400, json_encode([$userPersona->errors]));
        }

        return $id;
    }

    public function setBaja($params)
    {
        $resultado = false;

        $userPersona = UserPersona::findOne(['userid'=>$this->id]);
        if($userPersona==null){
            throw new \yii\web\HttpException(400, 'No se encuentra la integridad del usuario');
        }

        if(strlen($params['descripcion_baja'])<15){
            throw new \yii\web\HttpException(400, json_encode([['descripcion_baja'=>['La descripcion debe tener 10 caracteres como minimo']]]));
        }
        $userPersona->fecha_baja = date('Y-m-d');
        $userPersona->descripcion_baja = $params['descripcion_baja'];

        if($userPersona->save()){
            $resultado = true;
        }

        return $resultado;
    }

    public function unSetBaja($params)
    {
        $resultado = false;

        $userPersona = UserPersona::findOne(['userid'=>$this->id]);
        if($userPersona==null){
            throw new \yii\web\HttpException(400, 'No se encuentra la integridad del usuario');
        }
        
        $userPersona->fecha_baja = null;
        $userPersona->descripcion_baja = null;
        
        if($userPersona->save()){
            $resultado = true;
        }

        return $resultado;
    }


    static function buscarPersonaPorCuil($cuil){
        $resultado = \Yii::$app->registral->buscarPersonaPorCuil($cuil);
                
        if(count($resultado)>0){    
            $data['id'] = $resultado['id'];       
            $data['nro_documento'] = $resultado['nro_documento'];       
            $data['cuil'] = $resultado['cuil'];       
            $data['nombre'] = $resultado['nombre'];       
            $data['apellido'] = $resultado['apellido'];

            $usuarioPersona = UserPersona::findOne(['personaid'=>$resultado['id']]);
            if($usuarioPersona!=null){
                $data['usuario'] = User::findOne(['id'=>$usuarioPersona->userid])->toArray();
                $data['usuario']['personaid'] = $usuarioPersona->personaid;
                $data['usuario']['localidadid'] = $usuarioPersona->localidadid;
                unset($data['usuario']['password_hash']);
            }
            
        }else{
            $data = false;  
        }
        
        return $data;
    }

    /**
     * Se asigna un modulo a un usuario
     *
     * @param [array] $params
     * @return bool
     */
    public static function setAsignacion($params){
        #Chequeamos que venga el modulo
        if(!isset($params['moduloid']) || empty($params['moduloid'])){
            throw new \yii\web\HttpException(400, 'Falta la lista de permisos');
        }

        #Validamos que exista el usuario
        if(User::findOne(['id'=>$params['userid']])==NULL){
            throw new \yii\web\HttpException(400, 'El usuario con el id '.$params['userid'].' no existe!');
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $usuario_modulo = new UsuarioModulo();
            $usuario_modulo->userid = $params['userid'];
            $usuario_modulo->moduloid = $params['moduloid']; 

            if(!$usuario_modulo->save()){
                throw new \yii\web\HttpException(400, Help::ArrayErrorsToString($usuario_modulo->errors));
            }
            
            $transaction->commit();

            return true;
        }catch (\yii\web\HttpException $exc) {
            $transaction->rollBack();
            $mensaje =$exc->getMessage();
            $statuCode =$exc->statusCode;
            throw new \yii\web\HttpException($statuCode, $mensaje);
        }
    }

    /**
     * Se borra la asigacion de un modulo a un usuario
     *
     * @param [array] $params
     * @return bool
     */
    public static function unsetAsignacion($params){
        #Chequeamos que venga el modulo
        if(!isset($params['moduloid']) || empty($params['moduloid'])){
            throw new \yii\web\HttpException(400, 'Falta la lista de permisos');
        }

        #Validamos que exista el usuario
        if(!isset($params['userid']) || empty($params['userid'])){
            throw new \yii\web\HttpException(400, 'Falta el id del usuario.');
        }

        $usuario_modulo = UsuarioModulo::find()->where(['userid' => $params['userid'], 'moduloid' => $params['moduloid']])->one();

        if($usuario_modulo == null){
            throw new \yii\web\HttpException(400, 'No se encuentra la asignacion de modulo para borrar');
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {

            if(!$usuario_modulo->delete()){
                throw new \yii\web\HttpException(400, Help::ArrayErrorsToString($usuario_modulo->errors));
            }
            
            $transaction->commit();

            return true;
        }catch (\yii\web\HttpException $exc) {
            $transaction->rollBack();
            $mensaje =$exc->getMessage();
            $statuCode =$exc->statusCode;
            throw new \yii\web\HttpException($statuCode, $mensaje);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPersona()
    {
        return $this->hasOne(UserPersona::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModulos()
    {
        return UsuarioModulo::find()->select('modulo.*')->leftJoin('modulo','modulo.id = moduloid')->where(['userid' => $this->id])->asArray()->all();
    }

    public function fields()
    {
        $fields = ArrayHelper::merge(parent::fields(), [
            "confirmed_at" => function () {
                return date('Y-m-d',$this->confirmed_at);
            },
            "created_at" => function () {
                return date('Y-m-d',$this->created_at);
            },
            "updated_at" => function () {
                return date('Y-m-d',$this->updated_at);
            },
            "last_login_at" => function () {
                return date('Y-m-d H:i:s',$this->last_login_at);
            },
            "last_login_ip" => function () {
                return $this->userPersona->last_login_ip;
            },
            "personaid" => function () {
                return $this->userPersona->personaid;
            },
            "fecha_baja" => function () {
                return ($this->userPersona->fecha_baja)?$this->userPersona->fecha_baja:'';
            },
            "baja" => function () {
                return ($this->userPersona->fecha_baja)?true:false;
            },
            "descripcion_baja" => function () {
                return ($this->userPersona->descripcion_baja)?$this->userPersona->descripcion_baja:'';
            },
            "localidadid" => function () {
                return $this->userPersona->localidadid;
            },
            "lista_modulo" => function () {
                return $this->modulos;
            }
        ]);

        unset($fields['password_hash'],$fields['auth_key']);

        return $fields;
    }
    
}
