<?php

namespace app\modules\api\controllers;

use app\components\VinculoInteroperableHelp;
use app\models\User;
use app\models\UserPersona;
use yii\rest\ActiveController;
use Yii;
use yii\web\Response;
use dektrium\user\Finder;
use dektrium\user\helpers\Password;
use dektrium\user\Module;
use yii\helpers\ArrayHelper;

class UsuarioController extends ActiveController
{
    public $modelClass = 'app\models\ApiUser';
    
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }
    
    
    public function behaviors()
    {

        $behaviors = parent::behaviors();     

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className()
        ];

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        $behaviors['authenticator'] = $auth;

        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::className(),
        ];

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = [
            'options',
            'login',
        ];     

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'only' => ['*'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login'],
                    'roles' => ['?'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index','create','update','view','buscar-persona-por-cuil','check-user','baja','asignar-modulo','desasignar-modulo','delete'],
                    'roles' => ['@'],
                ]
            ]
        ];



        return $behaviors;
    }
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['view']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider() 
    {
        $searchModel = new \app\models\UserSearch();
        $params = \Yii::$app->request->queryParams;
        $resultado = $searchModel->search($params);

        $resultado['resultado'] = VinculoInteroperableHelp::vincularDatosLocalidad($resultado['resultado']);
        $resultado['resultado'] = VinculoInteroperableHelp::vincularDatosPersona($resultado['resultado'],['nombre','apellido','nro_documento','cuil']);

        return $resultado;
    }
    
    /**
     * Login action.
     *
     * @return Response|array
     */
    public function actionLogin()
    {
        $parametros = Yii::$app->getRequest()->getBodyParams();
        
        #Intancia de ActiveRecord
        $usuario = $this->finder->findUserByUsernameOrEmail($parametros['username']);       
        
        if(!($usuario !== null && Password::validate($parametros['password_hash'],$usuario->password_hash))){
            throw new \yii\web\HttpException(401, 'usuario o contraseña inválido');
        }

        #instanciamos nuestro 
        $usuario = User::findOne(['id' => $usuario->id]);
        
        #Buscamos la tabla relacional user_persona
        $userPersona = UserPersona::findOne(['userid'=>$usuario->id]);
        
        #Chequeamos si exite el userpersona
        if($userPersona == null){
            throw new \yii\web\HttpException(401, 'El usuario '.$usuario->id.' tiene una inconsitencia con la tabla user_persona');
        }
        
        #Validamos si el usuario esta habilitado
        if($userPersona->fecha_baja != null){
            throw new \yii\web\HttpException(401, 'El usuario se encuentra inhabilitado');
        }
        
        #Registramos el horario de ingreso
        $usuario->last_login_at = time();
        $usuario->save();
        
        #Registramos la ip de ingreso
        $userPersona->last_login_ip = Yii::$app->getRequest()->getUserIP();
        $userPersona->save();

        $lista_modulo = $usuario->modulos;

        #Generamos el Token
        $payload = [
            'exp' => time()+3600*8,
            'usuario' => $usuario->username,
            'uid' =>  $usuario->id
        ];
        $token = \Firebase\JWT\JWT::encode($payload, \Yii::$app->params['JWT_SECRET']);
        
        #Seteamos principales datos del resultado
        $resultado = [
            'access_token' => $token,
            'username' => $usuario->username,
            'lista_modulo' => $lista_modulo,
        ];
        
        #Si es diferente de admin
        if($userPersona->personaid != 0){
            $resultado = ArrayHelper::merge($userPersona->persona, $resultado);
        }

        return $resultado;
    }
    
    /**
     * Se registra un usuario con personaid y localidadid
     *
     * @return void
     */
    public function actionCreate(){
        $resultado['message']='Se crea un usuario';
        $params = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        try {
       
            $resultado['data']['id'] = User::registrarUsuario($params);

            $transaction->commit();
            return $resultado;
        }catch (\yii\web\HttpException $exc) {
            $transaction->rollBack();
            $mensaje =$exc->getMessage();
            $statuCode =$exc->statusCode;
            throw new \yii\web\HttpException($statuCode, $mensaje);
        }
    }

    public function actionUpdate($id){
        $resultado['message']='Se modifica un usuario';
        $params = Yii::$app->request->post();

        $model = User::findOne(['id'=>$id]);            
        if($model==NULL){
            throw new \yii\web\HttpException(400, 'El usuario con el id '.$id.' no existe!');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
       
            $resultado['data']['id'] = $model->modificarUsuario($params);

            $transaction->commit();
            return $resultado;
        }catch (\yii\web\HttpException $exc) {
            $transaction->rollBack();
            $mensaje =$exc->getMessage();
            $statuCode =$exc->statusCode;
            throw new \yii\web\HttpException($statuCode, $mensaje);
        }
    }

    public function actionView($id){
        $model = User::findOne(['id'=>$id]);            
        if($model==NULL){
            throw new \yii\web\HttpException(400, 'El usuario con el id '.$id.' no existe!');
        }
        $resultado = ArrayHelper::merge($model->toArray(),$model->userPersona->persona);
        $resultado['localidad'] = $model->userPersona->localidad;
        
        return $resultado;
    }

    /**
     * Se chequea el estado del usuario para realizar la consulta actual
     *
     * @param [int] $id
     * @return user
     */
    public function actionCheckUser(){
        $params = Yii::$app->request->post();
        if(!isset($params['userid']) || empty($params['userid'])){
            throw new \yii\web\HttpException(400, 'Falta el id del usuario');
        }
        $model = User::findOne(['id'=>$params['userid']]);

        if($model==NULL){
            throw new \yii\web\HttpException(400, 'El usuario con el id '.$params['userid'].' no existe!');
        }
        $resultado = $model->checkUser($params);
        
        return $resultado;
    }

    /**
     * Esta funcionalidad realiza la busqueda de una persona, si la persona tiene un usuario le vinculamos el usuario, 
     * sino tiene un usuario solo se devolvera la persona, en todo caso si no se encuenta ninguna 
     * de las dos cosas se devuelve success=false
     *
     * @param [int] $cuil
     * @return array
     */
    public function actionBuscarPersonaPorCuil($cuil){

        $data = User::buscarPersonaPorCuil($cuil);
        if($data!=false){
            $resultado['success'] = true;
            $resultado['resultado'] = $data;
        }else{
            $resultado['success'] = false;
        }        

        return $resultado;
    }

    /**
     * Esta funcion habilita y deshabilita un usuario
     *
     * @param [int] $id
     * @return void
     */
    public function actionBaja($id){
        $params = Yii::$app->request->post();

        $model = User::findOne(['id'=>$id]);            
        if($model==NULL){
            throw new \yii\web\HttpException(400, 'El usuario con el id '.$id.' no existe!');
        }
        
        if($params['baja']===true){
            $resultado['message'] = 'Se inhabilita el usuario correctamente.';
            if(!$model->setBaja($params)){
                $resultado['message'] = 'No se pudo inhabilitar el usuario correctamente';
            }
        }else if($params['baja']===false){
            $resultado['message'] = 'Se Habilita el usuario correctamente.';
            if(!$model->unSetBaja($params)){
                $resultado['message'] = 'No se pudo habilitar el usuario correctamente';
            }
        }
        
        return $resultado;
    }

    /**
     * Se vincula un modulo con un usuario
     *
     * @return void
     */
    public function actionAsignarModulo(){
        $params = Yii::$app->request->post();
        $resultado['success'] = false;
        if(User::setAsignacion($params)){
            $resultado['success'] = true;
            $resultado['mensaje'] = 'Asignaciones guardadas exitosamente!';
        }

        return $resultado;
    }

    /**
     * Se asignan permisos por programa a un usuario
     *
     * @return void
     */
    public function actionDesasignarModulo(){
        $params = Yii::$app->request->post();
        $resultado['success'] = false;
        if(User::unsetAsignacion($params)){
            $resultado['success'] = true;
            $resultado['mensaje'] = 'Se borra una asignacion de modulo correctamente!';
        }

        return $resultado;
    }


    /**
     * Listamos todos los permisos asignados a un usuario, Este listado esta agrupado
     *
     * @param [int] $id
     * @return void
     */
    public function actionListarAsignacion($id){
        $model = User::findOne(['id'=>$id]);            
        if($model==NULL){
            throw new \yii\web\HttpException(400, 'El usuario con el id '.$id.' no existe!');
        }
        $resultado = $model->getAsignaciones();

        return $resultado;
    }

    /**
     * Se borran los permisos por programa asignado a un usuario
     *
     * @return void
     */
    public function actionBorrarAsignacion(){
        $params = Yii::$app->request->post();
        $resultado['success'] = false;
        if(User::borrarAsignaciones($params)){
            $resultado['success'] = true;
            $resultado['mensaje'] = 'Se borraron asignaciones correctamente!';
        }

        return $resultado;
    }


}
