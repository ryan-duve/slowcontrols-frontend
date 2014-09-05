$(function() {
  //initial poll for data
  poll();
});

function fetchData() {

				function onDataReceived(series) {
								//console.log(series);
								// Load all the data in one pass; if we only got partial
								// data we could merge it with what we already have.
								var datasets = series;

								// hard-code color indices to prevent them from shifting as
								// countries are turned on/off

								var coloring=["#EDC240","#AFD8F8","#CB4B4B","#4DA74D"];
								var i = 0;
								$.each(datasets, function(key, val) {
																val.color = i;
																++i;
																});

								// insert checkboxes 
								var j = 0;
								var choiceContainer = $("#choices");
								$.each(datasets, function(key, val) {
																//console.log(val.color);
																//console.log(datasets[key]["data"][0][1]);

																if($("#id"+key).length==0){
																choiceContainer.append("<br/><div style='background-color:"+ coloring[j++] +"'><input type='checkbox' name='" + key +
																				"' checked='checked' id='id" + key + "'></input>" +
																				"<label for='id" + key + "'>"
																				+ val.label + "<div class='lastReading' id='lastReading"+key+"'></div></label></div>");
																}

																//update lastReading inside label
																//http://stackoverflow.com/questions/11832914/round-to-at-most-2-decimal-places-in-javascript#12830454
																var lastReading=parseFloat(datasets[key]["data"][0][1]).toFixed(2);
																$('#lastReading'+key).html(lastReading);
																});

								var data = [];

								choiceContainer.find("input:checked").each(function () {
																var key = $(this).attr("name");
																if (key && datasets[key]) {
																data.push(datasets[key]);
																}
																});

								$.plot($("#placeholder"), data, {
										yaxis: {},
										xaxis: { mode: "time",
															timezone: "browser",
															timeformat:"%h:%M:%S",
															min: Date.now()-1200*1000,//120 seconds * 1000 milliseconds/second
															max: Date.now()
										},
										"lines": {"show": "true"},
										"points": {"show": "true"},
										"legend":{"position":"nw"}
									});
}

$.ajax({
url: "flow.php",
type: "GET",
dataType: "json",
success: onDataReceived
});

setTimeout(fetchData, 1000);
}

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
		//check report for errors
		handleReportErrors(report);
  
    //report is a JSON object returned from SlowControlReporter, but not
    //formatted the necessary way for flot.  constructFlotDataObjectFromReport()
    //formats the report and returns an object flot can take as a $.plot paramter
    /* report format:
        {
          "devices": {
            "d0": {
              "displayName": "dee o",
              "units": "arbs",
              "data": [
                {
                  "raw_reading": "2.2968",
                  "created_at": "2014-07-03 10:22:25"
                },
                {
                  "raw_reading": "2.1643",
                  "created_at": "2014-07-03 10:22:24"
                }
              ]
            },
            "d1": {
              "displayName": "dee un",
              "units": "arbs",
              "data": [
                {
                  "raw_reading": "0.0003",
                  "created_at": "2014-07-03 10:22:25"
                },
                {
                  "raw_reading": "0.0003",
                  "created_at": "2014-07-03 10:22:24"
                }
              ]
             }
          }
        }

      flot data object format:      
      [
        {
          "label":"dee o",
          "data":[
                  ["2014-07-03 10:22:25","2.2968"],
                  ["2014-07-03 10:22:24","2.1643"]
                 ]
        },
        {
          "label":"dee o",
          "data":[
                  ["2014-07-03 10:22:25","0.0003"],
                  ["2014-07-03 10:22:24","0.0003"]
                 ]
        }
      ]
     */
    function constructFlotDataObjectFromReport(report){
      var flot_data_object={};

      //loop over all devices
      for (var dev in report.devices){
        flot_data_object[dev]={};

        //set label
        flot_data_object[dev]['label']=report.devices[dev].displayName;

        //set data
        data = report.devices[dev].data;

        //make array out of dev's JSON data in report
        var data_array=[];
        for(var i=0;i<data.length;++i){
          //make a data point (x,y) from dev report data
          data_point=[];
          data_point.push(data[i]["created_at"]);
          data_point.push(data[i]["raw_reading"]);

          //add data point to data array
          data_array.push(data_point);
        }

        //add data array to flot_data_object
        flot_data_object[dev]['data']=data_array;
      }

      console.log(JSON.stringify(flot_data_object.d1));
      console.log(flot_data_object);
      return flot_data_object;
    }

	  //make flot data object out of report
	  flot_data_object = constructFlotDataObjectFromReport(report);

		//fill checkboxes
		//fillCheckboxDiv(report);

    var fdo=[
             {
               "label":"d1",
               "color":"blue",
               "data":[
                       ["1409934293000","1"],
                       ["1409934294000","2"],
                       ["1409934295000","3"],
                       ["1409934296000","4"],
                       ["1409934297000","5"],
                       ["1409934298000","6"],
                       ["1409934299000","7"]
                      ]
             }
            ];//test object to get flot working

		//plot it all!
		$.plot($("#placeholder"),fdo, {
				yaxis: {},
				xaxis: {
								mode: "time",
								timezone: "browser",
								timeformat:"%h:%M:%S",
								min: Date.now()-30*60*1000,//hours*minutes*seconds * 1000 milliseconds/second
								max: Date.now()
				},
				"lines": {"show": "true"},
				"points": {"show": "true"},
				"legend": {"position":"nw"}
			});
	}

	//make ajax call for report
	$.ajax({
					url:"getReport.php",
					type:"GET",
					dataType:"json",
					success: onReportReceived
	});

	//delay before polling self again
	setTimeout(poll,5000);
}
