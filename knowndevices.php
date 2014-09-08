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
    "color"=>"red",
  ),
  "mcSi"=>array(
    "displayName"=>"MC Si",
    "units"=>"K",
    "table"=>"lakeshore218s1",
    "color"=>"blue",
  ),
  "IVCpressure"=>array(
    "displayName"=>"IVC pressure",
    "units"=>"mbar",
    "table"=>"pfeiffertpg262",
    "color"=>"black",
  ),
  "d4"=>array(
    "displayName"=>"dee fo'",
    "units"=>"dillobars",
    "table"=>"usb1608g",
    "color"=>"cyan",
  ),
  "d0"=>array(
    "displayName"=>"dee o",
    "units"=>"arbs",
    "table"=>"slowcontrolreadings",
    "color"=>"orange",
  ),
  "d1"=>array(
    "displayName"=>"dee un",
    "units"=>"arbs",
    "table"=>"slowcontrolreadings",
    "color"=>"green",
  ),
);
