<?php
namespace trains\models\audit;

class AuditLog {
    public static function msg($message) {
        $GLOBALS['db']->query(sprintf(
            "INSERT INTO trains.auditlog SET audit_time = NOW(),
            message = '%s'", addslashes($message)
        ));
    }
}