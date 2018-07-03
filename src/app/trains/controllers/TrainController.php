<?php
namespace trains\controllers;
/**
 * CONTROLLER: Train Controller
 */
use \Slim\Http\Request;
use \Slim\Http\Response;

use \trains\models\nationalrail\OpenLDBWS;
use \trains\models\nationalrail\StationBoardProcessor;
use \trains\models\database\manage\DepatureWatchManager;

class TrainController {

    private $OpenLDBWS_KEY = "28f3639e-fce9-4a88-94a2-5fcb762260a5";

    public function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    /**
     * Homepage
     */
    public function homepage(Request $request, Response $response, $args) {
        return $response->withJSON(["hello"=>"Word"]);
    }

   

    public function summaryDataSet(Request $request, Response $response, $args) {
        return $this->container->renderer->render($response, 'summary-data.html', [
            'dataset' => $dataset
        ]);
    }

    public function departureListAsTable(Request $request, Response $response, $args) {
        $crs_code = $args['crs_code'];
        if (isset($crs_code) && strlen($crs_code) == 3) {
            $StationBoardProcessor = new StationBoardProcessor();
            $OpenLDBWS = new OpenLDBWS($this->OpenLDBWS_KEY);
            $dataset = $StationBoardProcessor->processDataSetForDelays(
                $OpenLDBWS->GetDepartureBoard(100,$crs_code)
            );
            return $this->container->renderer->render($response, 'table-departures.html', [
                'crs_code' => $crs_code,
                'dataset' => $dataset
            ]);
        } else {
           //return $this->sendDataAPIError($response, "invalid-crs-code-error");
        }
    }

   
}
