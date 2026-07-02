<?php

namespace App\Http\Middleware;

use Closure;

class InputSanitizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();
        if ($input) {
            array_walk_recursive($input, function (&$item) {
                $item = htmlspecialchars(strip_tags($item), ENT_QUOTES, 'UTF-8');
                $item = ($item == "") ? null : $item;
            });
            $request->merge($input);
        }

        return $next($request);
    }
}
