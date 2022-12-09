<?php declare(strict_types=1);

namespace Bref;

use Bref\Runtime\FileHandlerLocator;
use Closure;
use Psr\Container\ContainerInterface;
use RuntimeException;

class Bref
{
    private static ?Closure $containerProvider = null;
    private static ?ContainerInterface $container = null;

    /**
     * Configure the container that provides Lambda handlers.
     *
     * @param Closure(): ContainerInterface $containerProvider Function that must return a `ContainerInterface`.
     */
    public static function setContainer(Closure $containerProvider): void
    {
        self::$containerProvider = $containerProvider;
    }

    /**
     * @internal Used by the Bref runtime
     */
    public static function getContainer(): ContainerInterface
    {
        if (! self::$container) {
            if (self::$containerProvider) {
                self::$container = (self::$containerProvider)();
                if (! self::$container instanceof ContainerInterface) {
                    throw new RuntimeException('The closure provided to Bref\Bref::setContainer() did not return an instance of ' . ContainerInterface::class);
                }
            } else {
                self::$container = new FileHandlerLocator;
            }
        }

        return self::$container;
    }

    /**
     * @internal For tests.
     */
    public static function reset(): void
    {
        self::$containerProvider = null;
        self::$container = null;
    }
}
