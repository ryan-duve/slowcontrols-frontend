<?php

include('SlowControlReporter.php');

function fakeIncomingData(){
				$incomingDevList=[];
				$incomingDevList["incomingDevs"]=[];
				//array_push($incomingDevList["incomingDevs"],'evapSi');
				//array_push($incomingDevList["incomingDevs"],'mcSi');
				//array_push($incomingDevList["incomingDevs"],'IVCpressure');
				//array_push($incomingDevList["incomingDevs"],'d4');
				array_push($incomingDevList["incomingDevs"],'d0');
				$incomingDevList["nData"]="5";
				return $incomingDevList;
}



//******************************************************************************
//START PROGRAM
//******************************************************************************

//simulate incoming data
$incomingData=fakeIncomingData();

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


