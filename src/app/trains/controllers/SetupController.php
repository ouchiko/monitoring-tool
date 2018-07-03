<?php
namespace trains\controllers;
/**
 * CONTROLLER: Train Controller
 */
use \Slim\Http\Request;
use \Slim\Http\Response;


class SetupController {
    public function createDepartureWatch(Request $request, Response $response, $args) {    
        $DepartureWatch = new \trains\models\database\create\DepartureWatch();
        $dataset = $DepartureWatch->generateTable();
        return $response->withJSON($dataset);
    }
    public function createAuditLog(Request $request, Response $response, $args) {    
        $AuditLog = new \trains\models\database\create\AuditLog();
        $dataset = $AuditLog->generateTable();
        return $response->withJSON($dataset);
    }
    public function createCRSCodes(Request $request, Response $response, $args) {    
        $CRSCodes = new \trains\models\database\create\CRSCodes();
        $dataset = $CRSCodes->generateTable();
        if ($dataset['status'] == "generated") {
            $CRSCodes->fill();
        }
        return $response->withJSON($dataset);
    }
    
}