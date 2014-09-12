<?php
//knowndevices.php
//
//lists devices and their properties; the program will reject a device if it is 
//not on this list

$KNOWN_DEVICES=array(
  "evapSi"=>array(
    "displayName"=>"Evap Si",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"red",
  ),
  "sepSi"=>array(
    "displayName"=>"Sep Si",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"orange",
  ),
  "mcSi"=>array(
    "displayName"=>"MC Si",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"turquoise",
  ),
  "OVCPressure"=>array(
    "displayName"=>"OVC",
    "units"=>"mbar",
    "table"=>"slowcontrolreadings",
    "color"=>"red",
  ),
  "IVCPressure"=>array(
    "displayName"=>"IVC",
    "units"=>"mbar",
    "table"=>"slowcontrolreadings",
    "color"=>"gold",
  ),
  "he3Pressure"=>array(
    "displayName"=>"He-3",
    "units"=>"mbar",
    "table"=>"slowcontrolreadings",
    "color"=>"gray",
  ),
  "evapPressureCm330"=>array(
    "displayName"=>"Evap",
    "units"=>"mbar",
    "table"=>"slowcontrolreadings",
    "color"=>"brown",
  ),
  "evapPressureMv110"=>array(
    "displayName"=>"Evap",
    "units"=>"mbar",
    "table"=>"slowcontrolreadings",
    "color"=>"cyan",
  ),
  "sepFlow"=>array(
    "displayName"=>"Sep Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"red",
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
    "displayName"=>"Evap Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"pink",
  ),
  "100ldlevel"=>array(
    "displayName"=>"100LD",
    "units"=>"L",
    "table"=>"slowcontrolreadings",
    "color"=>"teal",
  ),
);
