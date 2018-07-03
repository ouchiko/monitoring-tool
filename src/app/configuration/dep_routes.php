<?php
/**
 * Train Routing
 */
/** MAP VISIT DATA - CALLABLES FROM PRODUCTS **/
// $app->post("/v1/maps/visits", \hotelmap\controllers\MapDataController::class.':getVisitDataForMaps')->add($authMiddleware);
// $app->get("/v1/maps/visits/{mapid}", \hotelmap\controllers\MapDataController::class.':getVisitDataForMaps')->add($authMiddleware);
// $app->post("/v1/maps/no_bookings/{days}", \hotelmap\controllers\MapDataController::class.':getNoBookingsForEvents')->add($authMiddleware);


/************************************************************************************************************************
 * Web pages
 ************************************************************************************************************************/

/* Homepage */
$app->get("/", \trains\controllers\TrainController::class.':homepage');
/* Summary of specific day */
$app->get("/summary/{date}", \trains\controllers\TrainController::class.':summaryDataSet');
/* Table of live routes */
$app->get("/table/{crs_code}", \trains\controllers\TrainController::class.':departureListAsTable');


/*************************************************************************************************************************
 * Cron processes.
 *************************************************************************************************************************/

/* The core departure watcher processor */
$app->get("/v1/processors/departurewatcher/{crs_code}", \trains\controllers\API::class.':getDepartureBoard');
$app->get("/v1/summary/{date}", \trains\controllers\API::class.':summaryDataSetAPI');



/*************************************************************************************************************************
 * Base Setup Process
 *************************************************************************************************************************/

/* Generates the departure watch table in a new instance */
$app->get("/v1/setup/database/create/departurewatch", \trains\controllers\SetupController::class.':createDepartureWatch');
$app->get("/v1/setup/database/create/auditlog", \trains\controllers\SetupController::class.':createAuditLog');
$app->get("/v1/setup/database/create/crscodes", \trains\controllers\SetupController::class.':createCRSCodes');