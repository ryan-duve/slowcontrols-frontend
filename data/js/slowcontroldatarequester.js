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
    //wrapping div (for display:block)
    $('<div/>',{
      id:"radioLabelWrapper-"+dev,
    }).appendTo('#devNameSelectionForm');

    $('<input>',{
      type:'radio',
      value:'devNameRadioButton-'+dev,
      class:'devNameRadioButton',
      name:'devNameRadioButton',
    }).appendTo('#radioLabelWrapper-'+dev);

    //the label
    $('<label />',{
      text:dev,
      for:'devNameRadioButton-'+dev,
      class:'devNameLabel',
    }).appendTo('#radioLabelWrapper-'+dev);
  });
}