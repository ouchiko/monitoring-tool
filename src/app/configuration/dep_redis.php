<?php
/**
 * REDIS configuration.
 *
 */
$GLOBALS['redis_refresh'] = isset($_POST['refresh']) || isset($_GET['refresh']);
$GLOBALS['redis'] = new Predis\Client('tcp://data-api-redis:6379');
