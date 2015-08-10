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
  "sepSiLo"=>array(
    "displayName"=>"Sep Lo",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"orange",
  ),
  "sepSiHi"=>array(
    "displayName"=>"Sep Hi",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"gray",
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
    "table"=>"alcatelasm120h",
    "color"=>"red",
  ),
  "IVCPressure"=>array(
    "displayName"=>"IVC",
    "units"=>"mbar",
    "table"=>"alcatelasm120h",
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
  "alcatelASM120H"=>array(
    "displayName"=>"Leak",
    "units"=>"mbar L/s",
    "table"=>"alcatelasm120h",
    "color"=>"orange",
  ),
  "avs47"=>array(
    "displayName"=>"AVS47",
    //"units"=>"Ω",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"purple",
  ),
  "sepSi"=>array(
    "displayName"=>"sepsi",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"black",
  ),
  "b17separatorFlow"=>array(
    "displayName"=>"Sep Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"red",
  ),
  "b17evaporatorFlow"=>array(
    "displayName"=>"Evap Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"purple",
  ),
  "b17shieldFlow"=>array(
    "displayName"=>"Shield Flow",
    "units"=>"SLPM",
    "table"=>"slowcontrolreadings",
    "color"=>"green",
  ),
  "b17He3IVCOVC"=>array(
    "displayName"=>"b17OVC",
    "units"=>"mbar",
    "table"=>"alcatelasm120h",
    "color"=>"red",
  ),
  "b17EvapPressure"=>array(
    "displayName"=>"b17Evaporator",
    "units"=>"mbar",
    "table"=>"alcatelasm120h",
    "color"=>"teal",
  ),
  "evapSiLo"=>array(
    "displayName"=>"Evap Lo",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"red",
  ),
  "evapSiMed"=>array(
    "displayName"=>"Evap Med",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"green",
  ),
  "evapSiHi"=>array(
    "displayName"=>"Evap Hi",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"brown",
  ),
  "IVCSi"=>array(
    "displayName"=>"IVC Si",
    "units"=>"K",
    "table"=>"slowcontrolreadings",
    "color"=>"purple",
  ),
  "hfResistance"=>array(
    "displayName"=>"Resistance",
    "units"=>"Ω",
    "table"=>"slowcontrolreadings",
    "color"=>"purple",
  ),
);
