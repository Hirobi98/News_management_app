<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->resolving('db', function ($db) {
            $db->extend('oracle', function ($config, $name) {
                $servername = $config['host'] ?? 'localhost';
                $port = $config['port'] ?? '1521';
                $dbname = $config['database'] ?? 'xe';
                $username = $config['username'] ?? '';
                $password = $config['password'] ?? '';
                
                $tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$servername)(PORT=$port))(CONNECT_DATA=(SERVICE_NAME=$dbname)))";
                
                $pdo = new \PDO("oci:dbname=" . $tns, $username, $password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
                
                return new \Illuminate\Database\Connection($pdo, $config['database'], $config['prefix'] ?? '', $config);
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
