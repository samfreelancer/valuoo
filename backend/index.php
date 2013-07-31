<?php
include_once '../inc/config.php';
include_once '../inc/database.php';
include_once '../inc/functions.php';

for($i= 5; $i >= 1; $i--){
    $str = '';    
$parm_date = date('d-M-Y', mktime(0,0,0,date('m'), date('d')-$i, date('Y'))); 
$str = file_get_contents("http://www.amfiindia.com/DownloadNAVHistoryReport_Po.aspx?frmdt=".$parm_date);


echo "<pre>";

//echo $str;

$pattern = "/(\d{6})\;(.*)\;(\d+\.?\d*)\;(\d+\.?\d*)\;(\d+\.?\d*)\;(\d{1,2}-?[a-z]{3}-\d{4})/i";

preg_match_all($pattern, $str, $matches);
//$matches = explode('Mutual Fund',$str);

//print_r($matches[0]);

$insert_parm = '';
foreach($matches[0] as $key => $val){
    
    $details = explode(';',$val);
    print_r($details);
    $scheme_code = '';$scheme_name = '';$net_asset_value = '';$date = '';
    
    
    $scheme_code = $details[0];
    $scheme_name = $details[1];
    $net_asset_value = $details[2];
    $date = date('Y-m-d', strtotime($details[5]));
    
    $insert_parm .= "('".Functions::escape_string($scheme_code)."','".Functions::escape_string($scheme_name)."','".Functions::escape_string($net_asset_value)."','".Functions::escape_string($date)."'),";
    
}
$fast_query = "ALTER TABLE `tbl_mutual_funds` DISABLE KEYS;SET FOREIGN_KEY_CHECKS = 0;SET UNIQUE_CHECKS = 0;SET AUTOCOMMIT = 0;";
$rest_query = "ALTER TABLE `tbl_mutual_funds` ENABLE KEYS;SET UNIQUE_CHECKS = 1;SET FOREIGN_KEY_CHECKS = 1;COMMIT;";
$insert_query = "INSERT INTO tbl_mutual_funds(`scheme_code`,`scheme_name`,`net_asset_value`,`scheme_date`) VALUES ".rtrim($insert_parm,',');

try{
    $adapter = new Database();
    if($adapter->insert($insert_query)){
        
        throw new Exception("Successfully inserted data");
    }else{
        
        throw new Exception("Some problem in insert");
    }

}catch(Exception $e){
    
    print_r($e->getMessage());
}
echo "</pre>";

}