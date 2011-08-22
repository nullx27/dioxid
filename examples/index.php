<?php
/**
 * This is the Standard index.php for Dioxid
 * Your Webserver only need to have access to this and your static
 * files (css/images/js/...).
 *
 */
namespace example; // Your Project Basenamespace

// Load the dioxid loader
require_once('path/to/dioxid/common/Loader.php');

use dioxid\Loader;

// setup the app namespace to use the loader on user written classes and the basepath for the loadding
// for each different base namespace in your app add an entry in the array in the scheme NAMESAPCE => BASEPATH
Loader::setup(array(__NAMESPACE__ => __DIR__));

//register the loader
Loader::register();

use dioxid\config\Config; // We need to read the config
use dioxid\controller\Dispatcher; // We have to dispatch the Controller/Action pair
use dioxid\controller\Router; // We Only need this if we have static routes
use dioxid\error\ErrorHandler; // And at least we want to have some Errorhandling ;)

Config::loadConfig(__DIR__ . '/config.ini',true); //Load the Config, remeber to give it the full path to your config.ini
ErrorHandler::register(); // Register the Errorhandler

// Register your static routes. Take a look at dioxid\controller\Router.php for more documentation ;)
Router::registerStaticRoutes(':lang:/index', array('index'=>'test'));

// What is your default controller and your default action? By Default this is index/index!
Dispatcher::registerIndex('index', 'test');

// Lets Dispatch the Controller/Action pair ;)
Dispatcher::dispatch();


?>