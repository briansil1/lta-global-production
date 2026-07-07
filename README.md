## Requirements

- **PHP extension php_zip enabled**
- **PHP extension php_xml enabled**
- **PHP extension php_iconv enabled**
- **PHP extension php_simplexml enabled**
- **PHP extension php_xmlreader enabled**
- **PHP extension php_zlib enabled**
- **Also install the ext-zip extension**


## Database

For the database, you need to follow the following instructions.

1. You need to create a backup of the production environment, You must execute the following instruction

- **mysqldump -u [username] -p [database] > prod_database.sq**

2. Create user table backup, You must execute the following instruction

- **mysqldump -u [username] -p [database] users > users.sql**

3. Download the latest changes and deploy the application

4. Create database migrations
- **php artisan migrate:fresh --seed**

5. Finally we must return the information from the users table, for this it is necessary to execute the following instruction

- **mysql -u [username] -p [database] < users.sql**

## Enviroment variables
- **APP_EUROPE_ID**
The id is 23
- **APP_DEFAULT_COUNTRY_ID**
The id is 4
- **APP_USA_ID**
The id is 24

- **ADMIN_PASS**

### Enlaces de botones de continente (dynamic-tools)

URLs destino de los botones flotantes America / Europe / Asia en la vista
`dynamic-tools`. Se consumen vía `config('links.*')` (ver `config/links.php`),
por lo que **funcionan con `php artisan config:cache`**. Si no se definen en el
`.env`, se usan los valores por defecto de `config/links.php`.

- **URL_AMERICA** — botón America / LATAM. Ej: `https://ethanolblendslta.grains.org/en/dynamic-tools-continent/1`
- **URL_EUROPE** — botón Europe. Ej: `https://ethanolblendslta.grains.org/en/dynamic-tools-continent/2`
- **URL_ASIA** — botón Asia. Ej: `https://ethanolblendslta.grains.org/en/dynamic-tools-continent/3`

> Nota: usar siempre `config('links.xxx')` en las vistas, **no** `env('URL_XXX')`
> directamente, porque con la config cacheada `env()` devuelve `null` en runtime.
This password is set by US Grains