<?php
declare(strict_types=1);

namespace Services;

use Dice\Dice;
use Illuminate\Database\Capsule\Manager as Capsule;

class ServiceProvider
{
    public Dice $dice;

    public function __construct(array $ENV)
    {
        $this->dice = new Dice();

        // Configure Eloquent (Capsule)
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => $ENV['DB_ADAPTER'],
            'host' => $ENV['DB_HOST'],
            'database' => $ENV['DB_NAME'],
            'username' => $ENV['DB_USER'],
            'password' => $ENV['DB_PASS'],
            'charset' => $ENV['DB_CHARSET'],
            'collation' => $ENV['DB_COLLATION'],
            'prefix' => $ENV['DB_PREFIX'],
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

    }
}
