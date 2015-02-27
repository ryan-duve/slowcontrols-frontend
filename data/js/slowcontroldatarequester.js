$(function (){
  //onload
  //populate time fields
  populateTimeFields();

  //send request for device names
  $.ajax({
    url:"getAvailableDeviceNames.php",
    type:"POST",
    dataType:"json",
    success: onDeviceNamesReceived
  });

  //bind form submission
  $("#submit").on('click',function(event){
    event.preventDefault();
    $('.graph-container').show();
    submitDataRequest();
  });

});

//on click submit button
function submitDataRequest(){
  //get timestamps
  begTimestamp=moment($('#begTime').val());
  endTimestamp=moment($('#endTime').val());

  differenceInSeconds=endTimestamp.diff(begTimestamp,'seconds');

  //if the beginning time is later than the end time
  if(differenceInSeconds < 0){
    flipTimes();
    //multiply by negative 1 to reflect the timestamp flip
    differenceInSeconds=differenceInSeconds*-1;
  }

  //nData is difference in minutes
  nData=Math.ceil(differenceInSeconds/60); //60 seconds/min

  //get devices
  var devs= $('input[name=checkboxlist]:checked').map(function() {
        return $(this).parent().text();
      }).get();
  //console.log(devs);

  //format endTimestamp
  endTimestamp=$('#endTime').val();
  //console.log('sending endTimestamp='+endTimestamp);

  $.ajax({
    url:"../getReport.php",
    type:"POST",
    data:getReportParams(nData,devs,endTimestamp),
    dataType:"json",
    success: onReportReceived
  }); 

}

//flipTimes switches the values of the input boxes
function flipTimes(){
  var temp=$('#begTime').val();
  $('#begTime').val($('#endTime').val());
  $('#endTime').val(temp);
}

function getReportParams(nData,devs,endTimestamp){
  var reportparams={};
  reportparams['incomingDevs']=devs;
  reportparams['nData']=nData;
  reportparams['endTimestamp']=endTimestamp;
  return reportparams;
}

function populateTimeFields(){
  //get new date
  currentTimestamp=moment().format('YYYY-MM-DD HH:mm:ss');
  pastTimestamp = moment().subtract(1,'minutes').format('YYYY-MM-DD HH:mm:ss');

  //set fields
  $('#begTime').val(pastTimestamp);
  $('#endTime').val(currentTimestamp);

}

function onDeviceNamesReceived(devNames){
  makeListFromDevNames(devNames);
}

//makes select list of devices
function makeListFromDevNames(devNames){
  devNames.forEach(function(dev){
    var label=$('<label />',{
      "css":{
        'display':'inline-block',
        'margin':'10px',
      }
    })
      .appendTo('#devNameSelectionWrapper');

    $('<input />',{
      type:'checkbox',
      value:dev,
      name:'checkboxlist',
      id:'dev'+dev,
    }).appendTo(label);

    $('<span/>',{
      'text':dev,
    }).appendTo(label);


  });
}
