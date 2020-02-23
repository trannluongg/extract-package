<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 23/2/2020
 * Time: 9:26 PM
 */
namespace Luongtv\Extract;

use Dompdf\Dompdf;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Luongtv\Extract\core\PDF_v2;

class ExtractServiceProvider extends IlluminateServiceProvider
{
    protected $defer = false;

    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen') === true;
    }

    public function boot(){
        $this->loadRoutesFrom(__DIR__. '/routes/web.php');
        $this->loadViewsFrom(__DIR__ .'/views', 'extract');
        $this->mergeConfigFrom(__DIR__ . '/core/config/extract.php','extract');
        $this->publishes([
            __DIR__ . '/core/config/extract.php' => config_path('extract.php'),
        ]);
        if (! $this->isLumen()) {
            $configPath = __DIR__.'/core/config/dompdf.php';
            $this->publishes([$configPath => config_path('dompdf.php')], 'config');
        }
    }

    public function register()
    {
        $configPath = __DIR__.'/core/config/dompdf.php';
        $this->mergeConfigFrom($configPath, 'dompdf');

        $this->app->bind('dompdf.options', function(){
            $defines = $this->app['config']->get('dompdf.defines');

            if ($defines) {
                $options = [];
                foreach ($defines as $key => $value) {
                    $key = strtolower(str_replace('DOMPDF_', '', $key));
                    $options[$key] = $value;
                }
            } else {
                $options = $this->app['config']->get('dompdf.options');
            }

            return $options;

        });

        $this->app->bind('dompdf', function() {

            $options = $this->app->make('dompdf.options');
            $dompdf = new Dompdf($options);
            $dompdf->setBasePath(realpath(base_path('public')));

            return $dompdf;
        });
        $this->app->alias('dompdf', Dompdf::class);

        $this->app->bind('dompdf.wrapper', function ($app) {
            return new PDF_v2($app['dompdf'], $app['config'], $app['files'], $app['view']);
        });
    }
}