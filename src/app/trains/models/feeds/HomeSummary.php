<?php
namespace trains\models\feeds;

class HomeSummary {

    /**
     * Summary data sets
     * 
     * @param <string> date
     * 
     * @return <object>
     */
    public function loadSummaryDataSetForToday($date) {
        $summary = $GLOBALS['db']->queryRow(sprintf(
            "SELECT 
                COUNT(*) AS services_monitored,
                SUM(IF(base_status='CANCELLED',1,0)) AS cancelled_services,
                SUM(IF((base_status='DELAYED' OR base_status='LATE'),1,0)) AS delayed_services,
                SUM(IF(delay_time>0,delay_time,0)) AS minutes_delayed
            FROM 
                trains.departurewatch
            WHERE 
                DATE(last_update) = '%s'",
            addslashes($date)
        ));
        
        $summary->cancelled_as_percentage = round(($summary->cancelled_services/$summary->services_monitored) * 100,2);
        $summary->delayed_as_percentage = round(($summary->delayed_services/$summary->services_monitored) * 100,2);
          
        return $summary;
    }

    /**
     * Cancelled services for the day
     * 
     * @param <tring> date
     * 
     * @return <array>
     */
    public function loadCancelledServices($date) {
        $cancelled = $GLOBALS['db']->queryRows(sprintf(
            "SELECT * FROM trains.departurewatch WHERE DATE(last_update) = '2018-07-03' AND was_cancelled = 'YES'"
        ));
        return $cancelled;
    }

    /**
     * The 20 most cancelled routes
     * 
     * @param <string> date
     * 
     * @return <array>
     */
    public function mostCancelledRoutes($date) {
        $cancelled = $GLOBALS['db']->queryRows(sprintf(
            "SELECT route_id, scheduled_time, origin, destination, base_status, 
            COUNT(*) AS cancelled_times FROM trains.departurewatch WHERE 
            was_cancelled = 'YES' GROUP BY route_id ORDER BY COUNT(*) DESC LIMIT 0,20"
        ));
        return $cancelled;
    }
}