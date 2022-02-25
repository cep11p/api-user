<?php

/**** Para mostrar listado ****/
/*****Login****
* @url http://api.user.local/api/modulos?param1=valor1&param2=valor2
* @param int pagesize este parametro si es igual 0 o está nulo desactiva la paginacion 
* @param string nombre
* @param string servicio
* @param string sigla
* @param string componente
* @method GET
* @return arrayJson #con paginacion
   
{
    "pagesize": 10,
    "pages": 1,
    "total_filtrado": 7,
    "resultado": [
        {
            "id": 3,
            "nombre": "Gestor Cuentas Bancarias",
            "servicio": "gcb",
            "sigla": "GCB",
            "componente": null
        },
        {
            "id": 4,
            "nombre": "Inventario",
            "servicio": "inventario",
            "sigla": "INV",
            "componente": null
        },
        {
            "id": 2,
            "nombre": "Lugar",
            "servicio": "lugar",
            "sigla": "LUG",
            "componente": null
        },
    ]
}

* @return arrayJson #con paginacion
[
    {
        "id": 3,
        "nombre": "Gestor Cuentas Bancarias",
        "servicio": "gcb",
        "sigla": "GCB",
        "componente": null
    },
    {
        "id": 4,
        "nombre": "Inventario",
        "servicio": "inventario",
        "sigla": "INV",
        "componente": null
    },
    {
        "id": 2,
        "nombre": "Lugar",
        "servicio": "lugar",
        "sigla": "LUG",
        "componente": null
    }
]
**/
