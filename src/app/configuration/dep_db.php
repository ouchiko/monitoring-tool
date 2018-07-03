<?php
/**
 * Database setup.
 */
try {
    $db = $settings['settings']['db']["default"];
    $GLOBALS['db'] = new trains\models\database\DBMysql(
        $db['host'],
        $db['user'],
        $db['pass']
    );
} catch (\Exception $dbConnectionException) {
    print "<XMP>";
    print_r($dbConnectionException);
}
