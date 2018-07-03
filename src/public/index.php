<?php
/**
 * WATERHELL.COM
 */
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

error_reporting(E_ALL ^ E_NOTICE);
if ($_GET['debugsentry']) {
    ini_set("display_errors","on");
} else {
    ini_set("display_errors","off");
}
// error_reporting(E_ALL);
// ini_set("display_errors","on");


require __DIR__ . '/../vendor/autoload.php';    /* Vendor autoloader */
require __DIR__ . '/../app/configuration/dep_redis.php';          /* Global Redis Setup */

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../app/configuration/dep_settings.php';
$app = new \Slim\App($settings);

require __DIR__ . '/../app/configuration/dep_db.php';       /* DB Container */
//require __DIR__ . '/../app/configuration/dep_sentry.php';         /* Sentry Tracing */


require __DIR__ . '/../app/configuration/dep_render.php';   /* Dependecies */
#require __DIR__ . '/../src/middleware.php';     /* Middle ware */
require __DIR__ . '/../app/configuration/dep_routes.php';         /* Routing */

// Run app
$app->run();
