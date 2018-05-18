<?php
/**
 * Sentry Error Handler.
 * Push of error events into the Sentry DSN and if i development show the error
 * messages to the screen
 */
$container = $app->getContainer();
$GLOBALS['isShowingErrors'] = $container->get('settings')['displayErrorDetails'];

/* PHP Error Handler */
$container['phpErrorHandler'] = function($container) {
    return new \monitor\models\errors\SentryErrorHandler();
};

/* PHP Error Notices & Warns */
$container['errorHandler'] = function ($c) {
    return new \monitor\models\errors\SentryErrorHandler();
};


\monitor\models\errors\SentryErrorHandler::initialiseBaseErrors();
