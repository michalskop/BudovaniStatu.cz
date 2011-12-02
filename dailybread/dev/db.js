/**
* javascript for 'daily bread' application
*/

//GLOBAL VARIABLES
var min_income = 10000; //must be >0
var max_income = 100000; //must be >0
var min_income_ln = Math.log(min_income);
var max_income_ln = Math.log(max_income);

//income -> tax function
function income2tax(income) {
  return 12*income/3;
}

$(function() {
  //set slider
  $( "#db-slider" ).slider({value:50});
  //set income field
  $("#db-income-field-text").val(slider2value($( "#db-slider" ).slider("option","value"),min_income,max_income));
  //recalculate
  recalculate();
  
});

$(function() {
  //on slider change
  $("#db-slider").slider({
    change: function(event,ui) {$("#db-income-field-text").val((slider2value($( "#db-slider" ).slider("option","value"),min_income,max_income)));
    recalculate();
    }
  });
  //on income change
  $("#db-income-field-text").change(function() {
    $( "#db-slider" ).slider("option","value",value2slider($("#db-income-field-text").val(),min_income,max_income));
    recalculate();
  });
  //on radio change
  $("input:radio[name=db-frequency]").change(function() {
    recalculate();
  });
});


function slider2value(slider_value,minn,maxx) {
  lnx = slider_value/100*(Math.log(maxx) - Math.log(minn)) + Math.log(minn);
  return Math.exp(lnx);
}

function value2slider(value,minn,maxx) {
  return 100*(Math.log(value) - Math.log(minn))/(Math.log(maxx) - Math.log(minn));
}

function recalculate() {
  //get income and calculate tax
  var income = $('#db-income-field-text').val();
  var tax = income2tax(income);
  //set taxes
  $("#db-tax-values-year-value").html(tax);
  $("#db-tax-values-month-value").html(tax/12);
  $("#db-tax-values-day-value").html(tax/365);
  
  $.each($(".db-chapter"),function(index,value) {
    coef = $(this).children("input").val() / $("input:radio[name=db-frequency]:checked").val();
    
    $(this).children(".db-chapter-value").html(coef*tax);
  });
}


