<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enlaces externos de los botones de continente
    |--------------------------------------------------------------------------
    |
    | URLs destino de los botones flotantes (America / Europe / Asia) que se
    | muestran en la vista de dynamic-tools. Se leen desde variables de entorno
    | (definidas en .env). Se usan vía config('links.xxx') en las vistas para
    | que sigan funcionando cuando el pipeline ejecuta `php artisan config:cache`
    | (env() directo en Blade devuelve null con la config cacheada).
    |
    | Los valores por defecto apuntan a la herramienta LTA y garantizan que los
    | botones funcionen aunque el .env del entorno no defina las variables.
    |
    */

    'america' => env('URL_AMERICA', 'https://ethanolblendslta.grains.org/en/dynamic-tools-continent/1'),
    'europe'  => env('URL_EUROPE',  'https://ethanolblendslta.grains.org/en/dynamic-tools-continent/2'),
    'asia'    => env('URL_ASIA',    'https://ethanolblendslta.grains.org/en/dynamic-tools-continent/3'),

];
