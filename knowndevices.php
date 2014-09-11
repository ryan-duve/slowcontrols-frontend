<?php
//knowndevices.php
//
//lists devices and their properties; the program will reject a device if it is 
//not on this list

$KNOWN_DEVICES=array(
  "evapSi"=>array(
    "displayName"=>"Evaporator Si",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
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
  "evapPressureMv110"=>array(
    "displayName"=>"Evaporator Pressure",
    "units"=>"mbar",
    "table"=>"slowcontrolreadings",
    "color"=>"cyan",
  ),
  "sepFlow"=>array(
    "displayName"=>"Separator Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"blue",
  ),
  "shieldFlow"=>array(
    "displayName"=>"Shield Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"green",
  ),
  "he3Flow"=>array(
    "displayName"=>"He-3 Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"orange",
  ),
  "evapFlow"=>array(
    "displayName"=>"Evaporator Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"pink",
  ),
);
