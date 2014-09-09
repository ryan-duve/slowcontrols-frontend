<?php

include('SlowControlReporter.php');

function getIncomingData(){
  $incomingDevList["nData"]=$_POST['nData'];
  $incomingDevList["incomingDevs"]=$_POST['incomingDevs'];
  return $incomingDevList;
}

//******************************************************************************
//START PROGRAM
//******************************************************************************

//simulate incoming data
$incomingData=getIncomingData();

//instantiate new reporter
$SCR = new SlowControlReporter();

//sanitize incoming nData or fail
$cleanNData=$SCR->sanitizeNData($incomingData["nData"]);
$SCR->setNData($cleanNData);

//add devices to be queried to reporter
$SCR->addDevices($incomingData["incomingDevs"]);

//fill report with data
$SCR->fillReportWithData();

//build query response
foreach($SCR->getDeviceList() as $dev){
				$SCR->queryDatabase($dev);
}

//echo query response
$SCR->echoReport();
