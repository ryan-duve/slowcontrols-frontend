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
  protected $endTimestamp=0;
  protected $queryResponse=[];
  protected $report=[];
  protected $DBH=null;//database handler
  protected $MAXNDATA=100;//max minutes on strip chart
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

  public function getEndTimestamp(){
    return $this->endTimestamp;
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
    $devColor=$this->getColorForDevice($dev);

    $this->report["devices"][$dev]=array(
      "displayName"=>$devDisplayName,
      "units"=>$devUnits,
      "color"=>$devColor,
      "data"=>array()
    );
  }

  public function validateDevice($dev){
    //check device list
    if(!array_key_exists($dev,$this->knownDevices)){
      throw new Exception("device $dev not in knownDevices");
    }
  }

  public function setEndTimestamp($endTimestamp){
    $this->endTimestamp=$endTimestamp;
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

  public function getColorForDevice($dev){
    return $this->knownDevices[$dev]["color"];
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

  public function sanitizeEndTimestamp($endTimestamp){
    //check if endTimestamp is in MySQL format, else return false
    //http://stackoverflow.com/questions/11510338/regular-expression-to-match-mysql-timestamp-format-y-m-d-hms#12025632
    $regex="/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|11)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468][048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(02)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02)(-)(29)))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/";

    try{
      if(preg_match($regex,$endTimestamp)){
        //if timestamp matches mysql
        return $endTimestamp;
      }else if($endTimestamp=="now"){
        //if timestamp is "now", return current timestamp (MySQL format)
        return date('Y-m-d H:i:s');
      }else{
        throw new Exception ("endTimestamp not 'now' or MySQL valid (endTimestamp=$endTimestamp)");
      }
    }catch(Exception $e){
      $this->processException($e);
    }

    return null;
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

      //format report date to JS time ("2014-09-08 08:53:18" to 1410180798000)
      for($i=0;$i<count($res);$i++){
        $mysqlTimestamp=$res[$i]['created_at'];
        $unixTimestamp=$this->convertMysqlToJSTimestamp($mysqlTimestamp);
        $res[$i]['created_at']=$unixTimestamp;
      }

      //attach data to report
      $this->addQueryResponse($dev,$res);
    }
  }

  public function convertMysqlToJSTimestamp($mysqlTimestamp){
    return strtotime($mysqlTimestamp)*1000;
  }

  public function queryDatabase($dev){
    //open SQL connection
    $this->connectToDatabase();
    
    //query database
    //STH="statement handler"
    $table=$this->getTableForDevice($dev);
		$lim=$this->getNData();
		$endTimestamp=$this->getEndTimestamp();
     
    $STH=$this->DBH->prepare("SELECT measurement_reading, created_at FROM $table WHERE device=:dev AND (created_at BETWEEN DATE_SUB(:endTimestamp,INTERVAL :lim MINUTE) AND :endTimestamp) ORDER BY id DESC");

		//temporary query!  Uses last few entries instead of time since we are not writing to Hifrost.org yet
    //$STH=$this->DBH->prepare("SELECT measurement_reading, created_at FROM $table WHERE device=:dev  ORDER BY id DESC LIMIT :lim");
    $STH->bindParam(':dev',$dev,PDO::PARAM_STR);
    $STH->bindParam(':endTimestamp',$endTimestamp,PDO::PARAM_STR);
    $STH->bindParam(':lim',$lim,PDO::PARAM_INT);

    //execute statement
    try{
      $STH->execute();
    }catch(PDOException $e){
      $this->processException($e);
    }

    $res=$STH->fetchAll(PDO::FETCH_ASSOC);
      
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
    print_r($this->jsonReport());
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
    //get database info from git-hidden file
		$dbinfo=$this->getDBinfo();

		$host=$dbinfo["host"];
		$dbname=$dbinfo["dbname"];
		$user=$dbinfo["user"];
		
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

	//throws a fake error
	public function fakeError($fakeErrorMsg){
		try{
			throw new Exception($fakeErrorMsg);
		}catch (Exception $e){
			$this->processException($e);
		}

	}

}
