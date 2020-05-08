<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 23/2/2020
 * Time: 9:26 PM
 */

namespace WorkableCV\Extract;

use Dompdf\Dompdf;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Str;
use WorkableCV\Extract\core\PdfWithHtml;

class ExtractServiceProvider extends IlluminateServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'extract');
        $this->publishes([
                             __DIR__ . '/../assets/fontawesome-free-5.12.1-web' => public_path('fontawesome-free-5.12.1-web'),
                             __DIR__ . '/../config/dompdf.php'                  => config_path('dompdf.php'),
                             __DIR__ . '/../config/extract.php'                 => config_path('extract.php'),
                             __DIR__ . '/../assets/fonts'                       => storage_path('fonts')
                         ]);
    }

    public function register()
    {
        $configDomPath      = __DIR__ . '/../config/dompdf.php';
        $configExtractPath  = __DIR__ . '/../config/extract.php';

        $this->mergeConfigFrom($configDomPath, 'dompdf');
        $this->mergeConfigFrom($configExtractPath, 'extract');

        $this->app->bind('dompdf.options', function ()
        {
            $defines = $this->app['config']->get('dompdf.defines');
            if ($defines)
            {
                $options = [];
                foreach ($defines as $key => $value)
                {
                    $key           = strtolower(str_replace('DOMPDF_', '', $key));
                    $options[$key] = $value;
                }
            }
            else
            {
                $options = $this->app['config']->get('dompdf.options');
            }
            return $options;
        });

        $this->app->bind('dompdf', function ()
        {
            $options = $this->app->make('dompdf.options');
            $dompdf  = new Dompdf($options);
            $dompdf->setBasePath(realpath(base_path('public')));
            return $dompdf;
        });

        $this->app->alias('dompdf', Dompdf::class);

        $this->app->bind('dompdf.wrapper', function ($app)
        {
            return new PdfWithHtml($app['dompdf'], $app['config'], $app['files'], $app['view']);
        });

        foreach (glob(__DIR__ . '/Helpers/*.php') as $filename)
        {
            require_once $filename;
        }
    }

    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen') === true;
    }
}
