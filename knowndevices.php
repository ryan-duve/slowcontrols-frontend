<?php
//knowndevices.php
//
//lists devices and their properties; the program will reject a device if it is 
//not on this list

$KNOWN_DEVICES=array(
  "evapSi"=>array(
    "displayName"=>"Evaporator Si",
    "units"=>"K",
    "table"=>"lakeshore218s1",
  ),
  "mcSi"=>array(
    "displayName"=>"MC Si",
    "units"=>"K",
    "table"=>"lakeshore218s1",
  ),
  "IVCpressure"=>array(
    "displayName"=>"IVC pressure",
    "units"=>"mbar",
    "table"=>"pfeiffertpg262",
  ),
  "d4"=>array(
    "displayName"=>"dee fo'",
    "units"=>"dillobars",
    "table"=>"usb1608g",
  ),
  "d0"=>array(
    "displayName"=>"dee o",
    "units"=>"arbs",
    "table"=>"slowcontrolreadings",
  ),
  "d1"=>array(
    "displayName"=>"dee un",
    "units"=>"arbs",
    "table"=>"slowcontrolreadings",
  ),
);
