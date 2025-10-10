@extends('layouts.main', [
    'route_name' => 'routes.tools',
    'tools_country' => isset($country) ? $country : null,
    'tools_compare' => isset($compareCountry) ? $compareCountry : null,
])
@section('title', __('dynamic.title'))
@section('scriptvars')
    <script type="application/javascript">
        var _url_home = "{{ route(__('routes.home')) }}";
        var _csrf_token = "{{ csrf_token() }}";
        var _is_logged = @auth true @else false @endauth;
        var _token_form_modal = null;
        var _current_tab = '{{ $tab }}';
        var country_id = '{{ isset($country->id) && $country ? $country->id : '' }}';
        var country_name = '{{ isset($country->id) && $country ? __('countries.' . $country->name) : '' }}';
        var country_profile_compare_id = {{ $tab == 1 ? isset($compareCountry) && $compareCountry ? $compareCountry->id : '0' : '0' }};
        var country_component_compare_id = {{ $tab == 2 ? isset($compareCountry) && $compareCountry ? $compareCountry->id : '0' : '0' }};
        var country_impact_compare_id = {{ $tab == 3 ? isset($compareCountry) && $compareCountry ? $compareCountry->id : '0' : '0' }};
        var country_impact_ghg_compare_id = {{ $tab == 4 ? isset($compareCountry) && $compareCountry ? $compareCountry->id : '0' : '0' }};

        var country_compare_name = @if(isset($compareCountry) && $compareCountry) '{{ __('countries.' . $compareCountry->name) }}' @else {{ 'null' }} @endif;
        var _emission_titles = {
            'co2': '{{ __('dynamic.content.impact-tab.graph-title-co2') }}',
            'thc': '{{ __('dynamic.content.impact-tab.graph-title-thc') }}',
            'co': '{{ __('dynamic.content.impact-tab.graph-title-co') }}',
            'pm': '{{ __('dynamic.content.impact-tab.graph-title-pm') }}',
            'nox': '{{ __('dynamic.content.impact-tab.graph-title-nox') }}',
            'btx': '{{ __('dynamic.content.impact-tab.graph-title-btx') }}',
            'redii': '{{ isset($country->id) && $country ? __('countries.' . $country->name) : '' }}',
            'greet': '{{ isset($country->id) && $country ? __('countries.' . $country->name) : '' }}'
        };
        var _emissions_euro = '{{ __('dynamic.content.impact-tab.graph-euro-emissions') }}';
        var _emissions_usa = '{{ __('dynamic.content.impact-tab.graph-usa-emissions') }}';
        var _country_component_compare = {{ isset($compareCountry) && $compareCountry ? 'true' : 'false' }};
        var _component_legends = {
            'catalytic_gasoline': '{{ __('dynamic.content.component-tab.legends.catalytic_gasoline') }}',
            'reformate': '{{ __('dynamic.content.component-tab.legends.reformate') }}',
            'isomerate': '{{ __('dynamic.content.component-tab.legends.isomerate') }}',
            'alkylate': '{{ __('dynamic.content.component-tab.legends.alkylate') }}',
            'isobutane': '{{ __('dynamic.content.component-tab.legends.isobutane') }}',
            'normal_butane': '{{ __('dynamic.content.component-tab.legends.normal_butane') }}',
            'isopentane': '{{ __('dynamic.content.component-tab.legends.isopentane') }}',

            'normal_pentane': '{{ __('dynamic.content.component-tab.legends.normal_pentane') }}',
            'hydrocracked_gasoline': '{{ __('dynamic.content.component-tab.legends.hydrocracked_gasoline') }}',

            'coker_naphtha': '{{ __('dynamic.content.component-tab.legends.coker_naphtha') }}',
            'heavy_naphtha': '{{ __('dynamic.content.component-tab.legends.heavy_naphtha') }}',
            'light_primary_naphtha': '{{ __('dynamic.content.component-tab.legends.light_primary_naphtha') }}',
            'domestic_naphtha': '{{ __('dynamic.content.component-tab.legends.domestic_naphtha') }}',
            'low_octane_blendstock': '{{ __('dynamic.content.component-tab.legends.low_octane_blendstock') }}',
            'high_octane_blendstock': '{{ __('dynamic.content.component-tab.legends.high_octane_blendstock') }}',
            'mtbe': '{{ __('dynamic.content.component-tab.legends.mtbe') }}',
            'aromatics': '{{ __('dynamic.content.component-tab.legends.aromatics') }}',
            'raffinate': '{{ __('dynamic.content.component-tab.legends.raffinate') }}',
            'ethanol': '{{ __('dynamic.content.component-tab.legends.ethanol') }}',
        };
        var _getComponentImageURL = (country_name, rel) => (rel ? rel : '/') + 'images/' + country_name + '/{{ __('dynamic.images.component_comparison') }}';
        var _getComponentsListURL = (country) => '{{ route(__('routes.components-list')) }}/' + country;
        var _getEmissionsByCountryURL = (country, emission, cComparing) => '{{ route('get-emissions-by-country') }}/' + country + (emission ? '/' + emission : '') + (cComparing ? '/c/' + cComparing : '');
        var _getGasolineComponentsByCountryURL = (country, gasoline, grade, cComparing) => '{{ route('get-components-by-country') }}/' + country + (gasoline ? '/' + gasoline : '') + (grade ? '/' + grade : '') + (cComparing ? '/c/' + cComparing : '');
        var _getCountryUrl = (id, cComparition) => '{{ route(__('routes.tools')) }}/' + _current_tab + '/' + id + (cComparition ? '/' + cComparition : '');
        var _getProfilePDFUrl = () => '{{ route(__('routes.profile-pdf')) }}';
        var _getComponentsFileUrl = () => '{{ route(__('routes.components-file')) }}';
        var _getImpactFileUrl = () => '{{ route(__('routes.emission-file')) }}';
        var _getPriceUpdateResultsURL = (country, gasolineRegular, gasolinePremium, normalButane, ethanol, emtbe, btxWeighted) => '{{ route(__('routes.price-update-results')) }}/' + country + (gasolineRegular ? '/' + gasolineRegular : '') + (gasolinePremium ? '/' + gasolinePremium : '') + (normalButane ? '/' + normalButane : '') + (ethanol ? '/' + ethanol : '') + (emtbe ? '/' + emtbe : '') + (btxWeighted ? '/' + btxWeighted : '');
        var _getGhgByCountryURL = (country) => '{{ route('get-ghg-by-country') }}/' + country;
        var _get_change_continent = (continent_id) => '{{ route(__('routes.tools-continent')) }}/' + continent_id;

    </script>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.1/chart.min.js" integrity="sha512-O2fWHvFel3xjQSi9FyzKXWLTvnom+lOYR/AUEThL/fbP4hv1Lo5LCFCGuTXBRyKC4K4DJldg5kxptkgXAzUpvA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/dynamic.js') }}"></script>
    <script src="{{ asset('js/component-chart.js') }}"></script>
    <script src="{{ asset('js/impact-graphs.js') }}"></script>
    <script src="{{ asset('js/ghg-graphs.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/json2csv"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dynamic.css') }}">
