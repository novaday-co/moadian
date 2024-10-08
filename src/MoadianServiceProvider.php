<?php

namespace Novaday\Moadian;

use Illuminate\Support\ServiceProvider;

class MoadianServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/moadian.php',
            'moadian'
        );

        $this->app->bind('Novaday\Moadian\Moadian', function ($app) {

            $config = $app['config']['moadian'];

            $privateKeyPath = $config['private_key_path'] ?? storage_path('app/keys/private.pem');
            $privateKey = file_get_contents($privateKeyPath);

            $certificatePath = $config['certificate_path'] ?? storage_path('app/keys/certificate.crt');
            $certificate = file_get_contents($certificatePath);
            $certificate = str_replace("\r\n", '', $certificate);

            $baseUri = $config['base_uri'] ?? 'https://tp.tax.gov.ir/requestsmanager/api/v2/';

            return new Moadian($privateKey, $certificate, $baseUri);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/moadian.php' => config_path('moadian.php'),
        ], 'config');
    }
}
