<?php 
use Phalcon\Config;
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;
use Phalcon\Url;
use Phalcon\Mvc\Application;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
require("../app/vendor/autoload.php");
require('../vendor/autoload.php');

$loader = new Loader();
$container= new FactoryDefault();
$loader->registerDirs(
    [
        APP_PATH . "/controllers",
        APP_PATH . "/model",
    ]
    );

$loader = new Loader();
$container->set(
    'response',
    function(){
        return new Response();
    }
);
// ------------------------------------------NameSpace Register----------------------------------------------
$loader->registerNamespaces(
    [
        'Api\Handler'=>APP_PATH.'/handlers'
    ]
    );
    $loader->register();
// ----------------------------------------------------------------------------------------------------------
// ------------------------------------------Rest------------------------------------------------------------
    $prod = new Api\Handlers\Product();
    $app = new Micro($container);
// ----------------------------------------Connecting Mongo database-----------------------------------------
    $container->set(
        'mongo',
        function(){
            $mongo = new \MongoDB\Client("mongodb://mongo",array("username"=>"root","password"=>"password123"));
        }
    );
// -----------------------------------------Setting up views-------------------------------------------------
    $container->set(
        'view',
        function(){
            $view = new View();
            $view->setViewUri(
                APP_PATH.'/views',

            );
        }
    );
//----------------------------------------------Setting up Url--------------------------------------------
    $container->set(
        'url',
        function(){
            $url = new Url();
            $url->setBaseUri(
                '/'
            );
        }
    );
// ---------------------------------Setting up the app --------------------------------------------------
    $application = new Application($container);



try{
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );
    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
