<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\helpers\ArrayHelper;
use DateTime;
class Help extends \yii\base\Component{

    /**
     * Agregamos espacion sin importar las letras y/o simbolos raros
     *
     * @return void
     */
    public static function mbstrpad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL){
        $encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
        $padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
        $padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
        $pad_len -= mb_strlen($str, $encoding);
        $targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
        $strToRepeatLen = mb_strlen($pad_str, $encoding);
        $repeatTimes = ceil($targetLen / $strToRepeatLen);
        $repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid utf-8 strings
        $before = $padBefore ? mb_substr($repeatedString, 0, floor($targetLen), $encoding) : '';
        $after = $padAfter ? mb_substr($repeatedString, 0, ceil($targetLen), $encoding) : '';
        return $before . $str . $after;
    }

    public static function ArrayErrorsToString($errors = []){

        $oracion = '';
        foreach ($errors as $key => $value) {
            foreach ($value as $msj) {
                $oracion .= (empty($oracion))?$msj:"\r\n ".$msj;
            }
        }

        return $oracion;
    }

    /**
     * Se convierte un objeto de json a array
     */
    public static function objectJsonToArray($json){
        $errors = json_decode($json);
        $msj = '';
        $resultado = array();
        foreach ($errors as $key => $value) {

            foreach ($value as $val) {
                $msj .= (empty($msj))?$val:' - '.$val;
            }
            $resultado[$key] = $msj;
        }

        return $resultado;
    }
    
    /**
     * Se valida la fechas
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Vamos a extrar un array de una array por las keys seteadas
     * @param array $arrays_colection es la coleccion de arrays que se obtiene para filtrar
     * @param array $array_search array a buscar
     * @return array
     * @author cep11p
     */
    public static function filtrarArrayAsociativo($arrays_colection, $array_search)
    {
        
        $resultado = null;
        $array_search = array_filter($array_search);
                
        foreach ($arrays_colection as $array) {
            
            $array = array_filter($array);
            $array_found = $array;
            unset($array['id']);
            
            if($array == $array_search){
                $resultado = $array_found;
            }
        }  
        
        return $resultado;
    }
    
    /*
    * filtering an array
    */
    public static function filter_by_value ($array, $index, $value){
        $resultado = array();
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                if ($temp[$key] == $value){
                    $resultado[] = $array[$key];
                }
            }
          }
        return $resultado;
    } 
    
    /**
     * Se sacan todas las tildes
     * @param string $cadena
     * @return string
     */
    public static function quitar_tildes($cadena) {
        $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
        $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
        $texto = str_replace($no_permitidas, $permitidas ,$cadena);
        return $texto;
    }
    
    /**
     * Se convierte un entero (0 o 1) a boolean
     * @param int $int
     * @return boolean
     */
    public static function intToBoolean($int = 0) {
        $resultado = false;
        
        if($int == 1){
            $resultado = true;
        }
        
        return $resultado;
    }
    
    /**
     * Se convierte un boolean a entero (0 o 1)
     * @param bool $bool
     * @return int
     */
    public static function booleanToInt($bool = 'false') {
        
        $resultado = 0;
        
        if($bool=='true' || $bool==true){
            $resultado = 1;
        }

        return $resultado;
    }
    
    public static function setBoolean($bool) {
        
        $resultado = false;
        
        if($bool==='true' || $bool===1 || $bool===true){
            $resultado = true;
        }

        return $resultado;
    }
}