@endpush

@push('scripts')
    {{-- ya tienes Chart.js cargado arriba --}}
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0"></script>

@endpush

@section('content')
    <!-- VARDUMP  de dataG1-->
    <div class="site-content-contain z-10">
        <div id="content" class="site-content">
            <div id="primary" class="content-area mg-banded-secondary-lightest mg-noise">
                <main id="main" role="main" class="site-main">
                    <div class="mg-banded-primary-darkest text-white white-links antialiased">
                    </div>
                    <div class="back-blue text-center">
                        <input type="hidden" class="form-control" id="user_locale_hidden" aria-label="user_locale_hidden" aria-describedby="user_locale_hidden" value="{{ app()->getLocale() }}">
                        <h2 class="h1 uppercase text-2xl md:text-5xl container oswald"><a href="{{ route(__('routes.home')) }}" class="text-white">{{ __('dynamic.content.profiles') }}</a></h2>
                        <label id="tool_continent"  class="text-white mt-0 pt-0 oswald" aria-label="tool_continent" aria-describedby="tool_continent">{{ $continent->name }}</label>
                        <h3 class="text-white mt-0 pt-0 oswald">
                            {{ __('dynamic.content.dynamic-tool') }}
                        </h3>
                        <!-- Agregar divs que parescan botones VOLUME & QUALITY, VEHICULAR EMISSIONS y GREEN HOUSE EMISSIONS  -->
                         <div class="container my-4">
                             <div class="dynamic-flex-area md:flex flex-wrap justify-center text-shadow">
                                /
    <!-- si {{ $tab }} == 1 resaltar como seleccionado -->

                                <a href="{{ route(__('routes.tools')) }}/1" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($tab == 1) selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                    
                                    <p class="card-p">
                                        {{ __('dynamic.content.volume-quality') }}
                                    </p>
                                </a>
                                <a href="{{ route(__('routes.tools')) }}/2" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($tab == 2) selectedBTN @endif" style="flex-direction: column;">
                                   
                                    <p class="card-p">
                                        {{ __('dynamic.content.vehicular-emissions') }}
                                    </p>
                                </a>
                                <a href="{{ route(__('routes.tools')) }}/3" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($tab == 3) selectedBTN @endif" style="flex-direction: column;">
                                    
                                    <p class="card-p">
                                        {{ __('dynamic.content.green-house-emissions') }}
                                    </p>
                                </a>
                            </div>
                        </div>
                        <!-- Divs que parescan botones AMERICA, ASIA, EUROPE, AFRICA y GLOBAL  -->
                        <div class="dynamic-flex-area md:flex flex-wrap justify-center text-shadow">
                            <!-- si tab != 2 --> 
                            @if($tab != 2 and $tab != 3)
                                <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/1"  class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($continentid == 1) selectedBTN @endif" data-value="AMERICA" style="flex-direction: column;">
                                    <div class="mx-auto w-100">
                                        <img src="{{ asset('images/map.png') }}" alt="" class="hero-sec-img">
                                    </div>
                                    <p class="card-p">
                                        {{ __('main.content.america') }} <br>
                                   
                                    </p>
                                </a>

                                <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/2" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($continentid == 2) selectedBTN @endif" data-value="EUROPE" style="flex-direction: column;">
                                    <div class="mx-auto w-100">
                                        <img src="{{ asset('images/europa2.png') }}" alt="" class="hero-sec-img">
                                    
                                    </div>
                                    <p class="card-p">
                                        {{ __('main.content.europe') }}
                                    
                                    </p>
                                </a>

                                <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/3" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($continentid == 3) selectedBTN @endif" data-value="ASIA" style="flex-direction: column;">
                                    <div class="mx-auto w-100">
                                        <img src="{{ asset('images/asia.png') }}" alt="" class="hero-sec-img">
                                        <img src="{{ asset('images/africa.png') }}" alt="" class="hero-sec-img">
                                    </div>
                                    <p class="card-p">
                                        {{ __('main.content.asia-africa') }}
                                     
                                    </p>
                                </a>
                            @endif
                        </div>
                        <!-- Regiones del continente 1 -->
                        <div class="container my-4" id="regions_container" >
                             <div class="dynamic-flex-area md:flex flex-wrap justify-center text-shadow">
                                <!-- dump regions -->
                                <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($regionid == 0) selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                    <p class="card-p">
                                        All
                                    </p>
                                </a>
                                @if(isset($regionsAll))
                                    @foreach ($regionsAll as $regionOne)
                                        <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionOne->id }}" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($regionid == $regionOne->id) selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                            <p class="card-p">
                                                {{ $regionOne->name }}
                                            </p>
                                        </a>
                                    @endforeach
                                @endif
                               
                            </div>
                        </div>
                        <!-- boton de Litros y galones si $tab == 1-->
                         @if($tab == 1)
                             <div class="flex justify-center">
                                 <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}@if($regionid)/{{ $regionid }}@else/{{0}}@endif/litros" class="mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == '_lt') selectedBTN @endif">
                                    <p class="card-p">
                                     {{ __('dynamic.content.liters') }}
                                    </p>
                                 </a>
                                 <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}@if($regionid)/{{ $regionid }}@else/{{0}}@endif/galones" class="mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == '_gl') selectedBTN @endif">
                                    <p class="card-p">
                                     {{ __('dynamic.content.gallons') }}
                                    </p>
                                 </a>
                             </div>
                         @endif
                         @if($tab == 2)
                            <div class="flex justify-center">
                                    <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/Benzene" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == 'Benzene') selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                        <p class="card-p">
                                            Benzene
                                        </p>
                                    </a>
                                    <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/CO" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == 'CO') selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                        <p class="card-p">
                                            CO
                                        </p>
                                    </a>
                                    <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/CO2" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == 'CO2') selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                        <p class="card-p">
                                            CO2
                                        </p>
                                    </a>

                                    <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/NOx" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == 'NOx') selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                        <p class="card-p">
                                            NOx
                                        </p>
                                    </a>


                                    <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/PM 2.5" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == 'PM 2.5') selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                        <p class="card-p">
                                            PM 2.5
                                        </p>
                                    </a>   


                                    <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/VOC" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl @if($type and $type == 'VOC') selectedBTN @endif" style="flex-direction: column; padding-top: 10px;">
                                        <p class="card-p">
                                            VOC
                                        </p>
                                    </a>
                            </div>
                        @endif
                        @if($tab == 3)
                        <div class="flex justify-center">
                            <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/GHG" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl" style="flex-direction: column; padding-top: 10px;">
                                <p class="card-p">
                                    GHG
                                </p>
                            </a>
                            <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/%Redvsbase" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl" style="flex-direction: column; padding-top: 10px;">
                                <p class="card-p">
                                    %Red vs base
                                </p>
                            </a>
                            <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/%RedTarget" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl" style="flex-direction: column; padding-top: 10px;">
                                <p class="card-p">
                                    %Red Target
                                </p>
                            </a>
                            <a href="{{ route(__('routes.tools')) }}/{{ $tab }}/{{ $continentid }}/{{ $regionid }}/CI" class="flex-1 mg-button mg-button--larger mg-button--tertiary mx-4 p-3 flex  items-center text-base md:text-lg lg:text-3xl" style="flex-direction: column; padding-top: 10px;">
                                <p class="card-p">
                                    CI
                                </p>
                            </a>
                        </div>
                    @endif
                    </div>
                </main>
            </div>
        </div>
        <div class="off-blue pb-5">
            <div class="container">
                <div class="row mt-4">
                    <div class="col-12">
                        <div id="continent-accordions">
                            <div id="accordion-america" class="continent-accordion ">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        @if($tab == 1)
                                            {{ __('dynamic.content.volume-quality') }}
                                        @elseif($tab == 2)
                                            {{ __('dynamic.content.vehicular-emissions') }}
                                        @elseif($tab == 3)
                                            {{ __('dynamic.content.green-house-emissions') }}
                                        @endif
                                        / {{ $continent->name }} @if($region) @if($region->name) / {{ $region->name }}  @endif  @if($type and $type == "_lt") / {{ __('dynamic.content.liters') }} @else / {{ __('dynamic.content.gallons') }} @endif @endif</h2>
                                    <div class="accordion-body">
                                        <!-- Si Tab es 1-->
                                        @if($tab == 1)
                                            <div class="container my-12">
                                                
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="oswald">Gasoline demand (MML/yr)</h3>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="oswald">Gasoline Growth (%)</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g1Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g2Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="oswald">Gasoline production (MML/yr)</h3>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="oswald">Gasoline Imports (%)</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g3Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g4Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="oswald">Ethanol demand (MML/yr)</h3>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="oswald">Octane demand (ROM)</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g5Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g6Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h3 class="oswald">Ethanol potential (MML/yr)</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g7Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                @push('scripts')
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function () {
                                                        // Grafica 1 - Gasoline demand (MML/yr) por país
                                                        const labels = @json($chartLabels ?? []);  // países
                                                        const values = @json($chartValues ?? []);  // litros
                                                        // Grafica 2 - Gasoline growth (%) por país
                                                        const labels2 = @json($chartLabels2 ?? []);  // países
                                                        const values2 = @json($chartValues2 ?? []);  // litros
                                                        // Grafica 3 - Gasoline production (MML/yr) por país
                                                        const labels3 = @json($chartLabels3 ?? []);  // países
                                                        const values3 = @json($chartValues3 ?? []);  // litros
                                                        // Grafica 4 - Gasoline import dependence (MML/yr) por país
                                                        const labels4 = @json($chartLabels4 ?? []);  // países
                                                        const values4 = @json($chartValues4 ?? []);  // litros
                                                        // Grafica 5 - 
                                                        const labels5 = @json($chartLabels5 ?? []);  // países
                                                        const values5 = @json($chartValues5 ?? []);  // litros
                                                        // Grafica 6 - 
                                                        const labels6 = @json($chartLabels6 ?? []);  // países
                                                        const values6 = @json($chartValues6 ?? []);  // litros
                                                        // Grafica 7 -
                                                        const labels7 = @json($chartLabels7 ?? []);  // países
                                                        const values7 = @json($chartValues7 ?? []);  // litros


                                                        const ctx = document.getElementById('g1Chart').getContext('2d');
                                                        const ctx2 = document.getElementById('g2Chart').getContext('2d');
                                                        const ctx3 = document.getElementById('g3Chart').getContext('2d');
                                                        const ctx4 = document.getElementById('g4Chart').getContext('2d');
                                                        const ctx5 = document.getElementById('g5Chart').getContext('2d');
                                                        const ctx6 = document.getElementById('g6Chart').getContext('2d');
                                                        const ctx7 = document.getElementById('g7Chart').getContext('2d');

                                                        new Chart(ctx, {
                                                            type: 'bar',
                                                            data: {
                                                            labels,
                                                            datasets: [{
                                                                label: 'Gasoline demand (MML/yr)',
                                                                data: values,
                                                                borderWidth: 1,
                                                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                                                borderColor: 'rgba(54, 162, 235, 1)'
                                                            }]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        @if($type == '_lt')
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()}  L`
                                                                        @else
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()}  G`
                                                                        @endif
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                }
                                                            },
                                                            scales: {
                                                                x: {                           // valores numéricos
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => Number(v).toLocaleString()
                                                                },
                                                                title: { display: true, text: @if($type == '_lt') 'Litros' @else 'Galones' @endif }
                                                                },
                                                                y: {                           // nombres de países (no convertir a número)
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            plugins: [ChartDataLabels]
                                                        });

                                                        new Chart(ctx2, {
                                                            type: 'bar',
                                                            data: {
                                                            labels: labels2,
                                                            datasets: [{
                                                                label: 'Gasoline growth (%)',
                                                                data: values3,
                                                                borderWidth: 1,
                                                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                                                borderColor: 'rgba(54, 162, 235, 1)'
                                                            }]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} %`
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => `${Number(val).toLocaleString()} %`
                                                                }
                                                            },
                                                            scales: {
                                                                x: {                           // valores numéricos
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => `${Number(v).toFixed(1)} %`
                                                                },
                                                                title: { display: true, text: '(%)' }
                                                                },
                                                                y: {                           // nombres de países (no convertir a número)
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            plugins: [ChartDataLabels]
                                                        });

                                                        new Chart(ctx3, {
                                                            type: 'bar',
                                                            data: {
                                                            labels: labels3,
                                                            datasets: [{
                                                                label: 'Gasoline production (MML/yr)',
                                                                data: values3,
                                                                borderWidth: 1,
                                                                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                                                                borderColor: 'rgba(75, 192, 192, 1)'
                                                            }]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        @if($type == '_lt')
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} L`
                                                                        @else
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} G`
                                                                        @endif
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                }
                                                            },
                                                            scales: {
                                                                x: {                           // valores numéricos
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => Number(v).toLocaleString()
                                                                },
                                                                title: { display: true, text: @if($type == '_lt') 'Litros' @else 'Galones' @endif }
                                                                },
                                                                y: {                           // nombres de países (no convertir a número)
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            plugins: [ChartDataLabels]
                                                        });

                                                        new Chart(ctx4, {
                                                            type: 'bar',
                                                            data: {
                                                            labels: labels4,
                                                            datasets: [{
                                                                label: 'Gasoline import dependence (MML/yr)',
                                                                data: values4,
                                                                borderWidth: 1,
                                                                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                                                                borderColor: 'rgba(75, 192, 192, 1)'
                                                            }]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        @if($type == '_lt')
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} L`
                                                                        @else
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} G`
                                                                        @endif
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                }
                                                            },
                                                            scales: {
                                                                x: {                           // valores numéricos
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => Number(v).toLocaleString()
                                                                },
                                                                title: { display: true, text: @if($type == '_lt') 'Litros' @else 'Galones' @endif }
                                                                },
                                                                y: {                           // nombres de países (no convertir a número)
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            plugins: [ChartDataLabels]
                                                        });

                                                        new Chart(ctx5, {
                                                            type: 'bar',
                                                            data: {
                                                            labels: labels5,
                                                            datasets: [{
                                                                label: 'Ethanol Demand (MML/yr)',
                                                                data: values5,
                                                                borderWidth: 1,
                                                                backgroundColor: 'rgba(153, 102, 255, 0.8)',
                                                                borderColor: 'rgba(153, 102, 255, 1)'
                                                            }]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        @if($type == '_lt')
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} L`
                                                                        @else
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} G`
                                                                        @endif
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                }
                                                            },
                                                            scales: {
                                                                x: {                           // valores numéricos
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => Number(v).toLocaleString()
                                                                },
                                                                title: { display: true, text: @if($type == '_lt') 'Litros' @else 'Galones' @endif }
                                                                },
                                                                y: {                           // nombres de países (no convertir a número)
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            plugins: [ChartDataLabels]
                                                        });

                                                        new Chart(ctx6, {
                                                            type: 'bar',
                                                            data: {
                                                            labels: labels6,
                                                            datasets: [{
                                                                label: 'Octane Production (MML/yr)',
                                                                data: values6,
                                                                borderWidth: 1,
                                                                backgroundColor: 'rgba(153, 102, 255, 0.8)',
                                                                borderColor: 'rgba(153, 102, 255, 1)'
                                                            }]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            plugins: {
                                                                legend: { display: false },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        @if($type == '_lt')
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} L`
                                                                        @else
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} G`
                                                                        @endif
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                }
                                                            },
                                                            scales: {
                                                                x: {                           // valores numéricos
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => Number(v).toLocaleString()
                                                                },
                                                                title: { display: true, text: @if($type == '_lt') 'Litros' @else 'Galones' @endif }
                                                                },
                                                                y: {                           // nombres de países (no convertir a número)
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            plugins: [ChartDataLabels]
                                                        });

                                                        // La siguiente grafica debera de graficar varios valores por barra de pais
                                                        new Chart(ctx7, {
                                                            type: 'bar',
                                                            data: {
                                                            labels: labels7,
                                                            datasets: [
                                                                { label: 'E0 (MML/yr)',  data: values7.map(v => v.e0_lt),  borderWidth: 1, backgroundColor: 'rgba(201,203,207,0.8)', borderColor: 'rgba(201,203,207,1)' },
                                                                { label: 'E10 (MML/yr)', data: values7.map(v => v.e10_lt), borderWidth: 1, backgroundColor: 'rgba(255,159,64,0.8)',  borderColor: 'rgba(255,159,64,1)'  },
                                                                { label: 'E15 (MML/yr)', data: values7.map(v => v.e15_lt), borderWidth: 1, backgroundColor: 'rgba(255,205,86,0.8)',  borderColor: 'rgba(255,205,86,1)'  },
                                                                { label: 'E20 (MML/yr)', data: values7.map(v => v.e20_lt), borderWidth: 1, backgroundColor: 'rgba(75,192,192,0.8)',   borderColor: 'rgba(75,192,192,1)'   },
                                                                { label: 'E25 (MML/yr)', data: values7.map(v => v.e25_lt), borderWidth: 1, backgroundColor: 'rgba(54,162,235,0.8)',  borderColor: 'rgba(54,162,235,1)'  },
                                                                { label: 'E30 (MML/yr)', data: values7.map(v => v.e30_lt), borderWidth: 1, backgroundColor: 'rgba(153,102,255,0.8)', borderColor: 'rgba(153,102,255,1)' },
                                                            ]
                                                            },
                                                            options: {
                                                            indexAxis: 'y',                 // 👈 horizontal
                                                            responsive: true,
                                                            maintainAspectRatio: false,
                                                            /*
                                                            plugins: {
                                                                legend: { display: true, position: 'top' },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        // En horizontal, el valor está en parsed.x
                                                                        label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} L`
                                                                    }
                                                                },
                                                                datalabels: {
                                                                    anchor: 'end',
                                                                    align: 'end',
                                                                    formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                }
                                                            },*/
                                                            scales: {
                                                                x: {  
                                                                stacked : true,                        
                                                                beginAtZero: true,
                                                                ticks: {
                                                                    callback: (v) => Number(v).toLocaleString()
                                                                },
                                                                title: { display: true, text: @if($type == '_lt') 'Litros' @else 'Galones' @endif }
                                                                },
                                                                y: {    
                                                                stacked : true,                        
                                                                ticks: {
                                                                    autoSkip: false,           // opcional: muestra todos
                                                                },
                                                                title: { display: true, text: 'País' }
                                                                }
                                                            }
                                                            },
                                                            //plugins: [ChartDataLabels]
                                                        });
                                                    });
                                                </script>
                                                @endpush
                                            </div>
                                        @endif
                                        <!-- Si Tab es 2-->
                                        @if($tab == 2)
                                            <!-- GRAFICAS DE VEHICULAR EMISSIONS -->
                                            <!-- Grafica 1 - Vehicular emissions (kTons/yr) por país -->
                                            <div class="container my-12">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h3 class="oswald">{{$type}} Emissions (MML/yr)</h3>
                                                    </div>
                                                    <div class="col-12">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g1Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h3 class="oswald">{{$type}} Emissions (Ton/day)</h3>
                                                    </div>
                                                    <div class="col-12">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g2Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h3 class="oswald">Vehicle Fleet (Units)</h3>
                                                    </div>
                                                    <div class="col-12">
                                                        <div style="height:400px;">   <!-- 👈 altura fija -->
                                                            <canvas id="g3Chart" height="120"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                @push('scripts')
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function () {
                                                        // --- G1 ---
                                                        const EURO6 = 3.5;   // <-- pon aquí el valor real en las MISMAS unidades del eje X
                                                        const US    = 5.0;
                                                        const labels = @json($chartLabels1 ?? []);
                                                        const raw1 = @json($chartValues1 ?? []);
                                                        const values = Array.isArray(raw1) ? raw1 : Object.values(raw1);
                                                        const n = x => (x == null ? 0 : Number(x));
                                                        const ctx1 = document.getElementById('g1Chart');
                                                        if (ctx1) {
                                                            Chart.register(window['chartjs-plugin-annotation']); // si lo cargaste por <script>
                                                            new Chart(ctx1.getContext('2d'), {
                                                                type: 'bar',
                                                                data: {
                                                                    labels,
                                                                    datasets: [
                                                                    { label: 'E0 (MML/yr)',  data: values.map(v => n(v.e0)),  borderWidth: 1, backgroundColor: 'rgba(201,203,207,0.8)', borderColor: 'rgba(201,203,207,1)' },
                                                                    { label: 'E10 (MML/yr)', data: values.map(v => n(v.e10)), borderWidth: 1, backgroundColor: 'rgba(255,159,64,0.8)',  borderColor: 'rgba(255,159,64,1)'  },
                                                                    { label: 'E15 (MML/yr)', data: values.map(v => n(v.e15)), borderWidth: 1, backgroundColor: 'rgba(255,205,86,0.8)',  borderColor: 'rgba(255,205,86,1)'  },
                                                                    { label: 'E20 (MML/yr)', data: values.map(v => n(v.e20)), borderWidth: 1, backgroundColor: 'rgba(75,192,192,0.8)',   borderColor: 'rgba(75,192,192,1)'   },
                                                                    { label: 'E25 (MML/yr)', data: values.map(v => n(v.e25)), borderWidth: 1, backgroundColor: 'rgba(54,162,235,0.8)',  borderColor: 'rgba(54,162,235,1)'  },
                                                                    { label: 'E30 (MML/yr)', data: values.map(v => n(v.e30)), borderWidth: 1, backgroundColor: 'rgba(153,102,255,0.8)', borderColor: 'rgba(153,102,255,1)' },
                                                                    ]
                                                                },
                                                                options: {
                                                                    indexAxis: 'y',
                                                                    responsive: true,
                                                                    maintainAspectRatio: false,
                                                                    scales: {
                                                                        x: {
                                                                            stacked: true,
                                                                            beginAtZero: true,
                                                                            ticks: { callback: (value) => Number(value).toLocaleString() },
                                                                            title: { display: true, text: @if($type == 'g') 'Emissions (g/km)' @else 'Emissions (Ton/day)' @endif }
                                                                        },
                                                                        y: {
                                                                            stacked: true,
                                                                            ticks: { autoSkip: false },
                                                                            title: { display: true, text: 'País' }
                                                                        }
                                                                    },
                                                                    plugins: {
                                                                        legend: { position: 'bottom' },
                                                                        annotation: {
                                                                            annotations: {
                                                                                euro6: {
                                                                                    type: 'line',
                                                                                    xMin: EURO6, xMax: EURO6,
                                                                                    borderColor: '#ff7f0e', // naranja
                                                                                    borderWidth: 2,
                                                                                    label: {
                                                                                    display: true,
                                                                                    content: 'Euro 6',
                                                                                    position: 'start',
                                                                                    rotation: -90,
                                                                                    backgroundColor: 'rgba(0,0,0,0)',
                                                                                    color: '#ff7f0e',
                                                                                    text: 'Euro 6',
                                                                                    font: {
                                                                                        size: 12,
                                                                                        weight: 'bold'
                                                                                    }
                                                                                    }
                                                                                },
                                                                                us: {
                                                                                    type: 'line',
                                                                                    xMin: US, xMax: US,
                                                                                    borderColor: '#2ca02c', // verde
                                                                                    borderWidth: 2,
                                                                                    label: {
                                                                                    display: true,
                                                                                    content: 'US',
                                                                                    position: 'start',
                                                                                    rotation: -90,
                                                                                    backgroundColor: 'rgba(0,0,0,0)',
                                                                                    color: '#2ca02c',
                                                                                    
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            });
                                                        }

                                                        // --- G2 ---
                                                        const labels2 = @json($chartLabels2 ?? []);
                                                        const raw2 = @json($chartValues2 ?? []);
                                                        const values2 = Array.isArray(raw2) ? raw2 : Object.values(raw2);

                                                        const ctx2 = document.getElementById('g2Chart');
                                                        if (ctx2) {
                                                            new Chart(ctx2.getContext('2d'), {
                                                            type: 'bar',
                                                            data: {
                                                                labels: labels2,
                                                                datasets: [
                                                                { label: 'E0 (Ton/day)',  data: values2.map(v => n(v.e0)),  borderWidth: 1, backgroundColor: 'rgba(201,203,207,0.8)', borderColor: 'rgba(201,203,207,1)' },
                                                                { label: 'E10 (Ton/day)', data: values2.map(v => n(v.e10)), borderWidth: 1, backgroundColor: 'rgba(255,159,64,0.8)',  borderColor: 'rgba(255,159,64,1)'  },
                                                                { label: 'E15 (Ton/day)', data: values2.map(v => n(v.e15)), borderWidth: 1, backgroundColor: 'rgba(255,205,86,0.8)',  borderColor: 'rgba(255,205,86,1)'  },
                                                                { label: 'E20 (Ton/day)', data: values2.map(v => n(v.e20)), borderWidth: 1, backgroundColor: 'rgba(75,192,192,0.8)',   borderColor: 'rgba(75,192,192,1)'   },
                                                                { label: 'E25 (Ton/day)', data: values2.map(v => n(v.e25)), borderWidth: 1, backgroundColor: 'rgba(54,162,235,0.8)',  borderColor: 'rgba(54,162,235,1)'  },
                                                                { label: 'E30 (Ton/day)', data: values2.map(v => n(v.e30)), borderWidth: 1, backgroundColor: 'rgba(153,102,255,0.8)', borderColor: 'rgba(153,102,255,1)' },
                                                                ]
                                                            },
                                                            options: {
                                                                indexAxis: 'y',
                                                                responsive: true,
                                                                maintainAspectRatio: false,
                                                                scales: {
                                                                x: {
                                                                    stacked: true,
                                                                    beginAtZero: true,
                                                                    ticks: { callback: (value) => Number(value).toLocaleString() },
                                                                    title: { display: true, text: @if($type == 'g') 'Emissions (g/km)' @else 'Emissions (Ton/day)' @endif }
                                                                },
                                                                y: {
                                                                    stacked: true,
                                                                    ticks: { autoSkip: false },
                                                                    title: { display: true, text: 'País' }
                                                                }
                                                                }
                                                            }
                                                            });
                                                        }
                                                        

                                                        // --- G3 ---
                                                        const labels3 = @json($chartLabels3 ?? []);
                                                        const raw3 = @json($chartValues3 ?? []);
                                                        const values3 = Array.isArray(raw3) ? raw3 : Object.values(raw3);
                                                    
                                                        const ctx3 = document.getElementById('g3Chart');
                                                        if (ctx3) {
                                                            new Chart(ctx3, {
                                                                type: 'bar',
                                                                data: {
                                                                labels: labels3,
                                                                datasets: [{
                                                                    label: 'Gasoline demand (MML/yr)',
                                                                    data: values3,
                                                                    borderWidth: 1,
                                                                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                                                    borderColor: 'rgba(54, 162, 235, 1)'
                                                                }]
                                                                },
                                                                options: {
                                                                indexAxis: 'y',                 // 👈 horizontal
                                                                responsive: true,
                                                                maintainAspectRatio: false,
                                                                plugins: {
                                                                    legend: { display: false },
                                                                    tooltip: {
                                                                        callbacks: {
                                                                            label: (ctx) => ` ${ctx.parsed.x.toLocaleString()} L`
                                                                        }
                                                                    },
                                                                    datalabels: {
                                                                        anchor: 'end',
                                                                        align: 'end',
                                                                        formatter: (val) => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                                                    }
                                                                },
                                                                scales: {
                                                                    x: {                      
                                                                    beginAtZero: true,
                                                                    ticks: {
                                                                        callback: (v) => Number(v).toLocaleString()
                                                                    },
                                                                    title: { display: true, text: @if($type == 'g') 'Emissions (g/km)' @else 'Emissions (Ton/day)' @endif}
                                                                    },
                                                                    y: {                          
                                                                    ticks: {
                                                                        autoSkip: false,    
                                                                    },
                                                                    title: { display: true, text: 'País' }
                                                                    }
                                                                }
                                                                },
                                                                plugins: [ChartDataLabels]
                                                            });
                                                        }
                                                    });
                                                </script>
                                                @endpush
                                            </div>
                                        @endif
                                        <!-- Si Tab es 3-->
                                        @if($tab == 3)  
                                         
                                            <div class="container my-12">
                                                <!-- GRAFICAS DE GREEN HOUSE EMISSIONS -->
                                                <div class="container my-12">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h3 class="oswald">Life Cycle {{$type}} Emissions (MML/yr) - REDIII</h3>
                                                        </div>
                                                        <div class="col-12">
                                                            <div style="height:400px;">   <!-- 👈 altura fija -->
                                                                <canvas id="g1Chart" height="120"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @push('scripts')
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    // --- G1 ---
                                                    const labels = @json($chartLabels1 ?? []);
                                                    const raw1 = @json($chartValues1 ?? []);
                                                    const values = Array.isArray(raw1) ? raw1 : Object.values(raw1);
                                                    const n = x => (x == null ? 0 : Number(x));
                                                    const ctx1 = document.getElementById('g1Chart');
                                                    if (ctx1) {
                                                        Chart.register(window['chartjs-plugin-annotation']); // si lo cargaste por <script>
                                                        new Chart(ctx1.getContext('2d'), {
                                                            type: 'bar',
                                                            data: {
                                                                labels,
                                                                datasets: [
                                                                { label: 'E0 (MML/yr)',  data: values.map(v => n(v.e0_em)),  borderWidth: 1, backgroundColor: 'rgba(201,203,207,0.8)', borderColor: 'rgba(201,203,207,1)' },
                                                                { label: 'E10 (MML/yr)', data: values.map(v => n(v.e10_em)), borderWidth: 1, backgroundColor: 'rgba(255,159,64,0.8)',  borderColor: 'rgba(255,159,64,1)'  },
                                                                { label: 'E15 (MML/yr)', data: values.map(v => n(v.e15_em)), borderWidth: 1, backgroundColor: 'rgba(255,205,86,0.8)',  borderColor: 'rgba(255,205,86,1)'  },
                                                                { label: 'E20 (MML/yr)', data: values.map(v => n(v.e20_em)), borderWidth: 1, backgroundColor: 'rgba(75,192,192,0.8)',   borderColor: 'rgba(75,192,192,1)'   },
                                                                { label: 'E25 (MML/yr)', data: values.map(v => n(v.e25_em)), borderWidth: 1, backgroundColor: 'rgba(54,162,235,0.8)',  borderColor: 'rgba(54,162,235,1)'  },
                                                                { label: 'E30 (MML/yr)', data: values.map(v => n(v.e30_em)), borderWidth: 1, backgroundColor: 'rgba(153,102,255,0.8)', borderColor: 'rgba(153,102,255,1)' },
                                                                ]
                                                            },
                                                            options: {
                                                                indexAxis: 'y',
                                                                responsive: true,
                                                                maintainAspectRatio: false,
                                                                scales: {
                                                                    x: {
                                                                        stacked: true,
                                                                        beginAtZero: true,
                                                                        ticks: { callback: (value) => Number(value).toLocaleString() },
                                                                        title: { display: true, text: @if($type == 'g') 'Emissions (g/km)' @else 'Emissions (Ton/day)' @endif }
                                                                    },
                                                                    y: {
                                                                        stacked: true,
                                                                        ticks: { autoSkip: false },
                                                                        title: { display: true, text: 'País' }
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    }

                                                    
                                                });
                                            </script>
                                            @endpush

                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                  </div>
            </div>
        </div>
        <div id="content" class="site-content">
            <footer id="colophon" role="contentinfo" class="relative text-white site-footer mg-banded-primary antialiased uppercase text-base border-0 z-10 sm:text-left print-break-avoid">
                <div class="wrap xl:flex items-center">
                    <div class="flex-1 text-sm leading-normal lg:leading-loose">
                        <aside role="complementary" aria-label="Footer" class="widget-area">
                            <picture>
                                <source data-lazy-srcset="{{ asset('images/grains-symbol.svg') }}" srcset="{{ asset('images/grains-symbol.svg') }}" />
                                <img src="{{ asset('images/grains-symbol.png') }}" alt="flat graphic symbolizing a grain plant" data-lazy-src="{{ asset('images/grains-symbol.png') }}" data-ll-status="loading" class="mg-icon--larger md:float-left mt-1 md:mr-4 no-print lazyloading" />
                                <noscript>
                                    <img src="{{ asset('images/grains-symbol.png') }}" alt="flat graphic symbolizing a grain plant" class="mg-icon--larger md:float-left mt-1 md:mr-4 no-print" />
                                </noscript>
                            </picture>
                            <div class="site-copyright">{{ __('main.footer.site-name') }}</div>
                            <section id="custom_html-2" class="widget_text widget widget_custom_html">
                                <h2 class="widget-title">USGC Info</h2>
                                <div class="textwidget custom-html-widget">
                                    20 F Street NW, Suite 900, Washington, DC 20001 | TEL: <a href="tel:202.789.0789">202.789.0789</a> | FAX: <a href="tel:202.898.0522">202.898.0522</a>
                                    <p>
                                        <a href="https://grains.org/privacy/">{{ __('main.footer.privacy-policy') }}</a> | <a href="https://grains.org/cookies/">{{ __('main.footer.cookies-policy') }}</a> | <a href="https://grains.org/employment/">{{ __('main.footer.eeo-policy') }}</a> | <a href="https://www.usda.gov/non-discrimination-statement">{{ __('main.footer.non-discrimination-policy') }}</a>
                                    </p>
                                </div>
                            </section>
                        </aside>
                    </div>
                    <div class="xl:w-1/3 xl:flex xl:justify-end text-sm">
                        <nav role="navigation" aria-label="Social Links Menu" class="social-navigation mt-4 xl:mt-0">
                            <ul id="menu-social" class="social-links-menu mg-hover-menu social-icons">
                                <div class="menu-social-container">
                                    <li id="menu-item-88" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-88">
                                        <a href="https://www.facebook.com/usgrainscouncil">
                                            <svg class="icon-social icon"><use xlink:href="#icon-facebook"></use></svg>
                                            <span class="screen-reader-text">Facebook</span>
                                        </a>
                                    </li>
                                    <li id="menu-item-89" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-89">
                                        <a href="http://twitter.com/usgc">
                                            <svg class="icon-social icon"><use xlink:href="#icon-twitter"></use></svg>
                                            <span class="screen-reader-text">Twitter</span>
                                        </a>
                                    </li>
                                    <li id="menu-item-91" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-91">
                                        <a href="https://www.instagram.com/usgrains/">
                                            <svg class="icon-social icon"><use xlink:href="#icon-instagram"></use></svg>
                                            <span class="screen-reader-text">Instagram</span>
                                        </a>
                                    </li>
                                    <li id="menu-item-90" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-90">
                                        <a href="http://www.youtube.com/user/USGrainsCouncil">
                                            <svg class="icon-social icon"><use xlink:href="#icon-youtube"></use></svg>
                                            <span class="screen-reader-text">Youtube</span>
                                        </a>
                                    </li>
                                    <li id="menu-item-92" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-92">
                                        <a href="https://usgrains.photoshelter.com/galleries">
                                            <svg class="icon-social icon"><use xlink:href="#icon-photoshelter"></use></svg>
                                            <span class="screen-reader-text">Photoshelter</span>
                                        </a>
                                    </li>
                                    <li id="menu-item-5106" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-5106">
                                        <a href="https://soundcloud.com/usgc">
                                            <svg class="icon-social icon"><use xlink:href="#icon-soundcloud"></use></svg>
                                            <span class="screen-reader-text">Soundcloud</span>
                                        </a>
                                    </li>
                                </div>
                            </ul>
                        </nav>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
@push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', () => {
        // Si regionid > 0 entonces {$('#regions_container1').removeClass('hidden'); $('#regions_container2, #regions_container3, #regions_container4').addClass('hidden');} 
        if ({{ $regionid ?? 0 }} > 0) {
            $('#regions_container1').removeClass('hidden');
            $('#regions_container2, #regions_container3, #regions_container4').addClass('hidden');
        }

        // Si regionid == 2 entonces {$('#regions_container2').removeClass('hidden'); $('#regions_container1, #regions_container3, #regions_container4').addClass('hidden');}
        if ({{ $regionid ?? 0 }} == 2) {
            $('#regions_container2').removeClass('hidden');
            $('#regions_container1, #regions_container3, #regions_container4').addClass('hidden');
        }

        // Si regionid == 3 entonces {$('#regions_container3').removeClass('hidden'); $('#regions_container1, #regions_container2, #regions_container4').addClass('hidden');}
        if ({{ $regionid ?? 0 }} == 3) {
            $('#regions_container3').removeClass('hidden');
            $('#regions_container1, #regions_container2, #regions_container4').addClass('hidden');
        }

        // Si regionid == 4 entonces {$('#regions_container4').removeClass('hidden'); $('#regions_container1, #regions_container2, #regions_container3').addClass('hidden');}
        if ({{ $regionid ?? 0 }} == 4) {
            $('#regions_container4').removeClass('hidden');
            $('#regions_container1, #regions_container2, #regions_container3').addClass('hidden');
        }


        $(document).on('click', '#switch_continent_america', function (e) {
            e.preventDefault();
            $('#regions_container1').removeClass('hidden');
            $('#regions_container2, #regions_container3, #regions_container4').addClass('hidden');
        });
        $(document).on('click', '#switch_continent_asia', function (e) {
            e.preventDefault();
            $('#regions_container2').removeClass('hidden');
            $('#regions_container1, #regions_container3, #regions_container4').addClass('hidden');
        });
        $(document).on('click', '#switch_continent_europe', function (e) {
            e.preventDefault();
            $('#regions_container3').removeClass('hidden');
            $('#regions_container1, #regions_container2, #regions_container4').addClass('hidden');
        });
        $(document).on('click', '#switch_continent_africa', function (e) {
            e.preventDefault();
            $('#regions_container4').removeClass('hidden');
            $('#regions_container1, #regions_container2, #regions_container3').addClass('hidden');
        });
        $(document).on('click', '#switch_continent_global', function (e) {
            e.preventDefault();
            $('#regions_container1, #regions_container2, #regions_container3, #regions_container4').addClass('hidden');
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    let selectedContinent = null;

  function showAccordion(continent) {
    // Oculta todos
    document.querySelectorAll('.continent-accordion').forEach(acc => acc.style.display = 'none');

    // Mapea el valor a ID del acordeón
    const map = {
      'AMERICA': 'accordion-america',
      'ASIA':    'accordion-asia',
      'EUROPE':  'accordion-europe',
      'AFRICA':  'accordion-africa',
      'GLOBAL':  'accordion-global'
    };

    const targetId = map[continent];
    if (targetId) {
      const el = document.getElementById(targetId);
      if (el) {
        el.style.display = 'block';
        // (Opcional) scroll suave al acordeón
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  }

  // Listeners para los botones de continente
  document.querySelectorAll('.continent-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      selectedContinent = btn.getAttribute('data-value');

      // Marca activo visualmente
      document.querySelectorAll('.continent-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Muestra acordeón correspondiente
      showAccordion(selectedContinent);

      // (Opcional) actualiza la etiqueta superior si quieres reflejar el continente seleccionado
     
    });
  });

  // Estado inicial (opcional): muestra AMERICA por defecto
  // showAccordion('AMERICA');
});
</script>
@endpush