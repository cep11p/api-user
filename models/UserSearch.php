<?php

namespace app\models;

use app\components\VinculoInteroperableHelp;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use DateTime;

/**
* UserSearch represents the model behind the search form about `app\models\User`.
*/
class UserSearch extends User
{
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['id', 'confirmed_at', 'blocked_at', 'created_at', 'updated_at', 'flags', 'last_login_at'], 'integer'],
            [['username', 'email', 'password_hash', 'auth_key', 'unconfirmed_email', 'registration_ip'], 'safe'],
        ];
    }

    /**
    * @inheritdoc
    */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
    * Creates data provider instance with search query applied
    *
    * @param array $params
    *
    * @return ActiveDataProvider
    */
    public function search($params)
    {
        $query = User::find();
        $localidadid = (isset($params['localidadid']) && !empty($params['localidadid']))?$params['localidadid']:'';

        $pagesize = (!isset($params['pagesize']) || !is_numeric($params['pagesize']) || $params['pagesize']==0)?20:intval($params['pagesize']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pagesize,
                'page' => (isset($params['page']) && is_numeric($params['page']))?$params['page']:0
            ],
        ]);

        $this->load($params,'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        #global search #global param
        $personaForm = new PersonaForm();
        if(isset($params['global_param']) && !empty($params['global_param'])){
            $persona_params["global_param"] = $params['global_param'];
        }
                
        $lista_personaid = array();
        if (isset($persona_params)) {
            #Filtramos persona
            $coleccion_persona = $personaForm->buscarPersonaEnRegistral($persona_params);
            $lista_personaid = VinculoInteroperableHelp::obtenerListaIds($coleccion_persona,'id');
            if (count($lista_personaid) < 1) {
                $query->where('0=1');
            }
        }
         #Criterio para buscar por persona
         if(count($lista_personaid)>0){
            $query->andWhere(array('in', 'user_persona.personaid', $lista_personaid));
        }

        #Nombre de usuario
        if(isset($params['global_param']) && !empty($params['global_param'])){
            $query->orFilterWhere(['or',['like','username',$params['global_param']]]);
        }
               
        $query->andFilterWhere(['like', 'username', $this->username]);
        $query->andFilterWhere(['user_persona.localidadid'=>$localidadid]);

        #### Filtro por rango de fecha (create_at)####
        if(isset($params['fecha_ingreso_desde']) && isset($params['fecha_ingreso_hasta'])){
            $query->andWhere(['between', 'last_login_at', DateTime::createFromFormat('Y-m-d', $params['fecha_ingreso_desde'])->getTimestamp(), DateTime::createFromFormat('Y-m-d', $params['fecha_ingreso_hasta'])->getTimestamp()]);
        }else if(isset($params['fecha_ingreso_desde'])){
            $query->andWhere(['between', 'last_login_at', DateTime::createFromFormat('Y-m-d', $params['fecha_ingreso_desde'])->getTimestamp(), DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->getTimestamp()]);
        }else if(isset($params['fecha_ingreso_hasta'])){
            $query->andWhere(['between', 'last_login_at', '1970-01-01',  DateTime::createFromFormat('Y-m-d', $params['fecha_ingreso_hasta'])->getTimestamp()]);
        }
        
        $coleccion= array();
        foreach ($dataProvider->getModels() as $value) {
            $coleccion[] = $value->toArray();
        }

        $coleccion = VinculoInteroperableHelp::vincularDatosPersona($coleccion,['apellido','nombre','nro_documento','cuil']);
        
        $paginas = ceil($dataProvider->totalCount/$pagesize);           
        $data['pagesize']=$pagesize;            
        $data['pages']=$paginas;            
        $data['total_filtrado']=$dataProvider->totalCount;
        $data['resultado']=$coleccion;
        
        return $data;
    }
}