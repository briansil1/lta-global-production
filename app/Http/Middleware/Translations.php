<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Translations
{
    protected $languages = [
        'es' => 'es_MX',
        'en' => 'en_US',
        'fr' => 'fr_FR',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // get element 1 
        $locale = $request->segment(1);  
        //echo $locale; // outputs "en"
        // get element 2
        $group_lang = $request->route()->getPrefix();
        $group_locale_clean = substr($group_lang, 1, 2);
        app()->setLocale($this->languages[$locale]);
        return $next($request);
    }
}
