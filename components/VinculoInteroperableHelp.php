<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\helpers\ArrayHelper;
use app\models\LugarForm;
use app\models\PersonaForm;

class VinculoInteroperableHelp extends \yii\base\Component{

    /**
     * Se obtiene una lista de ids de alguna coleccion
     * @param array $coleccion lista de personas
     * @return array
     */
    static function obtenerListaIds($coleccion = array(),$key = '') {
        
        $lista_ids = array();
        foreach ($coleccion as $col) {
            $lista_ids[] = $col[$key];
        }
        
        return $lista_ids;    
    }
    
    /**
     * Interopera con el sistema registral para obtener datos de persona y vincularlas
     *
     * @param array $lista Lista de entidades que tiene como atributo personaid
     * @param array $atributos coleccion de atributos a vincular
     * @param String $campoNombre nombre del campo donde se vincularan todos o algunos atributos
     * @return void
     */
    static function vincularDatosPersona($lista = [], $atributos = [], $campoNombre = '') {
        #Obtenemos los datos a vincular
        $ids='';
        $personaForm = new PersonaForm();
        foreach ($lista as $ent) {
            $ids .= (empty($ids))?$ent['personaid']:','.$ent['personaid'];
        }
        
        $coleccionPersona = $personaForm->buscarPersonaEnRegistral(array("ids"=>$ids,"pagesize"=>count($lista)));

        #Vinculamos los datos
        $i=0;
        foreach ($lista as $ent) {
            foreach ($coleccionPersona as $persona) {
                if(isset($ent['personaid']) && isset($persona['id']) && $ent['personaid']==$persona['id']){        
                    if(count($atributos)>0){
                        foreach ($atributos as $value) {
                            #si camponombre exite le metemos cada atributo sino lo metemos en la raiz del array
                            ($campoNombre != '') ? $lista[$i][$campoNombre][$value] = $persona[$value] : $lista[$i][$value] = $persona[$value];
                        }
                    }else{
                        ($campoNombre != '') ? $lista[$i][$campoNombre] = $persona : $lista[$i] = ArrayHelper::merge($lista[$i], $persona);
                    }
                }
            }
            $i++;
        }

        return $lista;
    }

    /**
     * Interopera con el sistema lugar para obtener los datos de localidad y vincularla
     *
     * @param array $lista
     * @return void
     */
    static function vincularDatosLocalidad($lista = []) {
        $campoid = 'localidadid';
        #Obtenemos los datos a vincular

        $ids='';
        $pagesize = count($lista); 
        foreach ($lista as $valor) {
            #si esta seteada la localidad
            if(isset($valor[$campoid])){
                $ids .= (empty($ids))?$valor[$campoid]:','.$valor[$campoid];
            }
        }
        
        $coleccion = [];
        if(isset($lista)){
            $response = \Yii::$app->lugar->buscarLocalidad(array("ids"=>$ids,"pagesize"=>$pagesize));   
        }

        if(isset($response['resultado']) && count($response['resultado'])>0){
            $coleccion = $response['resultado'];
        }
        
        #Vinculamos los datos
        $i=0;
        foreach ($lista as $ent) {
            foreach ($coleccion as $col) {
                if(isset($ent[$campoid]) && isset($col['id']) && $ent[$campoid]==$col['id']){        
                    $lista[$i]['localidad'] = $col['nombre'];
                }
            }
            $i++;
        }
        return $lista;
    }

    /**
     * Se vinculan las personas a los recursos
     * @param type $coleccion_recurso
     * @param type $coleccion_personaid
     * @return array
     */
    public function vincularPersona($coleccion_recurso = array(), $coleccion_personaid = array()) {
        $i=0;
        foreach ($coleccion_recurso as $recurso) {
            foreach ($coleccion_personaid as $persona) {
                if(isset($recurso['personaid']) && isset($persona['id']) && $recurso['personaid']==$persona['id']){                    
                    $recurso['persona'] = $persona;
                    $coleccion_recurso[$i] = $recurso;
                }
            }
            $i++;
        }
        
        return $coleccion_recurso;
    }
    
