<?php

/**
 * Created by Freisicht
 * User: Zoran
 * Date: 24.10.16
 * Time: 12:00
 */

require_once '../auto_installer.php';

require_once "../vendor/autoload.php";
require_once '../config/propel/config.php';

use Slim\Container;
use Slim\Views\PhpRenderer;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/logs/app.log',
        ]
    ]
];

$container = new Container($configuration);

$container['view'] = function ($container) {
    return new PhpRenderer('../web/templates/');
};

SlimApp::setConfiguration($container);

require_once "../routes/default.php";
require_once "../routes/importer.php";

SlimApp::getInstance()->run();