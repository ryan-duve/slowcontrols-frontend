$(function() {

  //bind enter press on nData input to hidden field value
  $("#nData-input-text").bind("enterKey",function(e){
    //enter pressed
    //disable input value
    $("#nData-input-text").prop('disabled',true);
    //copy input value to hidden value
    $("#nData-value").val($("#nData-input-text").val());
  });

  $("#nData-input-text").keyup(function(e){
    if(e.keyCode==13){
      $(this).trigger("enterKey");
    }
  });

  //initial poll for data
  poll();
});

//poll for report
function poll() {

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
  
    //report is a JSON object returned from SlowControlReporter, but not
    //formatted the necessary way for flot.  constructFlotDataFromReport()
    //formats the report and returns an array flot can take as a $.plot paramter
    /* report format:
        {
          "devices": {
            "d0": {
              "displayName": "dee o",
              "units": "arbs",
              "data": [
                {
                  "measurement_reading": "2.2968",
                  "created_at": "2014-07-03 10:22:25"
                },
                {
                  "measurement_reading": "2.1643",
                  "created_at": "2014-07-03 10:22:24"
                }
              ]
            },
            "d1": {
              "displayName": "dee un",
              "units": "arbs",
              "data": [
                {
                  "measurement_reading": "0.0003",
                  "created_at": "2014-07-03 10:22:25"
                },
                {
                  "measurement_reading": "0.0003",
                  "created_at": "2014-07-03 10:22:24"
                }
              ]
             }
          }
        }

      flot data object format:      
      var data=[
        {
          "label":"dee o",
          "color":"blue",
          "data":[
                  ["1410179062000","2.2968"],
                  ["1410179072000","2.1643"]
                 ]
        },
        {
          "label":"dee o",
          "color":"red",
          "data":[
                  ["1410179082000","0.0003"],
                  ["1410179092000","0.0003"]
                 ]
        }
      ]
     */

	  //make flot data array out of report
	  flot_data = constructFlotDataFromReport(report);

		//update status box
    //"status box" = checkbox with last strip chart value (+units)
    //make status boxes if they do not exists
    updateStatusBoxes(report);

    //plot time
    plotTime=$("#nData-value").val();

    //enable input value
    $("#nData-input-text").prop('disabled',false);

		//plot it all!
		$.plot($("#placeholder"),flot_data, {
				yaxis: {},
				xaxis: {
								mode: "time",
								timezone: "browser",
								timeformat:"%h:%M:%S",
								min: Date.now()-plotTime*60*1000,//hours*minutes*seconds * 1000 milliseconds/second
								max: Date.now()
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
      //check if dev checkbox is unchecked (i.e., we don't want it in the data stream)
      if($('#statuscheckbox-'+dev).length){
        checked=$('#statuscheckbox-'+dev).is(':checked');
        //skip data set if checkbox isn't checked
        if(!checked){
          continue;
        }
      }


      flot_data_object={};

      //set label
      flot_data_object['label']=report.devices[dev].displayName;

      //set color
      flot_data_object['color']=report.devices[dev].color;

      //set data
      data = report.devices[dev].data;

      //continue if no data
      if(data.length==0){
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

  //return html for making a status box
  function makeStatusBox(dev){
    //status box
    $('<div/>',{
      id:'statusbox-'+dev,
      class:'statusbox'
    }).appendTo('#statusboxlist');

    //checkbox
    $('#statusbox-'+dev).append($('<input>',{
      type:'checkbox',
      id:'statuscheckbox-'+dev,
      checked:'checked'
    }));

    //reading in statusbox
    $('<span/>',{
      id:'lastreading-'+dev,
      class:'lastreading'
    }).appendTo('#statusbox-'+dev);

    //units in statusbox
    $('<span/>',{
      id:'lastreadingunits-'+dev,
      class:'lastreadingunits'
    }).appendTo('#statusbox-'+dev);

  }

  function updateStatusBoxes(report){
    //get device names
    var deviceReadings={};
    for(var dev in report.devices){
      deviceReadings[dev]=null;
    }

    //get device last data point
    for(var dev in deviceReadings){
      if(report.devices[dev]["data"].length==0){
        //if no data, last reading error 
        deviceReadings[dev]="--NaN--";
      }else{
        //set lastreading to newest data point value
        lastreading=report.devices[dev]["data"][0]["measurement_reading"];
        //format to 2 decimal points
        lastreading=parseFloat(lastreading).toFixed(2);
        deviceReadings[dev]=lastreading;
      }
    }

    //create status boxes (if doesn't exist)
    for(var dev in deviceReadings){

      //make statusbox HTML if it doesn't exist
      if($("#statusbox-"+dev).length==0){
        makeStatusBox(dev);

        //set units
        units=report.devices[dev]["units"];
        $("#lastreadingunits-"+dev).text(units);

        //set bgcolor
        bgcolor=report.devices[dev]["color"];
        $("#statusbox-"+dev).css('background',bgcolor);
      }

    }

    //update status boxes
    for(var dev in deviceReadings){
      //update last data point in status box
      updateStatusBox(dev,deviceReadings[dev]);
    }
  }

  //takes device name and reading and populates status box
  function updateStatusBox(dev,reading){
    $("#lastreading-"+dev).text(reading);
  }

  console.log(JSON.stringify(getReportParams()));

	//make ajax call for report
	$.ajax({
					url:"getReport.php",
					type:"POST",
          data:getReportParams(),
					dataType:"json",
					success: onReportReceived
	});

	//delay before polling self again
	setTimeout(poll,1000);
}
