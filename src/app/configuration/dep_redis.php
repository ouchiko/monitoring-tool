<?php
/**
 * REDIS Server Handler.
 * Globally defines our REDIS instance and our refresh status
 */
$GLOBALS['redis_refresh'] = isset($_POST['refresh']) || isset($_GET['refresh']);
$GLOBALS['redis'] = new Predis\Client('tcp://data-api-redis:6379');
