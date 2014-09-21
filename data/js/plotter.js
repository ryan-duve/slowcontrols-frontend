//check for error in report, log to console
function handleReportErrors(report){
	
	//check for errors
	if(report.hasOwnProperty('errors')){
		//log errors
	  var readableJSONerrors = JSON.stringify(report.errors);
		//console.log(readableJSONerrors);

    //flash body background
    //it isn't very descriptive, but it has a better chance of being reported than a notification
    blinkDOM("body");

	}else{
		//console.log('no errors!');
	}
}

//blink DOM element
//http://stackoverflow.com/questions/2510115/jquery-can-i-call-delay-between-addclass-and-such#2510255
function blinkDOM(element){
  $(element).addClass("redBG").delay(500).queue(function(next){
    $(this).removeClass("redBG");
    next();
  });
}

//function for when report comes in
function onReportReceived(report){
  //debug
  console.log(report);

	//check report for errors
	handleReportErrors(report);

  //make flot data array out of report
  flot_data = constructFlotDataFromReport(report);

  //plot time
  begTimestamp=moment($('#begTime').val());
  endTimestamp=moment($('#endTime').val());
  plotMin=begTimestamp.valueOf();
  plotMax=endTimestamp.valueOf();

	//plot it all!
	$.plot($("#placeholder"),flot_data, {
			yaxis: {},
			xaxis: {
							mode: "time",
							timezone: "browser",
							timeformat:"%h:%M:%S",
							min: plotMin,
							max: plotMax
			},
			"lines": {"show": "true"},
			"points": {"show": "true"},
			"legend": {"position":"nw"}
		});
}

function constructFlotDataFromReport(report){
  //flot_data is what is passed to $.plot
  var flot_data=[];

  //flot data object is set to flot_data[0] per flot API
  var flot_data_object={};

  //loop over all devices
  for (var dev in report.devices){

    flot_data_object={};

    //set label
    flot_data_object['label']=report.devices[dev].displayName;

    //set color
    flot_data_object['color']=report.devices[dev].color;

    //set data
    data = report.devices[dev].data;

    //continue if no data
    if(data.length==0){
        console.log('No data!');
      continue;
    }

    //make array out of dev's JSON data in report
    //data_array returns as [ [u,v],[w,x],[y,z] ]
    var data_array=[];
    for(var i=0;i<data.length;++i){
      //make a data point (x,y) from dev report data
      //data_point returns as [u,v]
      data_point=[];
      data_point.push(data[i]["created_at"]);
      data_point.push(data[i]["measurement_reading"]);

      //add data point to data array
      data_array.push(data_point);
    }

    //add data array to flot_data_object
    flot_data_object['data']=data_array;

    //pack JSON object into array (necessary for flot)
    flot_data.push(flot_data_object);

  }

  //console.log(JSON.stringify(flot_data));
  return flot_data;
}
