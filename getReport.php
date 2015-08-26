<?php

include('SlowControlReporter.php');

function getIncomingData(){
  $incomingDevList["nData"]=$_POST['nData'];
  $incomingDevList["incomingDevs"]=$_POST['incomingDevs'];
  $incomingDevList["begTimestamp"]=$_POST['begTimestamp'];
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

//sanitize incoming nData or fail
$cleanNData=$SCR->sanitizeNData($incomingData["nData"]);
$SCR->setNData($cleanNData);

//sanitize incoming timestamps or fail
$cleanBegTimestamp=$SCR->sanitizeTimestamp($incomingData["begTimestamp"]);
$SCR->setBegTimestamp($cleanBegTimestamp);
$cleanEndTimestamp=$SCR->sanitizeTimestamp($incomingData["endTimestamp"]);
$SCR->setEndTimestamp($cleanEndTimestamp);

//add devices to be queried to reporter
$SCR->addDevices($incomingData["incomingDevs"]);

//fill report with data
$SCR->fillReportWithData();

//echo query response
$SCR->echoReport();
