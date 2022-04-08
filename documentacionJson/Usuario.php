<?php

/** Login
* @url http://user.local/api/usuarios/login
* @method POST
* @param arrayJson
{
  "username":"admin",
  "password_hash":"admins"
}
* @return
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDc0NzA5ODAsInVzdWFyaW8iOiJhZG1pbiIsImxpc3RhX21vZHVsbyI6W3siaWQiOiIyIiwibm9tYnJlIjoiTHVnYXIiLCJzZXJ2aWNpbyI6Imx1Z2FyIiwic2lnbGEiOiJMVUciLCJjb21wb25lbnRlIjpudWxsfV0sInVpZCI6MX0.tBckVsonz3kyUDRQLOaFvkHCAt6XPPL3wemHnUUokmo",
    "username": "admin",
    "lista_modulo": [
        {
            "id": "2",
            "nombre": "Lugar",
            "servicio": "lugar",
            "sigla": "LUG",
            "componente": null
        },
        {
            "id": "3",
            "nombre": "INVENTARIO",
            "servicio": "inventario",
            "sigla": "INV",
            "componente": null
        }
    ]
}
**/

/** Listado de usuarios
* @url http://user.local/api/usuarios
* @method GET
* @return
{
    "pagesize": 20,
    "pages": 1,
    "total_filtrado": 3,
    "resultado": [
        {
            "id": 1,
            "username": "admin",
            "email": "admin_apiuser@correo.com",
            "confirmed_at": "2022-01-21",
            "unconfirmed_email": null,
            "blocked_at": null,
            "registration_ip": "172.21.0.8",
            "created_at": "2022-01-21",
            "updated_at": "2022-03-16",
            "flags": 0,
            "last_login_at": "2022-03-16 11:58:00",
            "last_login_ip": "172.21.0.7",
            "personaid": 0,
            "fecha_baja": "",
            "baja": false,
            "descripcion_baja": "",
            "localidadid": 0,
            "lista_modulo": [
                {
                    "id": "2",
                    "nombre": "Lugar",
                    "servicio": "lugar",
                    "sigla": "LUG",
                    "componente": null
                }
            ]
        },
        {
            "id": 5,
            "username": "nucleo",
            "email": "nucleo@correo.com",
            "confirmed_at": "1970-01-01",
            "unconfirmed_email": null,
            "blocked_at": null,
            "registration_ip": null,
            "created_at": "2021-12-06",
            "updated_at": "2022-01-26",
            "flags": 0,
            "last_login_at": "2022-01-26 15:46:13",
            "last_login_ip": "172.21.0.2",
            "personaid": 0,
            "fecha_baja": "",
            "baja": false,
            "descripcion_baja": "",
            "localidadid": 0,
            "lista_modulo": []
        },
        {
            "id": 23,
            "username": "usuario",
            "email": "uncorreo1@correo.com",
            "confirmed_at": "2022-03-04",
            "unconfirmed_email": null,
            "blocked_at": null,
            "registration_ip": "172.21.0.7",
            "created_at": "2022-03-04",
            "updated_at": "2022-03-04",
            "flags": 0,
            "last_login_at": "2022-03-04 16:27:40",
            "last_login_ip": "172.21.0.7",
            "personaid": 2,
            "fecha_baja": "",
            "baja": false,
            "descripcion_baja": "",
            "localidadid": 2626,
            "lista_modulo": [],
            "apellido": "Pezzatti",
            "nombre": "Ruben Alberto",
            "nro_documento": "10477134",
            "cuil": "20104771344"
        }
    ]
}
**/



/** Para crear usuario
* @url http://user.local/api/usuarios 
* @method POST
* @param 
# Con persona existente
{
	"personaid":8564,
	"usuario":{
		"personaid":8564,
		"username":"carlos",
		"password":"12345678",
		"email":"cep11p@correo.com",
		"localidadid":2626,
		"modulo": {
			"nombre" : "INVENTARIO",
			"servicio" : "inventario"
		},
		"rol" : "usuario"
	}
}
# Con persona nueva
{
	"nombre":"Carlos",
	"apellido":"Peralta",
	"nro_documento":"36123456",
	"cuil":"20361234569",
	"usuario":{
		"username":"cperez",
		"password":"carlos",
		"email":"cperez@correo.com",
		"localidadid":2626,
        "modulo": {
			"nombre" : "INVENTARIO",
			"servicio" : "inventario"
		},
		"rol" : "usuario"
	}
}
* @return Json
{
    "message": "Se crea un usuario",
    "data": {
        "id": 21
    }
}
**/

