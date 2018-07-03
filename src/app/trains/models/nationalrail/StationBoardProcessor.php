<?php
namespace trains\models\nationalrail;

class StationBoardProcessor {

    private function determineFlagAndColor($item) {
        if ($item->isCancelled) {
            return ["red", "CANCELLED"];
        }
        if ($item->delay == 0) {
            return ["green", "OK"];
        } else if ($item->delay>0 && $item->delay<5) {
            return ["amber", "LATE"];
        } else if ($item->delay>=5) {
            return ["red", "DELAYED"];
        }


    }

    private function determineLateness($item) {
        if ($item->etd == "On time") {
            $delay = 0;
        } else if ($item->etd == "Delayed") {
            $actual_predicted_departure = time();
            $scheduled_departure = strtotime($item->std);
            $delay = (($actual_predicted_departure - $scheduled_departure) / 60) . "+";
            if ($delay<0) {
                $delay = "=";
            }
        } else if ($item->etd == "Cancelled") {
            $delay = -2;
        } else if (preg_match("/[0-9]{2}:[0-9]{2}/", $item->etd)) {
            $actual_predicted_departure = strtotime($item->etd);
            if (date('Hm') > date('Hm', $actual_predicted_departure)) {
                $actual_predicted_departure = date('Hm');
            }
            $scheduled_departure = strtotime($item->std);
            $delay = ($actual_predicted_departure - $scheduled_departure) / 60;
        }
        return $delay;
    }

    public function processDataSetForDelays($dataset) {
        $dataset->GetStationBoardResult->trainServices->service = array_map(
            function ($item) {
                //$item->etd = "14:01";
                $item->delay = $this->determineLateness($item);
                list($color_flag, $base_status) = $this->determineFlagAndColor($item);
                $item->color_flag = $color_flag;
                $item->base_status = $base_status;

                $item->myServiceID = md5($item->origin->location->crs.$item->destination->location->crs.$item->std);
                $item->myDailyServiceID = md5(date('Ymd').$item->origin->location->crs.$item->destination->location->crs.$item->std);
                return $item;
            },
            $dataset->GetStationBoardResult->trainServices->service
        );
       
        return $dataset;
    }
}