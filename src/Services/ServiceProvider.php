<?php
declare(strict_types=1);

namespace Services;

use Dice\Dice;
use PDO;

class ServiceProvider
{
    public Dice $dice;

    public function __construct(array $ENV)
    {
        $this->dice = new Dice();

        // Configure PDO
        $this->dice = $this->dice->addRule(PDO::class, [
            'shared' => true,
            'constructParams' => [
                sprintf('mysql:host=%s;dbname=%s', $ENV['DB_HOST'], $ENV['DB_NAME']),
                $ENV['DB_USER'],
                $ENV['DB_PASS']
            ]
        ]);
    }
}
