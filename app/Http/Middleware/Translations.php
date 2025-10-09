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
        $locale = $request->segment(1);           
        $locale = strtolower((string) $locale);
        $locale2 = substr($locale, 0, 2);  

        echo "L:".$locale."- ";
        echo "L2:".$locale2."- ";

        $group_lang = $request->route()->getPrefix();
	    //$group_locale_clean = substr($group_lang, 1,2);
        echo "X:".$group_lang;
        $group_locale_clean = mb_substr($group_lang, 1, 2, 'UTF-8');
        //echo "X:".$group_locale_clean;
        app()->setLocale($this->languages[$group_locale_clean]);
        return $next($request);
    }
}
