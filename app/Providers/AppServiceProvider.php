<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'GpBuyingStatus' => 'App\Models\TripActivityTicket\GpBuyingStatus',
        ]);

        Validator::extend('before_equal', function($attribute, $value, $parameters, $validator) {
            return strtotime($validator->getData()[$parameters[0]]) >= strtotime($value);
        });
        Validator::extend('after_equal', function($attribute, $value, $parameters, $validator) {
            $day = in_array($parameters[0], ['today', 'yesterday', 'tomorrow']) ? $parameters[0] : $validator->getData()[$parameters[0]];
            return strtotime($day) <= strtotime($value);
        });
        Validator::extend('youtube_url', function($attribute, $value, $parameters, $validator) {
            $regex_pattern = "#^(?:https://)?(?:www.)?(?:youtu.be/|youtube.com/watch\?v=)([\w-]{11})?#x";
            $result = preg_match($regex_pattern, $value, $match);
            if(!isset($match[1])) return false;
            if(!$result) return false;
            return true;
        });
        Validator::extend('currency_unit',function($attribute, $value, $parameters, $validator) {
			$result = in_array($value,['TWD','JPY','HKD']);
			return $result;
		});
		Validator::extend('user_services',function($attribute, $value, $parameters, $validator) {
			$result = in_array($value,['us_translator','us_assistant','us_photographer']);
			return $result;
		});
        if ($this->app->environment() == 'local')
        {
            $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        }
        $this->app->register(\Barryvdh\Cors\ServiceProvider::class);

        $default_cur_units_arr = ['TWD','HKD', 'JPY'];
        $default_unit = 'TWD';
        if(!request()->cookie('currency_unit')){
            define('CLIENT_CUR_UNIT', $default_unit);
            Cookie::queue('currency_unit', $default_unit);
        }else{
            $cur_unit = Crypt::decrypt(Cookie::get('currency_unit'));
            if(!in_array($cur_unit, $default_cur_units_arr)){
                define('CLIENT_CUR_UNIT', $default_unit);
            }else{
                define('CLIENT_CUR_UNIT', $cur_unit);
            }
        }

        if(!request()->cookie('tz')){
            define('TZ', 'Asia/Taipei');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
