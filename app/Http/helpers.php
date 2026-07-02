<?php

if (! function_exists('settings')) {

    function settings($key){

        static $settings;

        if(is_null($settings))
        {
            $settings = \Cache::remember('settings', 1560, function() {
                return \Illuminate\Support\Arr::pluck(\App\Settings::all()->toArray(), 'value', 'name');
            });
        }

        return isset($settings[$key])? $settings[$key] : null;
    }
}