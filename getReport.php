<?php

include('SlowControlReporter.php');

function getIncomingData(){
  $incomingDevList["nData"]=$_POST['nData'];
  $incomingDevList["incomingDevs"]=$_POST['incomingDevs'];
  $incomingDevList["endTimestamp"]=$_POST['endTimestamp'];
  return $incomingDevList;
}

//******************************************************************************
//START PROGRAM
//******************************************************************************

//oyvey
ini_set('memory_limit', '2048M');

//simulate incoming data
$incomingData=getIncomingData();

//instantiate new reporter
$SCR = new SlowControlReporter();

//sanitize incoming endTimestamp or fail
$cleanEndTimestamp=$SCR->sanitizeEndTimestamp($incomingData["endTimestamp"]);
$SCR->setEndTimestamp($cleanEndTimestamp);

//sanitize incoming nData or fail
$cleanNData=$SCR->sanitizeNData($incomingData["nData"]);
$SCR->setNData($cleanNData);

//add devices to be queried to reporter
$SCR->addDevices($incomingData["incomingDevs"]);

//fill report with data
$SCR->fillReportWithData();

//build query response
//foreach($SCR->getDeviceList() as $dev){
//				$SCR->queryDatabase($dev);
//}

//echo query response
$SCR->echoReport();
