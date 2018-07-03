<?php
namespace trains\models\database\create;

use \trains\models\database\create\TableBase;

class DepartureWatch extends TableBase {
    public $base_database = "trains";
    public $target_table = "departurewatch";
    public $table_generation = "CREATE TABLE `departurewatch` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `crs_code` varchar(3) DEFAULT NULL,
      `route_id` varchar(100) DEFAULT NULL,
      `daily_route_id` varchar(100) DEFAULT NULL,
      `scheduled_time` varchar(10) DEFAULT NULL,
      `estimated_time` varchar(20) DEFAULT NULL,
      `platform` int(11) DEFAULT NULL,
      `carriages` int(11) DEFAULT NULL,
      `origin` varchar(3) DEFAULT NULL,
      `destination` varchar(3) DEFAULT NULL,
      `delay_time` int(11) DEFAULT NULL,
      `base_status` varchar(30) DEFAULT NULL,
      `color_flag` varchar(30) DEFAULT NULL,
      `was_delayed` enum('YES','NO') DEFAULT 'NO',
      `was_cancelled` enum('YES','NO') DEFAULT 'NO',
      `last_update` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `crs_code` (`crs_code`),
      KEY `route_id` (`route_id`),
      KEY `daily_route_id` (`daily_route_id`),
      KEY `last_update` (`last_update`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
}