<?php header("Content-Type: application/json; encoding:utf-8");
require_once "helper.inc.php";
$json = get_cbe_data();
if($json===false){
  $json = json_encode(array("JSON_ERROR",json_last_error_msg()));
  if($json===false){
    $json='{"JSON_ERROR":"Unknown JSON Error"}';
  }
  http_response_code(500);
}
echo $json;
?>
