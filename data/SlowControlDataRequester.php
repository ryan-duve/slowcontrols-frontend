<?php

//SlowControlDataRequester
//object that handles requesting of data (which devices and amount of data) for non-programmer Hifrost members to retrieve data from MySQL

include('../SlowControlReporter.php');

class SlowControlDataRequester extends SlowControlReporter{
  protected $availableDevices=[];

  public function getAvailableDevices(){
    return $this->knownDevices;
  }

  public function getAvailableDeviceNames(){
    $availableDevices=$this->getAvailableDevices();

    return array_keys($availableDevices);
  }
}

