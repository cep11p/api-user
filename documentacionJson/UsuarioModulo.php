<?php

/** Para mostrar listado
* @url http://user.local/api/usuario-modulos?param1=valor1&param2=valor2
* @param int pagesize este parametro si es igual 0 o está nulo desactiva la paginacion 
* @param int userid
* @param int moduloid
* @method GET
* @return arrayJson #con paginacion
   
{
    "pagesize": 1,
    "pages": 1,
    "total_filtrado": 1,
    "resultado": [
        {
        "userid": 1,
        "moduloid": 2,
        "create_at": "2022-02-24 14:12:24"
        }
    ]
}

* @return arrayJson #con paginacion
[
    {
        "userid": 1,
        "moduloid": 2,
        "create_at": "2022-02-24 14:12:24"
    }
]
**/
