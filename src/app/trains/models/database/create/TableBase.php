<?php
namespace trains\models\database\create;

use \trains\models\audit\AuditLog;

class TableBase {

    /**
     * Is the table we need to create.. already done?
     * 
     * @return <bool>
     */
    private function isTableGenerated() {
        $tables = $GLOBALS['db']->queryRows(
            sprintf("SHOW TABLES FROM %s", $this->base_database)
        );
 
        foreach ($tables as $table) {
            $table_id = "Tables_in_" . $this->base_database;
            if ($table->{$table_id} == $this->target_table) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generates the table structure 
     * 
     * @return <array>
     */
    public function generateTable() {
        if (!$this->isTableGenerated()) {
            $GLOBALS['db']->query("USE " . $this->base_database);
            $GLOBALS['db']->query($this->table_generation);
            if ($GLOBALS['db']->error) {
                return ["status" => $GLOBALS['db']->error];
            }
            AuditLog::msg("Generated {$this->target_table}");
            return ["status" => "generated"];
        } else {
            AuditLog::msg("Table {$this->target_table} already exists");
            return ["status" => "exists"];
        }
    }
}