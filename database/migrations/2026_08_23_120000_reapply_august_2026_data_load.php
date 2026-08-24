<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reaplica la carga de datos de agosto 2026.
 *
 * Contexto: los commits 20259ec ("Cambios en Data en secciones, 12 de Agosto")
 * y 3e35b5f ("Carga de datos de carga Agosto") reescribieron 17 migraciones ya
 * existentes en lugar de crear archivos nuevos. Como sus nombres seguian
 * registrados en la tabla `migrations`, artisan reporto "Nothing to migrate."
 * en los dos despliegues (runs 31660238080 y 32325174833) y los datos nunca
 * llegaron al servidor.
 *
 * Esta migracion vuelve a ejecutar el up() de esas 17 respetando su orden
 * cronologico original. Ver ADR-007 del framework.
 */
class ReapplyAugust2026DataLoad extends Migration
{
    /**
     * Migraciones que recrean su tabla por completo (dropIfExists + create +
     * insert). Se auto-limpian, pero su DDL provoca commit implicito en MySQL,
     * asi que corren fuera de la transaccion y antes que el resto.
     */
    private const SELF_CLEANING_MIGRATIONS = [
        '2025_09_25_024317_create_table_section_1'  => 'CreateTableSection1',
        '2025_09_26_030317_create_table_section_2'  => 'CreateTableSection2',
        '2025_09_28_183948_create_table_section_3'  => 'CreateTableSection3',
        '2025_09_30_181915_latam_ghg'               => 'LatamGhg',
    ];

    /**
     * Resto de migraciones, en orden cronologico original. Solo DML, corren
     * dentro de la transaccion.
     */
    private const DATA_MIGRATIONS = [
        '2025_09_30_164702_latam_dynamic_tool_text'      => 'LatamDynamicToolText',
        '2025_09_30_173102_latam_gasoline_components'    => 'LatamGasolineComponents',
        '2025_09_30_181844_latam_emissions'              => 'LatamEmissions',
        '2025_10_02_011832_latam_vehicles'               => 'LatamVehicles',
        '2025_10_09_021550_europe_emissions'             => 'EuropeEmissions',
        '2025_10_09_021616_europe_vehicles'              => 'EuropeVehicles',
        '2025_10_09_022855_europe_gasoline_components'   => 'EuropeGasolineComponents',
        '2025_10_09_023440_europe_ghg'                   => 'EuropeGhg',
        '2025_10_17_020037_asia_dynamic_tool_text'       => 'AsiaDynamicToolText',
        '2025_10_17_021235_asia_gasoline_components'     => 'AsiaGasolineComponents',
        '2025_10_17_021733_asia_emissions'               => 'AsiaEmissions',
        '2025_10_17_022135_asia_ghg'                     => 'AsiaGhg',
        '2025_10_17_115555_asia_vehicles'                => 'AsiaVehicles',
    ];

    /**
     * Tablas donde Europa (country_id 27..54) y Asia (55+) se insertan sin
     * guarda de idempotencia: hay que limpiar su rango antes de reejecutar.
     * Las migraciones base de 2023 solo llegan hasta country_id 25, asi que
     * borrar por encima de 26 no elimina datos que nadie vuelva a insertar.
     */
    private const REINSERTED_TABLES = [
        'dynamic_tools_texts',
        'emissions',
        'vehicles',
        'gasoline_components',
    ];

    /**
     * Nigeria (country_id 61) se conserva en `emissions` y `vehicles`.
     *
     * La carga de agosto dejo de insertarla en esas dos tablas, pero la mantuvo
     * en `dynamic_tools_texts` y `gasoline_components`, y su pagina sigue
     * publicada. Borrarla sin reinsertarla la dejaria sin datos de emisiones,
     * asi que se excluye del limpiado para no introducir una regresion. Mismo
     * criterio que en lta-ethanol-production.
     */
    private const PRESERVED_COUNTRY_ID = '61';

    private const PRESERVE_IN_TABLES = [
        'emissions',
        'vehicles',
    ];

    public function up()
    {
        $this->loadSourceMigrations();

        // Fase 1 — DDL. Cada una dropea y recrea su tabla, asi que no necesita
        // limpieza previa. Fuera de la transaccion por el commit implicito.
        foreach (self::SELF_CLEANING_MIGRATIONS as $class) {
            (new $class())->up();
        }

        // EuropeGasolineComponents crea etbe/tame con guarda hasColumn. Se
        // adelanta aqui para que dentro de la transaccion sea un no-op y no
        // quede DDL escondido en medio del DML.
        $this->ensureEtbeTameColumns();

        DB::transaction(function () {
            foreach (self::REINSERTED_TABLES as $table) {
                $stale = DB::table($table)->where('country_id', '>', 26);

                if (in_array($table, self::PRESERVE_IN_TABLES, true)) {
                    $stale->where('country_id', '<>', self::PRESERVED_COUNTRY_ID);
                }

                $stale->delete();
            }

            foreach (self::DATA_MIGRATIONS as $class) {
                (new $class())->up();
            }
        });
    }

    private function ensureEtbeTameColumns()
    {
        Schema::table('gasoline_components', function (Blueprint $table) {
            if (!Schema::hasColumn('gasoline_components', 'tame')) {
                $table->string('tame', 60)->after('raffinate')->nullable();
                $table->string('etbe', 60)->after('raffinate')->nullable();
            }
        });
    }

    /**
     * Las migraciones ya corrieron, asi que el Migrator no vuelve a incluir sus
     * archivos. Se cargan a mano para poder instanciar sus clases.
     */
    private function loadSourceMigrations()
    {
        $migrations = array_merge(
            array_keys(self::SELF_CLEANING_MIGRATIONS),
            array_keys(self::DATA_MIGRATIONS)
        );

        foreach ($migrations as $migration) {
            require_once database_path('migrations/' . $migration . '.php');
        }
    }

    /**
     * Sin reversa: revertir significaria restaurar la carga de datos anterior,
     * que ya no existe en el repositorio. Para volver atras, restaurar backup.
     */
    public function down()
    {
        //
    }
}
