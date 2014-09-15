$(function (){

  //onload, send request for device names
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
