<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enlaces externos de los botones de continente
    |--------------------------------------------------------------------------
    |
    | Los botones flotantes (America / Europe / Asia) de la vista dynamic-tools
    | enlazan a las rutas dynamic-tools-continent de este mismo sitio, usando
    | URL absoluta. Solo el DOMINIO cambia entre entornos, por eso se define en
    | una unica variable de entorno TOOL_BASE_URL; las rutas viven aqui.
    |
    | Se consumen via config('links.xxx') en las vistas para que sigan
    | funcionando con `php artisan config:cache` (env() directo en Blade
    | devuelve null con la config cacheada).
    |
    | El valor por defecto es el dominio de staging. Al desplegar en otro
    | entorno, el proveedor solo ajusta TOOL_BASE_URL en su .env.
    |
    */

    'base' => $base = rtrim(env('TOOL_BASE_URL', 'https://global.vision-it.com.mx'), '/'),

    'america' => $base.'/en/dynamic-tools-continent/1',
    'europe'  => $base.'/en/dynamic-tools-continent/2',
    'asia'    => $base.'/en/dynamic-tools-continent/3',

];
