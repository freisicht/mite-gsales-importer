<?php
/**
 * Created by Freisicht
 * User: Zoran
 * Date: 24.10.16
 * Time: 12:39
 */

use Slim\App;
use Slim\Container;

class SlimApp extends App
{
    private static $instance;
    private static $configuration;

    /**
     * @param Container $configuration
     */
    public static function setConfiguration(Container $configuration)
    {
        self::$configuration = $configuration;
    }

    /**
     * @return SlimApp
     */
    public static function getInstance() : SlimApp
    {
        if (!self::$instance instanceof SlimApp) {
            self::$instance = new SlimApp(self::$configuration);
        }

        return self::$instance;
    }
}
