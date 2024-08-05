<?php
declare(strict_types=1);

namespace Services;

use Controllers\ContactController;
use Dice\Dice;
use Models\City;
use Models\Contact;
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

        // Configure Dice to inject the PDO dependency into Contact and City models
        $this->dice = $this->dice->addRule(Contact::class, [
            'constructParams' => [[Dice::INSTANCE => PDO::class]]
        ]);

        $this->dice = $this->dice->addRule(City::class, [
            'constructParams' => [[Dice::INSTANCE => PDO::class]]
        ]);

        // Configure Dice to inject Contact and City models into ContactController
        $this->dice = $this->dice->addRule(ContactController::class, [
            'constructParams' => [
                [Dice::INSTANCE => Contact::class],
                [Dice::INSTANCE => City::class]
            ]
        ]);
    }
}
