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
    submitDataRequest();
  });

});

//on click submit button
function submitDataRequest(){
  //get timestamps
  begTimestamp=moment($('#begTime').val());
  endTimestamp=moment($('#endTime').val());

  //nData is difference in minutes
  differenceInSeconds=endTimestamp.diff(begTimestamp,'seconds');
  nData=Math.ceil(differenceInSeconds/60); //60 seconds/min

  console.log('nData='+nData);

  //get device
  dev=$("input:radio[name=devNameRadioButton]:checked").val();

  //format endTimestamp
  endTimestamp=$('#endTime').val();
  console.log('sending endTimestamp='+endTimestamp);

  $.ajax({
    url:"../getReport.php",
    type:"POST",
    data:getReportParams(nData,dev,endTimestamp),
    dataType:"json",
    success: onReportReceived
  }); 

}

function getReportParams(nData,dev,endTimestamp){
  var reportparams={};
  reportparams['incomingDevs']=[];
  reportparams['incomingDevs'].push(dev);
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
  makeRadioListFromDevNames(devNames);

  $("input:radio[name=devNameRadioButton]:first").attr('checked', true);
}

//makes radio list of devices
function makeRadioListFromDevNames(devNames){
  devNames.forEach(function(dev){
    //the label
    $('<label />',{
    text:dev,
    id:'devNameLabel-'+dev,
    class:'radio',
    }).appendTo('#devNameSelectionWrapper');

    $('<input>',{
      type:'radio',
      value:dev,
      name:'devNameRadioButton',
    }).appendTo('#devNameLabel-'+dev);
  });
}
