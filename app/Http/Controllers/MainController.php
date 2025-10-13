<?php
namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use App\Models\Emission;
use App\Models\Locale;
use App\Models\Profile;
use App\Models\Report;
use App\Models\Continent;
use App\Models\VolumeQuality;
use App\Models\VehicularEmissions;
use App\Models\GreenHouse;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    public $type = '_lt'; // Valor por defecto

    public function home(Request $request, $token = false) {
        $locale = app()->getLocale();
        $base_l = explode('_', $locale)[0];
        $locale = Locale::where('code', $locale)->first();
        if (empty($locale)) {
            $locale = Locale::where('code', $base_l)->first();
        }
        $reports = $locale->reports()->where('active', 1)->orderBy('order', 'asc')->get();
        return view('home', [
            'reports' => $reports,
            'token' => $token
        ]); 
    }

    public function tools(Request $request, $tab = '1', Continent $continent = null,  $regionid = null, $type = null) {
        if (!Auth::check()) {
            return redirect(route(__('routes.home')));
        }
        if (!session('continent_id')) {
            return redirect(route('logout-session'));
        }
        $tab = $request->tab ?? '1';
        $continentid = $continent->id ?? 1;
        $continent = ($continentid && (int)$continentid > 0) ? Continent::find($continentid) : null;
        $region = ($regionid && (int)$regionid > 0) ? Region::find($regionid) : null;
        // Variables
        $dataG1 = null;
        $chartLabels = null;
        $chartValues = null;
        $chartLabels2 = null;
        $chartValues2 = null;
        $chartLabels3 = null;
        $chartValues3 = null;
        $chartLabels4 = null;
        $chartValues4 = null;
        $dataGenerales = null;  
        // Si type es null o es igual a litros entonces $type = '_lt'; de lo contrario $type = '_gl';
        if($tab == 1){
            if($request->type === null || $request->type === 'litros') {
                $type = '_lt';
                $this->type = '_lt';
            } else {
                $type = '_gl';
                $this->type = '_gl';
            }
        }
        // si $tab == 2  this->type = "g" o "ton"
        if ($tab == 2){
            if($request->type === null ) {
                $type = 'Benzene';
            } elseif($tab == 2 && $request->type === 'ton') {
                $type = $request->type;
            }
        }

        if ($tab == 3){
            if($request->type === null ) {
                $type = 'RED III';
            } else {
                $type = $request->type;
            }
        }
        // Obtener todas las regiones sin where
        $regions = Region::all();
        // filtrar regiones por continent_id = 1 y guardar en $regions1
        $regionsAll = $regions->where('continent_id', $continentid);
        if($tab == '1') {
            // Datos generales de volumen y calidad (totales por continente) para mostrar en la tabla superior
            $dataGenerales = VolumeQuality::query()
                ->with(['region'])     // eager loading
                ->where('tipo', 'C')   // o ->tipo('P')
                // ->select(['id','region_id','campo1','campo2', ...]) // opcional: limitar columnas
                ->get();

            // Datos de volumen y calidad para luego filtrar por graficas
            $dataG1 = VolumeQuality::query()
            ->with('region')  // eager loading
            ->where('tipo', 'P')
            ->whereHas('region', function ($q) use ($continentid) {
                $q->where('continent_id', $continentid);
            })
            ->when($regionid > 0, function ($q) use ($regionid) {
                $q->where('region_id', $regionid);
            })
            ->get();

            // --> GRAFICA 1 - Ordena desc por gasolina y arma labels/values para el gráfico de litros
            $g1Sorted = $dataG1->sortByDesc('gasoline_demand'.$type)->values();
            // Labels de ciudades 
            $chartLabels = $g1Sorted->pluck('country');
            // Valores numéricos
            $chartValues = $g1Sorted->pluck('gasoline_demand'.$type);

            // --> GRAFICA 2 - Ordena desc por gasolina y arma labels/values para el gráfico de litros de crecimiento %
            $g2Sorted = $dataG1->sortByDesc('gasoline_growth_porc')->values();
            // Labels de ciudades 
            $chartLabels2 = $g2Sorted->pluck('country');
            // Valores numéricos
            $chartValues2 = $g2Sorted->pluck('gasoline_growth_porc');
            // Multiplicar todos los valores por 100
            $chartValues2 = $chartValues2->map(fn($v) => $v * 100);
            
            // --> GRAFICA 3 - Ordena desc por gasolina y arma labels/values para el gráfico de litros de producción
            $g3Sorted = $dataG1->sortByDesc('gasoline_prod'.$type)->values();
            // Labels de ciudades
            $chartLabels3 = $g3Sorted->pluck('country');
            // Valores numéricos
            $chartValues3 = $g3Sorted->pluck('gasoline_prod'.$type);

            // --> GRAFICA 4 - Ordena desc por gasolina y arma labels/values para el gráfico de litros de importación
            $g4Sorted = $dataG1->sortByDesc('gasoline_import'.$type)->values();
            // Labels de ciudades
            $chartLabels4 = $g4Sorted->pluck('country');
            // Valores numéricos
            $chartValues4 = $g4Sorted->pluck('gasoline_import'.$type);

            // --> GRAFICA 5 - Ordena desc por Ethanol Demand y arma labels/values para el gráfico de litros de Ethanol Demand
            $g5Sorted = $dataG1->sortByDesc('ethanol_demand'.$type)->values();
            // Labels de ciudades 
            $chartLabels5 = $g5Sorted->pluck('country');
            // Valores numéricos
            $chartValues5 = $g5Sorted->pluck('ethanol_demand'.$type);
            // --> GRAFICA 6 - Ordena desc por Octane Demand y arma labels/values para el gráfico de litros de Octane Demand
            $g6Sorted = $dataG1->sortByDesc('ethanol_prod'.$type)->values();
            // Labels de ciudades
            $chartLabels6 = $g6Sorted->pluck('country');
            // Valores numéricos
            $chartValues6 = $g6Sorted->pluck('ethanol_prod'.$type);
            // --> GRAFICA 7 - Ordena desc por Octane Potential y arma labels/values para el gráfico de litros de Octane Potential
            // El tema para esta grafica es que cada barra ejemplo mexico tiene diferentes valores campos e10_lt, e15_lt, e20_lt, e25_lt, e30_lt, e0_lt
            $g7Sorted = $dataG1->values();
            // Labels de ciudades
            $g7Sorted = $dataG1->map(function ($r) {
       
                if($this->type == '_lt') {
                    return [
                        'country' => $r->country,
                        'e0_lt'  => (float)($r->e0_lt ?? 0),
                        'e10_lt' => (float)($r->e10_lt ?? 0),
                        'e15_lt' => (float)($r->e15_lt ?? 0),
                        'e20_lt' => (float)($r->e20_lt ?? 0),
                        'e25_lt' => (float)($r->e25_lt ?? 0),
                        'e30_lt' => (float)($r->e30_lt ?? 0),
                        'total'  => (float)(
                            ($r->e0_lt ?? 0)+($r->e10_lt ?? 0)+($r->e15_lt ?? 0)+($r->e20_lt ?? 0)+($r->e25_lt ?? 0)+($r->e30_lt ?? 0)
                        ),
                    ];
                }else {
                    return [    
                        'country' => $r->country,
                        'e0_lt'  => (float)($r->e0_gl ?? 0),
                        'e10_lt' => (float)($r->e10_gl ?? 0),
                        'e15_lt' => (float)($r->e15_gl ?? 0),
                        'e20_lt' => (float)($r->e20_gl ?? 0),
                        'e25_lt' => (float)($r->e25_gl ?? 0),
                        'e30_lt' => (float)($r->e30_gl ?? 0),
                        'total'  => (float)(
                            ($r->e0_gl ?? 0)+($r->e10_gl ?? 0)+($r->e15_gl ?? 0)+($r->e20_gl ?? 0)+($r->e25_gl ?? 0)+($r->e30_gl ?? 0)
                        ),
                    ];
                }
            })
            ->sortByDesc('total')
            ->values();  
            $chartLabels7 = $g7Sorted->pluck('country');
            $chartValues7 = $g7Sorted->map(fn($r) => collect($r)->except(['country','total']));
    

            //var_dump($chartValues7);
            //return false;
            return view('dynamic', [
                'tab' => $tab,
                'continent' => $continent,
                'region' => $region,
                'continentid' => $continentid,
                'regionid' => $regionid,
                'regionsAll' => $regionsAll,
                'dataG1' => $dataG1,
                'type' => $type,
                'chartLabels'  => $chartLabels,
                'chartValues'  => $chartValues,

                'chartLabels2' => $chartLabels2,
                'chartValues2' => $chartValues2,

                'chartLabels3' => $chartLabels3,
                'chartValues3' => $chartValues3,

                'chartLabels4' => $chartLabels4,
                'chartValues4' => $chartValues4,

                'chartLabels5' => $chartLabels5,
                'chartValues5' => $chartValues5,

                'chartLabels6' => $chartLabels6,
                'chartValues6' => $chartValues6,

                'chartLabels7' => $chartLabels7,
                'chartValues7' => $chartValues7,
        

                'dataGenerales' => $dataGenerales,
                
            ]);
            
        } elseif($tab == '2') {
            // Si $regionid es null o vacio o "" entonces =  1
            //echo $tab."-".$continentid."-".$regionid."-".$type;
            //return false;
            $dataGenerales = VehicularEmissions::query()
                ->with(['region'])     // eager loading
                ->where('tipo', 'C')   // o ->tipo('C')
                // ->select(['id','region_id','campo1','campo2', ...]) // opcional: limitar columnas
                ->get();

            // Datos de emisiones vehiculares
            // Si $this->type == "" entonces $this->type = "Benzene"
            if($this->type == '') {
                $this->type = 'Benzene';
            }
            // --> GRAFICA 1 - CO Emmisions (g/km)
            $dataG1 = VehicularEmissions::query()
                ->with('region') // eager loading
                ->whereHas('region', function ($q) use ($continentid) {
                    $q->where('continent_id', $continentid);
                })
                ->when($regionid > 0, fn($q) => $q->where('region_id', $regionid))
                ->where('tipo', 'P')
                ->where('emission_component', $type)
                ->where('emission_type', 'Emissions (g/km)')
                ->get();
            
            $g1Limits = VehicularEmissions::query()
                ->where('tipo', 'L')
                ->where('emission_component', $type)
                ->where('emission_type', 'Emissions (g/km)')
                ->get();

            // El tema para esta grafica es que cada barra ejemplo mexico tiene diferentes valores campos e10, e15, e20, e25, e30, e0
            //$g1Sorted = $dataG1->values();
            // Labels de ciudades
            $g1Sorted = $dataG1->map(function ($r) {
                return [
                    'country' => $r->country,
                    'e0'  => (float)($r->e0 ?? 0),
                    'e10' => (float)($r->e10 ?? 0),
                    'e15' => (float)($r->e15 ?? 0),
                    'e20' => (float)($r->e20 ?? 0),
                    'e25' => (float)($r->e25 ?? 0),
                    'e30' => (float)($r->e30 ?? 0),
                    'total'  => (float)(
                        ($r->e0 ?? 0)+($r->e10 ?? 0)+($r->e15 ?? 0)+($r->e20 ?? 0)+($r->e25 ?? 0)+($r->e30 ?? 0)
                    ),
                ];
            })->sortByDesc('total')->values();  

            $chartLabels1 = $g1Sorted->pluck('country');
            $chartValues1 = $g1Sorted->map(fn($r) => collect($r)->except(['country','total']));

            // --> GRAFICA 2 - CO2 Emmisions (Ton/kday)
            $dataG2 = VehicularEmissions::query()
                ->with('region') // eager loading
                ->whereHas('region', function ($q) use ($continentid) {
                    $q->where('continent_id', $continentid);
                })
                ->when($regionid > 0, fn($q) => $q->where('region_id', $regionid))
                ->where('tipo', 'P')
                ->where('emission_component', $type)
                ->where('emission_type', 'Emissions (Ton/day)')
                ->get();

            // El tema para esta grafica es que cada barra ejemplo mexico tiene diferentes valores campos e
            //$g2Sorted = $dataG2->values();
            // Labels de ciudades
            $g2Sorted = $dataG2->map(function ($r) {
                return [
                    'country' => $r->country,
                    'e0'  => (float)($r->e0 ?? 0),
                    'e10' => (float)($r->e10 ?? 0),
                    'e15' => (float)($r->e15 ?? 0),             
                    'e20' => (float)($r->e20 ?? 0),
                    'e25' => (float)($r->e25 ?? 0),
                    'e30' => (float)($r->e30 ?? 0),
                    'total'  => (float)(
                        ($r->e0 ?? 0)+($r->e10 ?? 0)+($r->e15 ?? 0)+($r->e20 ?? 0)+($r->e25 ?? 0)+($r->e30 ?? 0)
                    ),
                ];
            })->sortByDesc('total')->values();
      
            $chartLabels2 = $g2Sorted->pluck('country');
            $chartValues2 = $g2Sorted->map(fn($r) => collect($r)->except(['country','total']));

            // --> GRAFICA 3 - Vehicle Fleet (millions)
            $dataG3 = VolumeQuality::query()
            ->with('region')  // eager loading
            ->where('tipo', 'P')
            ->whereHas('region', function ($q) use ($continentid) {
                $q->where('continent_id', $continentid);
            })
            ->when($regionid > 0, function ($q) use ($regionid) {
                $q->where('region_id', $regionid);
            })
            ->get();

            // --> GRAFICA 3 - Ordena desc por gasolina y arma labels/values para el gráfico de litros
            $g3Sorted = $dataG3->sortByDesc('vehicle_fleet')->values();
            // Labels de ciudades 
            $chartLabels3 = $g3Sorted->pluck('country');
            // Valores numéricos
            $chartValues3 = $g3Sorted->pluck('vehicle_fleet');
            //echo $regionid;
            $regionid = ($regionid && (int)$regionid > 0) ? $regionid : 0;

            // si $regionid == 10 entonces $regionid = 1
            /*
            if($regionid == 0) {
                $regionid = 1;
            }
            */
            return view('dynamic', [
                'tab' => $tab,
                'continent' => $continent,
                'region' => $region,
                'continentid' => $continentid,
                'regionid' => $regionid,
                'type' => $type,
                'regionsAll' => $regionsAll,

                'g1Limits' => $g1Limits,
                'chartLabels1'  => $chartLabels1,
                'chartValues1'  => $chartValues1,

                'chartLabels2'  => $chartLabels2,
                'chartValues2'  => $chartValues2,

                'chartLabels3' => $chartLabels3,
                'chartValues3' => $chartValues3,

                'dataGenerales' => $dataGenerales,
            ]);
        } elseif($tab == '3') {
            // Si $regionid es null o vacio o "" entonces =  0
            if($regionid === null || $regionid === '' || $regionid === '0') {
                $regionid = 0;
            }

            //echo $tab."-".$continentid."-".$regionid."-".$type;
            // Datos de emisiones de gases invernadero
            $dataGenerales = GreenHouse::query()
                ->with(['region'])     // eager loading
                // ->select(['id','region_id','campo1','campo2', ...]) // opcional: limitar columnas
                ->get();
            $regionsAll = $regions->where('continent_id', $continentid);
            
            // --> GRAFICA 1 - Life Cycle GHG Emissions (MMT/yr)
            $dataG1 = GreenHouse::query()
                ->with('region') // eager loading
                ->whereHas('region', function ($q) use ($continentid) {
                    $q->where('continent_id', $continentid);
                })
                ->when($regionid > 0, fn($q) => $q->where('region_id', $regionid))
                ->where('methodology', $type)
                ->where('data', 'GHG')
                ->where('tipo', 'P')
                ->get();

            $g1Sorted = $dataG1->map(function ($r) {
                return [
                    'country' => $r->country,
                    
                    'e10' => (float)($r->e10_em ?? 0),
                    'e15' => (float)($r->e15_em ?? 0),
                    'e20' => (float)($r->e20_em ?? 0),
                    'e25' => (float)($r->e25_em ?? 0),
                    'e30' => (float)($r->e30_em ?? 0),
                    'total'  => (float)(
                        ($r->e10_em ?? 0)+($r->e15_em ?? 0)+($r->e20_em ?? 0)+($r->e25_em ?? 0)+($r->e30_em ?? 0)
                    ),
                ];
            })->sortByDesc('total')->values();  
            $chartLabels1 = $g1Sorted->pluck('country');
            $chartValues1 = $g1Sorted->map(fn($r) => collect($r)->except(['country','total']));


            // --> GRAFICA 2 - Life Cycle GHG Reductions (%)

            $dataG2 = GreenHouse::query()
                ->with('region') // eager loading
                ->whereHas('region', function ($q) use ($continentid) {
                    $q->where('continent_id', $continentid);
                })
                ->when($regionid > 0, fn($q) => $q->where('region_id', $regionid))
                ->where('methodology', $type)
                ->where('data', '%Redvsbase')
                ->get();
                
            $g2Sorted = $dataG2->map(function ($r) {
                return [
                    'country' => $r->country,
                    'e0'  => (float)($r->e0_em ?? 0),
                    'e10' => (float)($r->e10_em ?? 0),
                    'e15' => (float)($r->e15_em ?? 0),
                    'e20' => (float)($r->e20_em ?? 0),
                    'e25' => (float)($r->e25_em ?? 0),
                    'e30' => (float)($r->e30_em ?? 0),
                    'total'  => (float)(
                        ($r->e0_em ?? 0)+($r->e10_em ?? 0)+($r->e15_em ?? 0)+($r->e20_em ?? 0)+($r->e25_em ?? 0)+($r->e30_em ?? 0)
                    ),
                ];      
            })->sortByDesc('total')->values();  
            $chartLabels2 = $g2Sorted->pluck('country');
            $chartValues2 = $g2Sorted->map(fn($r) => collect($r)->except(['country','total']));

            
            // --> GRAFICA 3 - 2035 GHG Participation (%)

            $dataG3 = GreenHouse::query()
                ->with('region') // eager loading
                ->whereHas('region', function ($q) use ($continentid) {
                    $q->where('continent_id', $continentid);
                })
                ->when($regionid > 0, fn($q) => $q->where('region_id', $regionid))
                ->where('methodology', $type)
                ->where('data', '%RedTarget')
                ->get();

              

            $g3Sorted = $dataG3->map(function ($r) {
                return [
                    'country' => $r->country,
                    'e0'  => (float)($r->e0_em ?? 0),
                    'e10' => (float)($r->e10_em ?? 0),
                    'e15' => (float)($r->e15_em ?? 0),
                    'e20' => (float)($r->e20_em ?? 0),
                    'e25' => (float)($r->e25_em ?? 0),
                    'e30' => (float)($r->e30_em ?? 0),
                    'total'  => (float)(
                        ($r->e0_em ?? 0)+($r->e10_em ?? 0)+($r->e15_em ?? 0)+($r->e20_em ?? 0)+($r->e25_em ?? 0)+($r->e30_em ?? 0)
                    ),
                ];      
            })->sortByDesc('total')->values();  
            $chartLabels3 = $g3Sorted->pluck('country');
          
            $chartValues3 = $g3Sorted->map(fn($r) => collect($r)->except(['country','total']));

            //var_dump($chartValues2);
            //echo "<hr>";
            //var_dump($chartValues3);
            //return false;
            // --> GRAFICA 4 - Carbone Intensity (gCO2e/MJ) - 
            $dataG4 = GreenHouse::query()
                ->with('region') // eager loading
                ->whereHas('region', function ($q) use ($continentid) {
                    $q->where('continent_id', $continentid);
                })
                ->when($regionid > 0, fn($q) => $q->where('region_id', $regionid))
                ->where('methodology', $type)
                ->where('data', 'CI')
                ->get();

            $g4Sorted = $dataG4->map(function ($r) {
                return [
                    'country' => $r->country,
                    'e0'  => (float)($r->e0_em ?? 0),
                    'e10' => (float)($r->e10_em ?? 0),
                    'e15' => (float)($r->e15_em ?? 0),
                    'e20' => (float)($r->e20_em ?? 0),
                    'e25' => (float)($r->e25_em ?? 0),
                    'e30' => (float)($r->e30_em ?? 0),
                    'total'  => (float)(
                        ($r->e0_em ?? 0)+($r->e10_em ?? 0)+($r->e15_em ?? 0)+($r->e20_em ?? 0)+($r->e25_em ?? 0)+($r->e30_em ?? 0)
                    ),  
                ];    
            })->sortByDesc('total')->values();  
            $chartLabels4 = $g4Sorted->pluck('country');
            $chartValues4 = $g4Sorted->map(fn($r) => collect($r)->except(['country','total']));     

            return view('dynamic', [
                'tab' => $tab,
                'continent' => $continent,
                'region' => $region,
                'continentid' => $continentid,
                'regionid' => $regionid,
                'dataGenerales' => $dataGenerales,
                'regionsAll' => $regionsAll,
                'type' => $type,
                'chartLabels1' => $chartLabels1,
                'chartValues1' => $chartValues1,
                'chartLabels2' => $chartLabels2,
                'chartValues2' => $chartValues2,
                'chartLabels3' => $chartLabels3,
                'chartValues3' => $chartValues3,
                'chartLabels4' => $chartLabels4,
                'chartValues4' => $chartValues4,
                'dataG1' => $dataG1,
            ]);
        }
    }

    public function setLocale(Request $request) {
        $request->validate([
            'new_locale' => ['required', Rule::in(['es', 'en', 'fr'])],
            'route' => 'required|string|min:4'
        ]);

        $request->session()->put('locale', $request->new_locale);
        redirect(route($request->route));
    }

    /**
     * @throws AuthenticationException
     * @throws FileNotFoundException
     */
    public function getPDFs(Request $request, Report $report)
    {
        if (!Auth::check()) {
            throw new AuthenticationException();
        }
        if (file_exists(base_path('storage/app/pdfs/' . $report->report_url))) {
            return response()->download(base_path('storage/app/pdfs/' . $report->report_url));
        } else {
            throw new FileNotFoundException('storage/app/pdfs/' . $report->report_url);
        }
    }

    /**
     * @throws AuthenticationException
     * @throws FileNotFoundException
     */
    public function downloadProfile() {
        if (!Auth::check()) {
            throw new AuthenticationException();
        }
        if (file_exists(base_path('storage/app/pdfs/' . __('dynamic.pdf-files.profile-pdf-filename')))) {
            return response()->download(base_path('storage/app/pdfs/' . __('dynamic.pdf-files.profile-pdf-filename')));
        } else {
            throw new FileNotFoundException('storage/app/pdfs/' . __('dynamic.pdf-files.profile-pdf-filename'));
        }
    }

    /**
     * @throws AuthenticationException
     * @throws FileNotFoundException
     */
    public function downloadComponents() {
        if (!Auth::check()) {
            throw new AuthenticationException();
        }
        if (file_exists(base_path('storage/app/pdfs/' . __('dynamic.pdf-files.component-pdf-filename')))) {
            return response()->download(base_path('storage/app/pdfs/' . __('dynamic.pdf-files.component-pdf-filename')));
        } else {
            throw new FileNotFoundException('storage/app/pdfs/' . __('dynamic.pdf-files.component-pdf-filename'));
        }
    }

    public function downloadEmission() {
        if (!Auth::check()) {
            throw new AuthenticationException();
        }
        if (file_exists(base_path('storage/app/pdfs/' . __('dynamic.pdf-files.emission-filename')))) {
            return response()->download(base_path('storage/app/pdfs/' . __('dynamic.pdf-files.emission-filename')));
        } else {
            throw new FileNotFoundException('storage/app/pdfs/' . __('dynamic.pdf-files.emission-filename'));
        }
    }

    

    public function toolsContinent($continent_id = null) {
        if (!Auth::check()) {
            return redirect(route(__('routes.home')));
        }

        // if (!session('continent_iddsd')) {
        //     return redirect(route(__('routes.home')));
        // }

        // if (!session('continent_iddsd')) {
        //     return redirect(route(__('routes.home')));
        // }
       
        Session::put('continent_id', $continent_id);

        $locale = app()->getLocale();
        $base_l = explode('_', $locale)[0];
        $locale = Locale::where('code', $locale)->first();
        if (empty($locale)) {
            $locale = Locale::where('code', $base_l)->first();
        }

        // $country_profiles = Profile::select('country_id')->where('country_id', '<>', env('APP_EUROPE_ID'))->groupBy('country_id')->get();
        $country_profiles = Profile::join('countries', 'countries.id', '=', 'profiles.country_id')
            ->join('regions', 'regions.id', '=', 'countries.region_id')
            ->join('continents', 'continents.id', '=', 'regions.continent_id')
            ->where('continents.id', $continent_id)
            ->select('country_id')->where('country_id', '<>', env('APP_EUROPE_ID'))->groupBy('country_id')->get();
        $c_ids = [];

        foreach ($country_profiles as $profile) {
            $c_ids[] = $profile->country_id;
        }
        $countries = Country::whereIn('id', $c_ids)->get();

        if (empty($country)) {
            return view('dynamic', [
                'tab' => 1,
                'countryList' => $countries
            ]);
        }
    }
}
