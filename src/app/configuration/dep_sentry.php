<?php
/**
 * Sentry Error Handler.
 * Push of error events into the Sentry DSN and if i development show the error
 * messages to the screen
 */
use \trains\models\errors\SentryErrorHandler;

$container = $app->getContainer();
$GLOBALS['isShowingErrors'] = $container->get('settings')['displayErrorDetails'];

/* PHP Error Handler */
$container['phpErrorHandler'] = function($container) {
    return new SentryErrorHandler();
};

/* PHP Error Notices & Warns */
$container['errorHandler'] = function ($c) {
    return new SentryErrorHandler();
};


SentryErrorHandler::initialiseBaseErrors();
