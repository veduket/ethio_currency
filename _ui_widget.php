<div id="torex_widget">

<style media="screen">
.flag {display: inline-block;  width: 32px; height: 32px; background: url(<?php echo plugin_dir_url(__FILE__)."assets/flags/flags.png"; ?>) no-repeat; }
.flag.flag-et {background-position: 0 -128px;}
tr,th,td {vertical-align: middle !important;text-align: center !important;}
#etbuna_widget *{ font-size:8pt !important;}
#etbuna_widget table{margin:0;}
#etbuna_widget .panel-body{padding:0 !important;}
#etbuna_widget .well{margin:0px !important;}
#etbuna_widget .form-control{height:auto;}
#etbuna_widget select,input{border-radius: 5px;line-height: 20px;border-style:none;border:1px solid black;}
</style>

<div class="container-fluid clearfix" id="etbuna_widget" style="width:250px !important;overflow:hidden;">
  <div class="panel-group" id="currency_widget" >
    <div class="panel panel-success" id="widget_currency_table">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#currency_widget" href="#collapseOne" aria-expanded="false" class="collapsed">Exchange Rate</a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
        <div class="panel-body">
          <img class="img img-rounded img-responsive clearfix center-block" src='<?php echo plugin_dir_url(__FILE__)."assets/images/cbeedited.png";?>' alt="CBE" width="220px" height="40px" />
          <div id="exchange_rates"></div>
        </div>
      </div>
    </div>
    <div class="panel panel-success" id="widget_currency_calculator">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#currency_widget" href="#collapseTwo" class="collapsed" aria-expanded="false">Currency Calculator</a>
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false">
        <div class="panel-body">
          <table class='table table-striped table-bordered table-hover table-condensed'>
            <thead><tr><th>From</th><th>Amount</th></tr></thead>
            <tbody>
              <tr><td><select id="currency_list" class='form-control' style="width:100px"></select></td><td><input id="from_amount" type="number" name="from_amount" value="" style="width:100px"></td></tr>
              <tr><td colspan="2"><div class="well well-sm text-right"><strong><span>ETB </span> <span id="to_amount">0.00</span></strong></div></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
<!-- jQuery first, then Bootstrap JS. -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src='<?php echo plugin_dir_url(__FILE__)."assets/js/jquery.number.min.js";?>'></script>
<script type="text/javascript">
$(document).ready(function() {
  $.getJSON("<?php echo plugin_dir_url(__FILE__).'libs/get_data.php';?>", function(data) {
    var currency_options="<option value=''>From</option>";
    var tbl = "<table class='table table-striped table-hover table-condensed  table-info table-bordered'>";
    if(data){
      tbl += "<thead><tr><th colspan='3'>Exchange rate for "+new Date(data['rate_date']*1000).format("longDate")+"</th></tr>";
      tbl += "<tr><th rowspan='2' style='vertical-align:middle;'>Currency</th><th colspan='2' class='text-center'><span class='flag flag-et'></span></th></tr><tr><th>Buying</th><th>Selling</th></tr></thead><tbody class='curr_body'>";
      for (var i = 0; i < data['detail'].length; i++) {
        currency_options+="<option value='"+data['detail'][i]['currency']+"'>"+ data['detail'][i]['currency']+"</option>";
        tbl += "<tr id='" + data['detail'][i]['currency'] + "' class='animated fadeIn'>";
        tbl += "<td id='" +i+"_"+data['detail'][i]['currency'] +"'><img src='"+data['detail'][i]['flag']+"'> "  +data['detail'][i]['currency'] + "</td>";
        tbl += "<td id='buy_" +  data['detail'][i]['currency'] + "'>" +
        (data['detail'][i]['cash_buying'] != 0 ? data['detail'][i]['cash_buying'] : data['detail'][i]['tran_buying']) + "</td>";
        tbl += "<td id='sell_" + data['detail'][i]['currency'] + "'>" + (data['detail'][i]['cash_selling'] != 0 ?
        data['detail'][i]['tran_selling'] : data['detail'][i]['tran_selling']) + "</td>";
        tbl+="</tr>";
      }
      tbl += "</tbody></table>";
      $("#currency_list").html(currency_options);
      $("#exchange_rates").html(tbl);
      $("#widget_currency_calculator").show();
    }else{
      error_msg="<div class='well well-sm alert alert-danger'><i class='fa fa-exclamation-triangle'></i>Could not get currency data</div>"
      $("#widget_currency_calculator").hide();
      $("#exchange_rates").html(error_msg);
    }
  });
  //currency converter
  $("#from_amount").add("#currency_list").on('keyup keypress blur change',function(t){
    var currency=$("#currency_list").val();
    var rate= $("#buy_"+currency).text();
    var amount=$("#from_amount").val();
    $("#to_amount").html($.number(parseFloat(amount*rate),2));
  });
  //Price per unit calculation
  $("#av_qty").add("#av_qty_unit, #pp_price , #pp_unit , #lbs , #bag, #Mg").on('keyup keypress blur change',function(t){
    var available = $("#av_qty").val();
    var av_unit   = $("#av_qty_unit").val();
    var pp_price  = $("#pp_price").val();
    var pp_unit   = $("#pp_unit").val();
    var rate = $("#buy_USD").text();
    var tt=available*pp_price;
    var tr="<tr><td>"+tt+""+av_unit+"</td><td>"+pp_price+"/"+pp_unit+"</td><td>" +$.number(parseFloat((rate*tt)),2)+"</td></tr>";
        tr+="<tr><td>"+tt+""+av_unit+"</td><td>"+pp_price+"/"+pp_unit+"</td><td>"+$.number(parseFloat((rate*tt)),2)+"</td></tr>";
        tr+="<tr><td>"+tt+""+av_unit+"</td><td>"+pp_price+"/"+pp_unit+"</td><td>"+$.number(parseFloat((rate*tt)),2)+"</td></tr>";
    $("#etb_result").html(tr);
   });
});
</script>
</div>