    /**
     * Se obtienen las personas que están vinculadas a la lista de recursos
     * @param array $coleccion_recursos
     * @return array
     */
    public function obtenerPersonaVinculada($coleccion_recursos = array()) {
        $personaForm = new PersonaForm();
        $ids='';
        $pagesize = count($coleccion_recursos); 
        foreach ($coleccion_recursos as $recursos) {
            $ids .= (empty($ids))?$recursos['personaid']:','.$recursos['personaid'];
        }
        
        $coleccionPersona = $personaForm->buscarPersonaEnRegistral(array("ids"=>$ids,"pagesize"=>$pagesize));
        
        return $coleccionPersona;
    }
    
    /**
     * Se vinculan los nombres de la localidades a una coleccion que tenga localidadid
     * @param array $coleccion
     * @param array $coleccion_localidad
     * @return array
     */
    static function vincularLocalidad($coleccion = array(), $coleccion_localidad = array()) {
        $i=0;
        foreach ($coleccion as $valor) {
            foreach ($coleccion_localidad as $localidad) {
                if(isset($valor['localidadid']) && isset($localidad['id']) && $valor['localidadid']==$localidad['id']){                    
                    $valor['localidad'] = $localidad['nombre'];
                    $coleccion[$i] = $valor;
                }
            }
            $i++;
        }
        
        return $coleccion;
    }
    
    /**
     * Se obtienen los id de localidades que están vinculadas a la coleccion
     * @param array $coleccion
     * @return array
     */
    static function obtenerLocalidadIdDeColeccion($coleccion = array()) {
        $lugarForm = new LugarForm();
        $ids='';
        $pagesize = count($coleccion); 
        foreach ($coleccion as $valor) {
            #si esta seteada la localidad
            if(isset($valor['localidadid'])){
                $ids .= (empty($ids))?$valor['localidadid']:','.$valor['localidadid'];
            }
            
        }
        
        $coleccion = $lugarForm->buscarLocalidadEnSistemaLugar(array("ids"=>$ids,"pagesize"=>$pagesize));
        
        return $coleccion;
    }
    
    /**
     * Se vincular los datos de lugar asociado a una prestacion
     * @param array $coleccion que tiene vinculo con datos de lugar
     * @return array
     */
    static function obtenerYVincularParametrosDeLugar($coleccion = array()) {
        
        #Hacemos el pedido de los parametros
        $parametrosLugar = \Yii::$app->lugar->obtenerParametro();
        
        $i = 0;
        foreach ($coleccion as $prestacion) {
            
            if(isset($prestacion['localidadid']) && !empty($prestacion['localidadid'])){
                foreach ($parametrosLugar['localidad'] as $value) {
                    if($prestacion['localidadid'] == $value['id']){
                        $prestacion['localidad'] = $value['nombre'];
                    }
                }
            }
            
            if(isset($prestacion['responsable_entrega_data']['tipo_responsableid']) && !empty($prestacion['responsable_entrega_data']['tipo_responsableid'])){
                switch ($prestacion['responsable_entrega_data']['tipo_responsableid']) {
                    case \app\models\Recurso::TIPO_RESPONSABLE_DELEGACION :
                        foreach ($parametrosLugar['delegacion'] as $value) {
                            if($prestacion['responsable_entrega_data']['responsable_entregaid'] == $value['id']){
                                $prestacion['responsable_entrega'] = $value['nombre'] .' (Delegación)';
                            }
                        }
                        break;
                    case \app\models\Recurso::TIPO_RESPONSABLE_MUNICIPIO :
                        foreach ($parametrosLugar['municipio'] as $value) {
                            if($prestacion['responsable_entrega_data']['responsable_entregaid'] == $value['id']){
                                $prestacion['responsable_entrega'] = $value['nombre'] .' (Municipio)';
                            }
                        }
                        break;
                    case \app\models\Recurso::TIPO_RESPONSABLE_COMISION_FOMENTO :
                        foreach ($parametrosLugar['comision_fomento'] as $value) {
                            if($prestacion['responsable_entrega_data']['responsable_entregaid'] == $value['id']){
                                $prestacion['responsable_entrega'] = $value['nombre'] .' (Comision de Fomento)';
                            }
                        }
                        break;
                }
            }
            $coleccion[$i] = $prestacion;
            $i++;
        }
        
        return $coleccion;
    }
}