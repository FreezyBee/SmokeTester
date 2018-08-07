<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\StaticClass;
use RuntimeException;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SmokeTester
{
    use StaticClass;

    /** @var string */
    private static $tempDir;

    /** @var callable|null */
    private static $configuratorCallback;

    /**
     * @param string $tempDir
     * @param callable|null $configuratorCallback
     */
    public static function setup(string $tempDir, callable $configuratorCallback = null): void
    {
        self::$tempDir = $tempDir;
        self::$configuratorCallback = $configuratorCallback;
    }

    /**
     * @param array $config
     * @return Client
     */
    public static function createClient(array $config = []): Client
    {
        return new Client(
            self::createContainer($config['tempDir'] ?? null, $config['configuratorCallback'] ?? null),
            $config
        );
    }

    /**
     * @param string|null $tempDir
     * @param callable|null $configuratorCallback
     * @return Container
     */
    public static function createContainer(?string $tempDir = null, ?callable $configuratorCallback = null): Container
    {
        $tempDir = $tempDir ?? self::$tempDir;
        $configuratorCallback = $configuratorCallback ?? self::$configuratorCallback;

        if (!is_dir($tempDir)) {
            throw new RuntimeException('Please specify $tempDir in setup or by first argument');
        }

        $configurator = new Configurator();
        $configurator->setTempDirectory($tempDir);

        if (is_callable($configuratorCallback)) {
            $configuratorCallback($configurator);
        }

        return $configurator->createContainer();
    }
}
