<?php

//getAvailableDeviceNames.php
//returns devices from knowndevices.php

include('SlowControlDataRequester.php');

//instantiate new data requester
$SCDR = new SlowControlDataRequester();

echo json_encode($SCDR->getAvailableDeviceNames());
