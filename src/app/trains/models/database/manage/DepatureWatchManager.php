<?php
namespace trains\models\database\manage;

use \trains\models\audit\AuditLog;

class DepatureWatchManager {
    
    public function delete($daily_route_id) {
        $GLOBALS['db']->query(sprintf(
            "DELETE FROM trains.departurewatch WHERE daily_route_id = '%s'",
            addslashes($daily_route_id)
        ));
    }

    public function insert($crs_code, $record) {
        $destination = is_array($record->destination) ? $record->destination[0]->location->crs : $record->destination->location->crs;

        $GLOBALS['db']->query(sprintf(
            "INSERT INTO trains.departurewatch SET crs_code = '%s',
            route_id = '%s', daily_route_id = '%s', scheduled_time = '%s',
            estimated_time = '%s', platform = '%s', carriages = '%s',
            origin = '%s', destination = '%s', delay_time = '%s', base_status = '%s',
            color_flag = '%s', was_delayed = '%s', was_cancelled = '%s', last_update = NOW()",
            addslashes($crs_code),
            addslashes($record->myServiceID),
            addslashes($record->myDailyServiceID),
            addslashes($record->std),
            addslashes($record->etd),
            addslashes($record->platform),
            addslashes($record->length),
            addslashes($record->origin->location->crs),
            addslashes($destination),
            addslashes($record->delay),
            addslashes($record->base_status),
            addslashes($record->color_flag),
            (($record->etd=="Delayed") ? "YES" : "NO"),
            (($record->isCancelled) ? "YES" : "NO")
        ));

       // print $GLOBALS['db']->error;
    }

    public function update($crs_code, $dataset) {
        AuditLog::msg("CRS CODE [$crs_code] " . count($dataset->GetStationBoardResult->trainServices->service). " services");
        foreach ($dataset->GetStationBoardResult->trainServices->service as $service) {
            $this->delete($service->myDailyServiceID);
            $this->insert($crs_code, $service);
        }
    }
}