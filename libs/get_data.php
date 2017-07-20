<?php require_once "helper.inc.php";
header("Content-Type: application/json;encoding:utf-8");
echo json_encode(get_cbe_data()['data']);
?>
