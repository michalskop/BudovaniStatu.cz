/**
* javascript for 'daily bread' application
*/

//GLOBAL VARIABLES
var min_income = 10000; //must be >0
var max_income = 100000; //must be >0
var step = 1000;
var default_income = 25000;
var min_income_ln = Math.log(min_income);
var max_income_ln = Math.log(max_income);

//income -> tax function
function income2tax(income) {
  return 12*income*0.62;
}

//load radio styling
$(function() {
  $(".db-time-period").buttonset();
});

//tax info dialog
$(function() {
  $( "#db-tax-info-dialog" ).dialog({autoOpen:false,modal:true});
  $("#db-tax-info").click(function() {
    $( "#db-tax-info-dialog" ).dialog('open');
  });
});

$(function() {
  //set slider
  if (($("#db-income-field-text").val() <= max_income) && ($("#db-income-field-text").val() >= min_income))
    current_value = $("#db-income-field-text").val();
  else
    current_value = default_income;
  //logarithmic:
  //$( "#db-slider" ).slider({value:value2slider(current_value,min_income,max_income)});
  //linear:
  $( "#db-slider" ).slider({value:current_value,min:min_income,max:max_income,step:step});
  //set income field
  $("#db-income-field-text").val(parseFloat(slider2value($( "#db-slider" ).slider("option","value"),min_income,max_income)).toFixed(0));
  //recalculate
  recalculate();
  
});

$(function() {
  //on slider change
  $("#db-slider").slider({
    change: function(event,ui) {$("#db-income-field-text").val(parseFloat(slider2value($( "#db-slider" ).slider("option","value"),min_income,max_income)).toFixed(0));
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
  //logarithmic:
  //lnx = slider_value/100*(Math.log(maxx) - Math.log(minn)) + Math.log(minn);
  //return Math.exp(lnx);
  
  //linear:
  lin = slider_value;
  return lin;
}

function value2slider(value,minn,maxx) {
  //logarithmic:
  //return 100*(Math.log(value) - Math.log(minn))/(Math.log(maxx) - Math.log(minn));
  
  //linear:
  return value;
}

function recalculate() {
  //get income and calculate tax
  var income = $('#db-income-field-text').val();
  var tax = income2tax(income);
  //set taxes
  taxYear = parseInt(tax.toFixed(0)).toLocaleString();
  taxMonth = parseInt((tax/12).toFixed(0)).toLocaleString();
  taxDay = parseInt((tax/365).toFixed(0)).toLocaleString();
  $("#db-tax-values-year-value").html(taxYear + ' Kč');
  $("#db-tax-values-month-value").html(taxMonth + ' Kč');
  $("#db-tax-values-day-value").html(taxDay + ' Kč');
  
  $.each($(".db-table-cell-list"),function(index,value) {
    coef = $(this).find("input").val() / $("input:radio[name=db-frequency]:checked").val();
    num = coef*tax;
    if (num > 10) numHtml = parseInt(num.toFixed(0)).toLocaleString() + ' Kč';
    else numHtml = parseFloat(num.toFixed(2)).toLocaleString() + ' Kč';
    $(this).children(".db-table-cell-value").html(numHtml);
  });
  
  //effect
  $(".db-recalculate-effect").animate({
    color: '#8ff'
  },500);
  $(".db-recalculate-effect").animate({
    color: '#000'
  },500);
}
/**
* checks if input is a number
* http://www.java2s.com/Code/JavaScript/Form-Control/AllowingOnlyNumbersintoaTextBox.htm
*/
function checkIt(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        status = "This field accepts numbers only."
        return false
    }
    status = ""
    return true
}
