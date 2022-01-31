<?php

/*
 * Clase para interactuar con el servicio de solicitudes de lugar (sistemaLugar)
 *
 */

namespace app\components;
use yii\base\Component;
use GuzzleHttp\Client;
use Exception;
use app\components\Help;

/**
 * Description of ServicioSolicitudComponent
 *
 * @author cep11p
 */
class DummyServicioInteroperable extends Component implements IServicioInteroperable
{
    public $base_uri;
    private $_client;
   
    public function __construct(Client $guzzleClient, $config=[])
    {
        parent::__construct($config);
        $this->_client = $guzzleClient;        
    }
   
    
       
}