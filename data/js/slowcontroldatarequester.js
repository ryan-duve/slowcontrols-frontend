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

  //nData is difference in minutes
  differenceInSeconds=endTimestamp.diff(begTimestamp,'seconds');
  nData=Math.ceil(differenceInSeconds/60); //60 seconds/min

  //get devices
  var devs= $('input[name=checkboxlist]:checked').map(function() {
        return $(this).parent().text();
      }).get();
  console.log(devs);

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
  makeRadioListFromDevNames(devNames);
}

//makes select list of devices
function makeRadioListFromDevNames(devNames){
 // $('<input>',{
 //   type:checkbox,
 //   id:"devSelect" 
 // })
 //   .addClass('form-control')
 //   .appendTo('#devNameSelectionWrapper');

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
