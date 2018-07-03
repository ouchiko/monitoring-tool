<?php
namespace trains\models\database\create;

use \trains\models\database\create\TableBase;

class AuditLog extends TableBase {
    public $base_database = "trains";
    public $target_table = "auditlog";
    public $table_generation = "CREATE TABLE `auditlog` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `audit_time` datetime DEFAULT NULL,
      `message` varchar(250) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
}