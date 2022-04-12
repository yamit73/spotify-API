<?php
use Phalcon\Di\FactoryDefault;
//Required class for loader
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use GuzzleHttp\Client;
/**
 * Required classes for DB
 */
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

//Composer autoload file
require_once(BASE_PATH.'/vendor/autoload.php');

// Register an autoloader
$loader = new Loader();
/**
 * Registering controllers and models dir
 */
$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

//register namespaces
$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH.'/components',
        'App\Events' => APP_PATH.'/events'
    ]
);
$loader->register();

$container = new FactoryDefault();

/**
 * Di container for view
 */
$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
/**
 * Url container
 */
$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

/**
 * Container for config 
 * Contains neccessory variables
 */
$container->set(
    'config',
    function () {
        $file='../app/config/config.php';
        $factory=new ConfigFactory();
        return $factory->newInstance('php', $file);
    }
);

//Di container for guzzle client
$container->set(
    'client',
    function() {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.spotify.com/v1/',
        ]);
        return $client;
    },
    true
);

/**
 * Session di
 */
$container->setShared(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session->setAdapter($files);
        $session->start();
        return $session;
    }
);
//container for database connection
$container->set(
    'db',
    function () {
        $config=$this->get('config')->db;
        return new Mysql(
            [
                'host'     => $config->host,
                'username' => $config->username,
                'password' => $config->password,
                'dbname'   => $config->dbname,
            ]
        );
    }
);
//Creating object of application class
$application = new Application($container);
//Event manager
$eventsManager=new EventsManager();
$eventsManager->attach(
    'events',
    new App\Events\EventListener()
);
$application->setEventsManager($eventsManager);
$eventsManager->attach(
    'application:getAccessTokenUsingRefresh',
    new App\Events\EventListener()
);
$container->set(
    'EventsManager',
    $eventsManager
);
try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
