<?php
declare(strict_types=1);

use FreezyBee\SmokeTester\SmokeTester;
use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

SmokeTester::setup(__DIR__ . '/tmp', function (\Nette\Configurator $configurator) {
    $configurator->addParameters([
        'appDir' => __DIR__ . '/AcmeApp',
    ]);

    $configurator->addConfig(__DIR__ . '/config.neon');
});

Environment::setup();
