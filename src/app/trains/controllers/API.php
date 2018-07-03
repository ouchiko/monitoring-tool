<?php
namespace trains\controllers;
/**
 * CONTROLLER: Train Controller
 */
use \Slim\Http\Request;
use \Slim\Http\Response;

use \trains\models\feeds\HomeSummary;

use \trains\models\nationalrail\OpenLDBWS;
use \trains\models\nationalrail\StationBoardProcessor;
use \trains\models\database\manage\DepatureWatchManager;


class API {
    private $OpenLDBWS_KEY = "28f3639e-fce9-4a88-94a2-5fcb762260a5";

    public function summaryDataSetAPI(Request $request, Response $response, $args) {
        $date = $args['date'];
        $date = date('Y-m-d',strtotime($date));
        $time = strtotime($date);
        $HomeSummary = new HomeSummary();
        $dataset['summary'] = $HomeSummary->loadSummaryDataSetForToday($date);
        $dataset['cancelled'] = $HomeSummary->loadCancelledServices($date);
        $dataset['mostcancelled'] = $HomeSummary->mostCancelledRoutes($date);
        $dataset['infodata'] = [
            "date" => [
                "day" => [
                    "short" => date('j', $time),
                    "long" => date('d', $time),
                    "name" => date('l', $time),
                    "ending" => date('S', $time)
                ],
                "month" => [
                    "short" => date('n', $time),
                    "long" => date('m', $time),
                    "name" => date('F', $time)
                ],
                "year" => date('Y', $time)
            ]
        ];
        return $response->withJSON($dataset);
    }

     /**
     * Generates the depature board from the source WDSL API
     * 
     */
    public function getDepartureBoard(Request $request, Response $response, $args) {
        $crs_code = $args['crs_code'];

        if (isset($crs_code) && strlen($crs_code) == 3) {
            $StationBoardProcessor = new StationBoardProcessor();
            $OpenLDBWS = new OpenLDBWS($this->OpenLDBWS_KEY);
            $dataset = $StationBoardProcessor->processDataSetForDelays(
                $OpenLDBWS->GetDepartureBoard(100,$crs_code)
            );
            $DepatureWatchManager = new DepatureWatchManager();
            $DepatureWatchManager->update($crs_code, $dataset);

            return $response->withJSON($dataset);
        } else {
           return $this->sendDataAPIError($response, "invalid-crs-code-error");
        }
    }

    

    private function sendDataAPIError(Response $response, $message) {
        $dataset = [
            "error" => [
                "message" => $message,
                "date" => time(),
            ]
        ];
        return $response->withJSON($dataset);
    }
}