/** Visualizar un usuario
* @url http://user.local/api/usuarios/23
* @method GET
* @return arrayJson
{
    "id": 23,
    "username": "usuario",
    "email": "uncorreo1@correo.com",
    "confirmed_at": "2022-03-04",
    "unconfirmed_email": null,
    "blocked_at": null,
    "registration_ip": "172.21.0.7",
    "created_at": "2022-03-04",
    "updated_at": "2022-03-04",
    "flags": 0,
    "last_login_at": "2022-03-04 16:27:40",
    "last_login_ip": "172.21.0.7",
    "personaid": 2,
    "fecha_baja": "",
    "baja": false,
    "descripcion_baja": "",
    "localidadid": 2626,
    "lista_modulo": [un rollback
    "nombre": "Ruben Alberto",
    "apellido": "Pezzatti",
    "nro_documento": "10477134",
    "cuil": "20104771344",
    "localidad": "Rio Colorado"
}
*/

/** Para modificar
* @url http://user.local/api/usuarios/{$id} 
* @method PUT
* @param arrayJson
{
    "username": "andres",
    "email": "uncorreo1@correo.com",
    "password": "newpass",
    "localidadid": 2626
}
**/

/** Dar de baja un Usuarios
* @url http://user.local/api/usuarios/baja/47 
* @method PUT
* @param arrayJson
{
    "baja":true,
	"descripcion_baja":"Esto es una descripcion de baja de usuario"
}
**/

/** Buscar a un usuario por nro de cuil
 * @url http://user.local/api/usuarios/buscar-persona-por-cuil/20320542389
 * @method GET
 * @return arrayJson
 * 
 {
    "success": true,
    "resultado": {
        "id": 2,
        "nro_documento": "32054238",
        "cuil": "20320542389",
        "nombre": "Isabel Sofía",
        "apellido": "Rodríguez",
        "usuario": {
            "id": 13,
            "username": "cep11p",
            "email": "cep11p@correo.com",
            "auth_key": "aN1ar_QzmaG90RK8vGo3IQdwI6ylIPo3",
            "confirmed_at": 1614092528,
            "unconfirmed_email": null,
            "blocked_at": null,
            "registration_ip": "172.20.0.8",
            "created_at": 1614092528,
            "updated_at": 1614092528,
            "flags": 0,
            "last_login_at": null,
            "personaid": 2,
            "localidadid": 2626
        }
    }
  }
 **/

/** Borrar un usuario de la bd
* @url http://user.local/api/usuarios/2
* @method DELETE
**/

/** Asignacion de modulo a Usuario
* @url http://user.local/api/usuarios/asignar-modulo
* @method POST
* @param arrayJson
{
	"userid" : 1,
	"moduloid" : 1
}
**/

/** Borrar Asignacion de modulo a Usuario
* @url http://user.local/api/usuarios/desasignar-modulo
* @method POST
* @param arrayJson
{
	"userid" : 1,
	"moduloid" : 1
}
**/

/** Listar Asignaciones a Usuarios
* @url http://gcb.local/api/usuarios/listar-asignacion/14
* @method GET
* @param return
[
    {
        "tipo_convenio": "8180",
        "tipo_convenioid": "1",
        "lista_permiso": [
            "prestacion_borrar",
            "cuenta_saldo_crear",
            "cuenta_saldo_crear",
            "persona_crear",
            "cuenta_saldo_crear",
            "persona_crear",
            "prestacion_borrar"
        ],
        "usuarioid": 31
    },
    {
        "tipo_convenio": "8277",
        "tipo_convenioid": "2",
        "lista_permiso": [
            "cuenta_saldo_crear",
            "persona_crear"
        ],
        "usuarioid": 31
    }
]
**/

/** Borrar Asignaciones a Usuarios
* @url http://gcb.local/api/usuarios/borrar-asignacion 
* @method POST
* @param arrayJson
{
    "lista_permiso": [
        "cuenta_saldo_exportar",
        "cuenta_ver"
    ],
    "usuarioid": 2
}
**/