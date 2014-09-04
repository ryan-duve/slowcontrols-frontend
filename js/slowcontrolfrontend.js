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
			console.log(readableJSONerrors);

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

/*		//make flot data object out of report
		var flot_data_object;
		flot_data_object = constructFlotObjectFromReport(report);

		//fill checkboxes
		fillCheckboxDiv(report);

		//plot it all!
		$.plot($("#placeholder"),
			data, {
				yaxis: {},
				xaxis: {
								mode: "time",
								timezone: "browser",
								timeformat:"%h:%M:%S",
								min: Date.now()-1200*1000,//120 seconds * 1000 milliseconds/second
								max: Date.now()
				},
				"lines": {"show": "true"},
				"points": {"show": "true"},
				"legend": {"position":"nw"}
			}
		);
*/

	}

	//make ajax call for report
	$.ajax({
					url:"getReport.php",
					type:"GET",
					dataType:"json",
					success: onReportReceived
	});

	//delay before polling self again
	setTimeout(poll,1000);
}
