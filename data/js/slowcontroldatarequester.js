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
  $("#submit").on('click',function(){
    console.log('submitting form');
  });

});

function populateTimeFields(){
  //get new date
  currentTimestamp= new Date();
  pastTimestamp = new Date(currentTimestamp);
  pastTimestamp.setMinutes(currentTimestamp.getMinutes()-1);

  currentTimestamp=currentTimestamp.toMysqlFormat();
  pastTimestamp=pastTimestamp.toMysqlFormat();

  //set fields
  $('#begTime').val(pastTimestamp);
  $('#endTime').val(currentTimestamp);

}

Date.prototype.toMysqlFormat = function() {
  return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate()) + " " + twoDigits(this.getUTCHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
};

function twoDigits(d) {
  if(0 <= d && d < 10) return "0" + d.toString();
  if(-10 < d && d < 0) return "-0" + (-1*d).toString();
  return d.toString();
}

function fillTimestampFieldsWithCurrentTime(){
  //get timestamp in 'YYYY-MM-DD HH:MM:SS' format

}

function onDeviceNamesReceived(devNames){
  console.log(devNames);
  makeRadioListFromDevNames(devNames);
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
      value:'devNameRadioButton-'+dev,
      name:'devNameRadioButton',
    }).appendTo('#devNameLabel-'+dev);
  });
}
