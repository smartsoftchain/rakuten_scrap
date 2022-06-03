<?php
header("Content-Type: text/html;charset=utf-8"); 
mb_language('Japanese');
ini_set("memory_limit", "1024M");
ini_set('max_execution_time', '360000');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$dirs = dirname(__FILE__);
chdir($dirs);

$path = "./inc";
require_once($path."/conf.php");
require_once($path."/scp.php");
require_once($path."/my_db.inc");
require_once($path."/ua.php");
	require_once($path."/tag.php");
	
$inst = DBConnection::getConnection($DB_URI);

for($i=0;$i<500;$i++){
	$p=500;
	$sql = "select * from `category_data` where `china_title` IS NULL order by `id` limit ".($p*$i).",".$p."";
	$ret = $inst->search_sql($sql);
	if($ret["count"] > 0){
		foreach($ret["data"] as $key => $val){
			echo "ID=>".$val["id"]."\n";
				//フレーズをわけ中国語に変換し、登録する
								//タイトルを中国語に(MSN)
								$china_title = getTranslate_m($val["item_name"],"ja","zh-CHS");
								if(strstr($china_title,"AppId is over the quota")){
									$china_title = "";
								}
								//タイトル分解
								$micro = array();$china_key = "";
								$phrase = explode(",",Insert_tag2($val["item_name"]));
								if($phrase){
									foreach($phrase as $val2){
										$micro[] = getTranslate_m($val2,"ja","zh-CHS");
									}
									if($micro){
										$china_key = implode(",",$micro);
									}
								}
								
					$sql2 = "update `category_data` set `china_title`='".$china_title."',`china_key`='".$china_key."' where `id`=".$val["id"]."";
					$inst->db_exec($sql2);
								
								
		}
		
	}else{
		exit;
	}
}
	
	
?>