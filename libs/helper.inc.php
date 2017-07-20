<?php //ini_set("display_errors","on"); error_reporting(-1);
function get_contents_curl($url,$timeout=120, $retries=10) {
    $state="FAILED_TO_READ_URL";
    $data="";
    $result=array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $retry=$retries;
    while($retry > 0){
      $data = curl_exec($ch);
      if(strlen($data)>0){
        $state="SUCCESS";
        $retry=0;
      }
      $retry=$retry-1;
    }
    curl_close($ch);
    $result['state']=$state;
    $result['data']=$data;
    return $result;
}
function get_cbe_data(){
  if($result['state']=="SUCCESS"){
    $state="FAILED_TO_LOAD_HTML";
    $html=array();
    $html['state']=$state;
    $html['data']=array();
    $currencies_required=array("USD","GBP","EUR","CAD","JPY","AUD","AED","CNY");
    $url = "http://www.combanketh.et/More/CurrencyRate.aspx";
    $rate_date_selector="span.date";
    $rates_table_selector="table:nth-of-type(2)";
    $result = get_contents_curl($url);
    $result=preg_replace("/CN\¥/s", "CNY", $result);
    preg_match('/<span id="dnn_dnnCURRENTDATE_lblDate" class="date">(.*?)<\/span>/s',$result['data'],$r_date);
    $rate_date = strtotime(preg_replace(array("/<span(.*?)>/s","/<\/span>/s"),array("",""),$r_date[0]));
    $patterns=array();
    $replacements=array();
    $patterns[0]="/<span(.*?)>/s";
    $patterns[1]="/<\/span>/s";
    $patterns[2]="/<script(.*?)>(.*?)<\/script>/s";
    $patterns[3]="/\/Portals\/0\/Images\/Flag/s";
    $replacements[0]="";
    $replacements[1]="";
    $replacements[2]="";
    $replacements[3]="/wp-content/plugins/ethio_currency/assets/flags";
    $raw_html = preg_replace('/\s+/s',' ', preg_replace($patterns, $replacements, $result['data']));
    $regex ='/<table border="0" width="400" cellpadding="1" style="border-top-color: Black; font-family: verdana;[ \s]* font-size:[\s]* 8pt">(.*?)<\/table>/s';
    preg_match($regex, $raw_html,$list);
    $pats=array();
    $reps=array();
    $pats[0]='/id=\"(.*?)\"/s';
    $pats[1]='/style=\"(.*?)\"/s';
    $pats[2]='/align=\"(.*?)\"/s';
    $pats[3]='/cellpadding=\"(.*?)\"/s';
    $pats[4]='/border=\"(.*?)\"/s';
    $pats[5]='/width=\"(.*?)\"/s';
    $reps[0]="";
    $currency_table = preg_replace('/\s+/s',' ',preg_replace($pats, $reps, $list[0]));
    $state="SUCCESS";
    $html['state']=$state;
    $table_dom  = new DOMDocument();
    $table_dom->loadHTML(mb_convert_encoding($currency_table, 'HTML-ENTITIES', "UTF-8"));
    $table_dom->preserveWhiteSpace=false;
    $table = $table_dom->getElementsByTagName("table");
    if($table->length == 0){
      throw new Exception("No Table Found");
    }
    $table_head = $table->item(0)->getElementsByTagName("th");
    $tbl_head=array();
    $flags=array();
    foreach($table_head as $header){
      $txt =trim($header->textContent);
      $txt=mb_strtolower($txt);
      $txt=implode("_",explode(" ", $txt));
      $tbl_head[]= empty($txt) ? "currency" : $txt;
    }
    $tbl_detail=array();
    $i=0; $j=0;
    $table_detail = $table->item(0)->getElementsByTagName("td");
    foreach ($table_detail as $detail) {
      $imgs=$detail->getElementsByTagName("img");
      if(is_object($imgs) && $imgs->length > 0){
        $key=trim($detail->textContent);
        $flags[$key]=$imgs->item(0)->getAttribute("src");
      }
      $tbl_detail[$j][]=trim($detail->nodeValue);
      $i=$i+1;
      $j=$i % count($tbl_head)==0 ? $j+1 : $j;
    }
    $formatted_data=array();
    // Prepare formatted data
    foreach ($tbl_detail as $detail) {
      //flags,details and headers
      $currency_info=array();
      if(in_array($detail[0],$currencies_required)){
        foreach($tbl_head as $key=>$header){
          $currency_info[$header]=$detail[$key];
        }
        $currency_info["flag"]=$flags[$detail[0]];
        $formatted_data[]=$currency_info;
      }
    }
    $currency_data=array('rate_date'=>$rate_date,'headers'=>$tbl_head,'detail'=>$formatted_data);
    $html['data']=$currency_data;
    return $html;
  }else{
    $html['state']=$html['state'].", ".$state;
  }
  return $html;
}
function get_nbe_data(){
  $url = "http://www.nbe.gov.et/xml/rss.php";
}
?>
