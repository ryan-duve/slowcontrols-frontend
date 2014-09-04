<?php

//SlowControlReporter.php
//object that 
// * inputs requested device(s) and number of data records,
// * queries respective tables for those data records,
// * formats to JSON object and returns ajax request
//
// The returned object is a "report".
//

class SlowControlReporter{

  protected $nData=0;
  protected $queryResponse=[];
  protected $report=[];
  protected $DBH=null;//database handler
  protected $MAXNDATA=50;
  protected $knownDevices=null;

  //import known devices from external file
  //http://stackoverflow.com/questions/1957732/can-i-include-code-into-a-php-class#1957830
  public function __construct(){
    include('knowndevices.php');
    $this->knownDevices=$KNOWN_DEVICES;
  }

  public function getDeviceList(){
    return array_keys($this->report["devices"]);
  }

  public function getNData(){
    return $this->nData;
  }

  public function addDevices($devList){
    foreach($devList as $dev){
      $this->addDevice($dev);
    }
  }

  public function addDevice($dev){

    //see if device is known
    try{
      $this->validateDevice($dev);
    }catch (Exception $e){
      $this->processException($e);
    }

    $devDisplayName=$this->getDisplayNameForDevice($dev);
    $devUnits=$this->getUnitsForDevice($dev);

    $this->report["devices"][$dev]=array(
      "displayName"=>$devDisplayName,
      "units"=>$devUnits,
      "data"=>array()
    );
  }

  public function validateDevice($dev){
    //check device list
    if(!array_key_exists($dev,$this->knownDevices)){
      throw new Exception("device $dev not in knownDevices");
    }
  }

  public function setNData($nData){
    $this->nData=$nData;
  }

  public function getTableForDevice($dev){
    return $this->knownDevices[$dev]["table"];
  }

  public function getDisplayNameForDevice($dev){
    return $this->knownDevices[$dev]["displayName"];
  }

  public function getUnitsForDevice($dev){
    return $this->knownDevices[$dev]["units"];
  }

  public function getData($connection, $tablename, $nData){
    //mysql call
    //return blob from MySQL
  }

  public function formatDataForJSON($dataArray){
    //manipulation
    //return nice JSON object
  }

  
  public function sanitizeNData($nData){
    //make sure NData is a valid int form or fail
    $this->validatePositiveInt($nData);

    //convert nData string to int
    $nData=$this->enforceIntNData($nData);

    //enforce maximum value
    $this->validateMaxValue($nData);

    //return cleaned nData
    return $nData;
  }

  public function enforceIntNData($nData){
    return (int)$nData;
  }

  public function validateMaxValue($nData){

    try{
      if($nData > $this->MAXNDATA){
        throw new Exception("nData = $nData > $this->MAXNDATA max");
      }
    }catch (Exception $e){
      $this->processException($e);
    }
  }

  public function validatePositiveInt($nData){

    try{
      //returns errors if $nData invalid
      $nDataMatchResult=preg_match('/^\d+$/',$nData);

      if($nDataMatchResult===FALSE){
        //preg_match fails
        throw new Exception('preg_match error occurred');

      }else if($nDataMatchResult===0){
        //non-digit in nData
        throw new Exception("preg_match did not match; is $nData an int?");

      }else if((int)$nData>$this->MAXNDATA){
        //returns error if $nData greater than MAXNDATA
        throw new Exception("nData = $nData > $this->MAXNDATA");

      }
    }catch (Exception $e){
      $this->processException($e);
    }
  }

  public function processException($e){
      //temporary error report
      //echo "Error message: ",$e->getMessage(),"<br><br>";

      //add error to report
      $this->report["errors"][]=$e->getMessage();

      //terminate program
      //throw $e;
  }

  public function fillReportWithData(){
    //get devs
    $devs=$this->getDeviceList();
    
    //get data dump for each dev
    foreach($devs as $dev){
      $res=$this->queryDatabase($dev);

      //attach data to report
      $this->addQueryResponse($dev,$res);
    }
  }

  public function queryDatabase($dev){
    //open SQL connection
    $this->connectToDatabase();
    
    //query database
    //STH="statement handler"
    $table=$this->getTableForDevice($dev);
     
    $STH=$this->DBH->prepare("SELECT raw_reading, created_at FROM $table WHERE device=:dev AND (created_at BETWEEN DATE_SUB(NOW(),INTERVAL :lim SECOND) AND NOW()) ORDER BY id DESC");
    $STH->bindParam(':dev',$dev,PDO::PARAM_STR);
    $STH->bindParam(':lim',$this->getNData(),PDO::PARAM_INT);

    //execute statement
    try{
      $STH->execute();
    }catch(PDOException $e){
      $this->processException($e);
    }

    $res=$STH->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($res);
      
    return $res;

  }

  public function addQueryResponse($dev, $res){
    $this->report["devices"][$dev]["data"]=$res;
  }

//  public function getQueryResponse(){
//    return $this->queryResponse;
//  }

  public function getReport(){
    return $this->report;
  }

  public function echoReport(){
    echo "<pre>";
    print_r($this->jsonReport());
    echo "</pre>";
  }

  public function jsonReport(){
    //http://stackoverflow.com/questions/7097374/php-pretty-print-json-encode#13638998
    echo json_encode($this->getReport(),JSON_PRETTY_PRINT);
  }

  public function getPassword(){
    //put password in different file and do not track with versioning
    include('password.php');
    return $DATABASEPASSWORD;
  }

	//hide DB info from the git repo
  public function getDBinfo(){
    include('dbinfo.php');
    return $DATABASEINFO;
  }

  public function connectToDatabase(){
    //connect to DB
    //$host="localhost";
    //$dbname="slowcontrols";
    //$user="uva";

		$dbinfo=$this->getDBinfo();
		var_dump($dbinfo);
    $pass=$this->getPassword();//don't share!!

    try{
      $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
      $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }catch(PDOException $e){
      $this->processException($e);
    }
  
    $this->setDBH($DBH);
  }

  public function setDBH($DBH){
    $this->DBH=$DBH;
  }
}

function fakeIncomingData(){
  $incomingDevList=[];
  $incomingDevList["incomingDevs"]=[];
  array_push($incomingDevList["incomingDevs"],'evapSi');
  //array_push($incomingDevList["incomingDevs"],'mcSi');
  array_push($incomingDevList["incomingDevs"],'IVCpressure');
  //array_push($incomingDevList["incomingDevs"],'d4');
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

