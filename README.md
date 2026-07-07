# LTA Global

## Requirements

- **PHP extension php_zip enabled**
- **PHP extension php_xml enabled**
- **PHP extension php_iconv enabled**
- **PHP extension php_simplexml enabled**
- **PHP extension php_xmlreader enabled**
- **PHP extension php_zlib enabled**
- **Also install the ext-zip extension**


## Database

For the database, follow these instructions.

1. Create a backup of the production environment by running:

- **mysqldump -u [username] -p [database] > prod_database.sql**

2. Create a backup of the users table by running:

- **mysqldump -u [username] -p [database] users > users.sql**

3. Download the latest changes and deploy the application.

4. Run the database migrations:

- **php artisan migrate:fresh --seed**

5. Finally, restore the users table data by running:

- **mysql -u [username] -p [database] < users.sql**

## Environment variables

- **APP_EUROPE_ID** — the id is 23.
- **APP_DEFAULT_COUNTRY_ID** — the id is 4.
- **APP_USA_ID** — the id is 24.
- **ADMIN_PASS** — this password is set by US Grains.

### Base domain and continent buttons (dynamic-tools)

The floating buttons (America / Europe / Asia) on the `dynamic-tools` view link
to this site's own `dynamic-tools-continent` routes using an absolute URL. Only
the **domain** changes between environments, so it is defined in a single
variable:

- **TOOL_BASE_URL** — the site's base domain. Changing the domain means changing
  only this variable. It defaults to the staging domain
  (`https://global.vision-it.com.mx`). When deploying to a client/production
  environment, set it to the real domain (e.g. `https://ethanolblendslta.grains.org`).

The full routes are built in `config/links.php` and consumed in the views via
`config('links.america|europe|asia')`.

> Note: always use `config('links.xxx')` in the views, **not** `env('...')`
> directly. Because the pipeline runs `php artisan config:cache`, `env()`
> returns `null` at runtime once the config is cached. If the variable is not
> defined, the default value from `config/links.php` is used.
