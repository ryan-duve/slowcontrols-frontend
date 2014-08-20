<?php

//SlowControlReporter.php
//object that 
// * inputs requested device(s) and number of data records,
// * queries respective tables for those data records,
// * formats to JSON object and 
// * returns ajax request
//

class SlowControlReporter{

  
  protected $deviceList=[];
  protected $nData=0;
  protected $queryResponse=[];
  protected $DBH=null;//database handler
  protected $MAXNDATA=10001;

  protected $deviceTableMap=array(
    "evapSi"=>"lakeshore218s1",
    "mcSi"=>"lakeshore218s1",
    "d4"=>"usb1608g");

  public function getDeviceList(){
    return $this->deviceList;
  }

  public function getNData(){
    return $this->nData;
  }

  public function addDevice($dev){

    try{
      $this->validateDevice($dev);
    }catch (Exception $e){
      $this->processException($e);
    }

    array_push($this->deviceList,$dev);
  }

  public function validateDevice($dev){
    //device list
    if(!array_key_exists($dev,$this->deviceTableMap)){
      throw new Exception("device $dev not in deviceTableMap");
    }
  }

  public function setNData($nData){
    $this->nData=$nData;
  }

  public function getTableForDevice($dev){
    return $this->deviceTableMap[$dev];
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
    $this->validateNData($nData);

    //convert nData string to int
    $nData=$this->enforceIntNData($nData);

    //return cleaned nData
    return $nData;
  }

  public function enforceIntNData($nData){
    return (int)$nData;
  }

  public function validateNData($nData){

    try{
      //returns errors if $nData invalid
      $nDataMatchResult=preg_match('/^\d+$/',$nData);

      if($nDataMatchResult===FALSE){
        //preg_match fails
        throw new Exception('preg_match error occurred!');

      }else if($nDataMatchResult===0){
        //non-digit in nData
        throw new Exception('preg_match did not match!');

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
      echo "Error message: ",$e->getMessage(),"***<br><br>";

      //add error to query reponse
      $this->queryResponse["errors"][]=$e;

      //terminate program
      //throw $e;
  }

  public function queryDatabase($dev){
    //open SQL connection
    $this->connectToDatabase();
    
    //query database
    //STH="statement handler"
    $table=$this->getTableForDevice($dev);
     
    $STH=$this->DBH->prepare("SELECT raw_reading, created_at FROM $table WHERE device=:dev ORDER BY id DESC LIMIT :lim");
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
      
    $this->addQueryResponse($dev,$res);

    //foreach ($res as $row){
    //  echo $row["raw_reading"]."\t".$row["created_at"]."<br>";
    //}

  }

  public function addQueryResponse($dev, $res){
    $this->queryResponse["devices"][$dev]=$res;
  }

  public function getQueryResponse(){
    return $this->queryResponse;
  }

  public function getPassword(){
    include('password.php');
    return $DATABASEPASSWORD;
  }

  public function connectToDatabase(){
    //connect to DB
    $host="localhost";
    $dbname="slowcontrols";
    $user="uva";
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
  array_push($incomingDevList["incomingDevs"],'mcSi');
  array_push($incomingDevList["incomingDevs"],'d4');
  $incomingDevList["NData"]="10";
  return $incomingDevList;
}



//******************************************************************************
//START PROGRAM
//******************************************************************************
$SCR = new SlowControlReporter();

//simulate incoming data
$incomingData=fakeIncomingData();

//sanitize incoming NData or fail
$cleanNData=$SCR->sanitizeNData($incomingData["NData"]);
$SCR->setNData($cleanNData);

//add devices to be queried to reporter
foreach($incomingData["incomingDevs"] as $dev){
  $SCR->addDevice($dev);
}

//build query response
foreach($SCR->getDeviceList() as $dev){
  $SCR->queryDatabase($dev);
}

//echo query response
var_dump($SCR->getQueryResponse());
