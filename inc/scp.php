<?php

/*   データベース設定    */
	$DB_URI = array("host" => $HOST, "db" => $DB, "user" => $USER, "pass" => $PASS);
/*   データベース設定    */
define("SCRIPT_ENCODING", "UTF-8");
define("DB_ENCODING", "UTF-8");

//会員ランク
$rank_list = array("通常版","上位版");

//端末
$ua_type = array("0"=>"PC","1"=>"タブレット","2"=>"iphone","3"=>"android","4"=>"モバイル");

//曜日
$week = array("日","月","火","水","木","金","土");

//お試し期間
$limit_day = 7;


$category_array = array(
  "Apparel"=>  "アパレル&ファッション雑貨",
  "Baby Product"=>      "ベビー&マタニティ",
  "Book"=>  "本・漫画・雑誌",
  "CE"=>    "家電&カメラ",
  "DVD"=>   "DVD",
  "Grocery"=>           "食品&飲料",
  "Health and Beauty"=> "ヘルス&ビューティー",
  "Kitchen"=>          "ホーム&キッチン",
  "Music"=> "ミュージック",
  "Office Product"=>    "文房具・オフィス用品",
  "Shoes"=> "シューズ",
  "Software"=>          "PCソフト",
  "Sports"=>"スポーツ&アウトドア",
  "Toy"=>   "おもちゃ",
  "VHS"=>   "ビデオ",
  "Video Games"=> "TVゲーム",
  "Watch"=> "時計"
);

$enc = array(
"A"=>"＼x41",
"B"=>"＼x42",
"C"=>"＼x43",
"D"=>"＼x44",
"E"=>"＼x45",
"F"=>"＼x46",
"K"=>"＼x47",
"L"=>"＼x48",
"M"=>"＼x49",
"N"=>"＼x50",
"O"=>"＼x51",
"P"=>"＼x52",
"Q"=>"＼x53",
"R"=>"＼x54",
"U"=>"＼x55",
"V"=>"＼x56",
"W"=>"＼x57",
"X"=>"＼x58",
"Y"=>"＼x59",
"Z"=>"＼x60",
"0"=>"＼x30",
"1"=>"＼x31",
"2"=>"＼x32",
"3"=>"＼x33",
"4"=>"＼x34",
"5"=>"＼x35",
"6"=>"＼x36",
"7"=>"＼x37",
"8"=>"＼x38",
"9"=>"＼x39",
"0"=>"＼x40"
	);

//並び替えよう配列
$order_list = array(
//	"item_name"=>"商品名",
//	"brand"=>"ブランド名",
//	"vote"=>"評価",
	"type"=>"評価数",
	"price"=>"カート価格",
//	"asin"=>"ASIN",
//	"jan"=>"JANコード",
//	"saiyasu_price"=>"最安値",
	"us_price"=>"USカート価格",
//	"category"=>"カテゴリ",
	"ranking"=>"ランキング",
	"member"=>"出品者数",
);
	
$order_list_us = array(
//	"item_name"=>"商品名",
//	"brand"=>"ブランド名",
//	"vote"=>"評価",
	"type_us"=>"評価数",
	"price"=>"カート価格",
//	"asin"=>"ASIN",
//	"jan"=>"JANコード",
//	"saiyasu_price"=>"最安値",
	"us_price"=>"USカート価格",
//	"category"=>"カテゴリ",
	"ranking_us"=>"ランキング",
	"member_us"=>"出品者数",
);
	
$order_list_jp = array(
//	"item_name"=>"商品名",
//	"brand"=>"ブランド名",
//	"vote"=>"評価",
	"type_jp"=>"評価数",
	"price"=>"カート価格",
//	"asin"=>"ASIN",
//	"jan"=>"JANコード",
//	"saiyasu_price"=>"最安値",
	"us_price"=>"USカート価格",
//	"category"=>"カテゴリ",
	"ranking_jp"=>"ランキング",
	"member_jp"=>"出品者数",
);
	
//google翻訳
function getTranslate_g($sour, $to){
	
	$api_url = 'https://www.googleapis.com/language/translate/v2?key=AIzaSyCJ9ClrIGmxV-bB01JnYBsMzxoii8nFzV0&target='.$to.'&q='.$sour;
	$retval = file_get_contents($api_url);
	$str = json_decode($retval);
	return $str->data->translations[0]->translatedText;
}
//マイクロソフト翻訳
/*
function getTranslate_m($sour, $from, $to){
	//zh-cn	中国語 (簡体)
	//zh-hk	中国語 (繁体)
	//en 英語
	//ja 日本語
	$api_url = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate';
	$api_url = 'https://api.datamarket.azure.com/Bing/MicrosoftTranslator/v1/';
	$param = array(
	                'appId' => 'O7YJiRCsbMlNhbUaU3cD+3OsmmP0GpLSOl/JcuQuvjE',
	                'from' => $from,
	                'to' => $to);

	$param['text'] = $sour;

	$request_url = $api_url . '?' . http_build_query($param);

	$retval = file_get_contents($request_url);
	$str = implode("",explode('\u000a', trim(substr($retval, 3), '"')));
	return $str;
}
	*/
function getTranslate_m($sour, $from, $to){
	
	require_once("MicrosoftTranslator.class.php");
	$appid = 'O7YJiRCsbMlNhbUaU3cD+3OsmmP0GpLSOl/JcuQuvjE';
	$text = htmlspecialchars($sour);
    //文字数長すぎると通らないので分割
    $textArr = str_split(strip_tags($text), 100);
     
    $result = '';
    foreach($textArr as $val){
        $translator = new MicrosoftTranslator($appid);
        $translator->translate($from,$to,$val);
        $response = json_decode($translator->response->jsonResponse);
        $result .= str_replace(array('<string xmlns="http://schemas.microsoft.com/2003/10/Serialization/">','</string>'),'',$response->translation);
    }
    return $result;
}
	
	
	
$asc_list = array(
	"asc"=>"昇順",
	"desc"=>"降順"
);

//為替情報を返す
function Get_kawase($from,$to){
	$url ="http://www.reuters.com/finance/currencies/quote?srcAmt=1.0&srcCurr=".$from."&destCurr=".$to;
	$get_contents = file_get_contents($url, false, $context);
	$match1 = array();
	preg_match_all('/<input id=\"destAmt.*\" value=\"([^<]+)\"/', $get_contents, $match1);
	return $match1[1][0];
}
//お試し期間あと何日あるかを返す
function Get_Limit_Day($regist){
	global $limit_day;
	$tday = strtotime(date("Y-m-d 00:00:00",strtotime($regist)));
	$limit_d = strtotime("+".($limit_day-1)." day",$tday);
	$limit_date = date("Y/m/d",$limit_d);
	$new_limit = date("Y/m/d 00:00:00",strtotime("+1 day",$limit_d));
	$zan =  ($limit_d-strtotime(date("Y-m-d 00:00:00")))/ (60 * 60 * 24);
	$zan2 =  24-(int)date("H");
	return array($limit_date,$zan,$zan2);
}
//お試しの期限が切れているかどうか
function Chk_Limit_Day($regist,$uid){
	global $limit_day,$DB_URI;
	$inst = DBConnection::getConnection($DB_URI);
	
	//最新の登録日を取得
	$sql = "select * from `user` where `id`=".$uid."";
	$ret = $inst->search_sql($sql);

	if($ret["count"] > 0){
		$regist = $ret["data"][0]["regist"];

	}
	$tday = date("Y-m-d 00:00:00",strtotime($regist));
	$limit_d = strtotime("+".($limit_day-1)." day",strtotime($tday));
	$now_d = strtotime(date("Y-m-d 00:00:00"));
	//切れていたらログイン停止に
	if($limit_d < $now_d){
		$sql = "update `user` set status=1 where `id`=".$uid."";
		$inst->db_exec($sql);
		return true;
	}else{
		return false;
	}
}


function Encode_str($val){
	$str = mb_convert_encoding($val, "UTF-8", "shift-jis");
	return $str;
}
/*function Encode_str($val,$to){
	if($to){
		$str = mb_convert_encoding($val, $to, "SJIS-win");
	}else{
		$str = $val;
	}
	return $str;
}*/
function Encode2_str($val){
	$str = mb_convert_encoding($val, "SJIS", "SJIS-win");
	return $str;
}
function Encode_array2($val){
	$new_val = array();
	foreach($val as $key => $val2){
		$new_val[$key] = Encode_str($val2);
	}
	return $new_val;
}
function Encode_array($val){
	return mb_convert_variables("UTF-8", "SJIS-win",$val);
}
/*function Decode_str($val){
	$str = mb_convert_encoding($val, "shift-jis", "UTF-8");
	return $str;
}*/
function Decode_array($val){
	return mb_convert_variables("shift-jis", "utf-8",$val);
}
function Decode_array2($val){
	$new_val = array();
	foreach($val as $key => $val2){
		$new_val[$key] = Decode_str($val2);
	}
	return $new_val;
}

function Decode_str($val){
	//return $val;
	$str = mb_convert_encoding($val, "shift-jis", "UTF-8");
	return $str;
}
function mbconvkana_array($array2,$to) {
	mb_convert_variables($to, "utf-8", $array2);
	$new_array = $array2;
	return $new_array;
}
//指定配列を指定文字コードに置き換える
function bmconvkana_array($array2,$to) {
	if($to){
		mb_convert_variables($to, "SJIS-win", $array2);
	}
	$new_array = $array2;
	return $new_array;
}
function q($str='') {
	if(is_array($str)) {
		$q = function_exists("q") ? "q" : array(&$this, "q");
		return array_map($q, $str);
	}else {
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		if(!is_numeric($str)) {
			$ver = explode('.', phpversion());
			if(intval($ver[0].$ver[1])>=43) {
				$str = mysql_real_escape_string($str);
			}else {
     			$str = addslashes($str);
     			$pre = array('/\n/m', '/\r/m', '/\x1a/m');
     			$after = array('\\\n', '\\\r', '\Z');
     			$str = preg_replace($pre, $after, $str);
    		}
   		}
   		return $str;
  	}
}


function Ltrim_Replace($str){
	$kwd_n = $str;
	//全角スペースを半角に
	$kwd_n = mb_convert_kana($kwd_n, "s","utf-8");
	$kwd_n = str_replace(" "," +","'+".ltrim($kwd_n));
	
	return $kwd_n;
}
function Ltrim_ReplaceY($str){
	$kwd_n = $str;
	//全角スペースを半角に
	$kwd_n = mb_convert_kana($kwd_n, "s","utf-8");
	$kwd_n = str_replace(" "," +","+".ltrim($kwd_n));
	
	return $kwd_n;
}
function Read_File($path){
	$new_str = "";
	$handle = fopen ($path, "r");
	while (!feof ($handle)) {
	    $str = fgets($handle, 4096);
	    if(strlen($str) > 0){
	    	$new_str .= $str;
	    }
	}
	fclose($handle);
	return $new_str;
}
function Write_File($path,$str,$a){
	$fp = fopen($path, $a);
	fputs($fp,$str."\n");
	fclose($fp);
}

function Paging ($page,$act,$page_count,$para="page",$para2=""){

	$pagingstring = "";
	if ($page > 1) {
		$pagingstring .= "<span class=\"pre\"><a rel=\"next\" href=\"?act=".$act."&".$para."=".strval($page - 1)."".$para2."\" title=\"前のページへ\">&laquo;前のページへ</a></span>";
		for ($i = 5; $i >= 1; $i--) {
			if ($page - $i >= 1) {
				$pagingstring .= "<span class=\"num\"><a href=\"?act=".$act."&".$para."=".strval($page - $i)."".$para2."\" >".strval($page - $i)."</a></span>";
			}
		}
	}
	$pagingstring .= "<span class=\"num01\">".strval($page)."</span>";
	if ($page < $page_count) {
		for ($i = 1; $i <= 5; $i++) {
			if ($page + $i <= $page_count) {
				$pagingstring .= "<span class=\"num\"><a href=\"?act=".$act."&".$para."=".strval($page + $i)."".$para2."\">".strval($page + $i)."</a></span>";
			}
		}
		$pagingstring .= "<span class=\"next\"><a rel=\"next\" href=\"?act=".$act."&".$para."=".strval($page + 1)."".$para2."\" title=\"次のページへ\">次のページへ&raquo;</a></span>";
	}
	return $pagingstring;
}
function Get_row($table,$id){
	global $DB_URI,$kawase;
	$inst = DBConnection::getConnection($DB_URI);
	$sqlt = "select * from `".$table."` where `id`=".$id."";

	$rett = $inst->search_sql($sqlt);
	if($rett["count"] > 0){
		$val = $rett["data"][0];
			$val["us_price"] = str_replace("Too low to display","",$val["us_price"]);
			//US再計算
			if($val["us_price"] > 0){
				$us_price = round($val["us_price"] * $kawase);
				$sa = $us_price - $val["price"];
				if($sa > 0){
					$sa_str = "<br /><span style=\"color:green;\">(+".number_format($sa)."円)</span>";
				}else{
					$sa_str = "<br /><span style=\"color:red;\">(".number_format($sa)."円)</span>";
				}
				$val["us_price"] = number_format($us_price)."円".$sa_str;
			}
			$val["price"] = number_format($val["price"]);
			$val["saiyasu_price"] = number_format($val["saiyasu_price"]);
				$val["ranking"] = @number_format($val["ranking"]);
			//ヤフオク用タイトル（エンコード）
			$wkey = explode(" ",$val["item_name"]);
			$val["yafuoku"] = urlencode($wkey[0]." ".$wkey[1]);
			if($val["us_price"] == "0"){
				$val["us_price"] = "";
			}
			//中国フレーズ分解
				//タイトルにもアンカー
				if($val["china_title"]){
					$str = $val["china_title"];
					$val["china_title"] = "<a href=\"http://s.taobao.com/search?q=".urlencode($str)."&commend=all&ssid=s5-e&search_type=mall&sourceId=tb.index&spm=a215z.7106357.5803581.d4908513\" target=\"_blank\">".$str."</a>";
					$str2 = mb_convert_encoding($str, "GB2312", "utf-8");
					$val["china_title_ari"] = "<a href=\"http://s.1688.com/selloffer/offer_search.htm?keywords=".urlencode($str2)."\" target=\"_blank\">".$str."</a>";

				}
				if($val["china_key"]){
					$phrase = explode(",",$val["china_key"]);
					$micro = array();$aribaba = array();
					if($phrase){
						foreach($phrase as $val2){
							if(strstr($val2,"AppId is over the quota")){
							}else{
								$str = mb_convert_encoding($val2, "GB2312", "utf-8");
								$micro[] = "■<a href=\"http://s.taobao.com/search?q=".urlencode($val2)."&commend=all&ssid=s5-e&search_type=mall&sourceId=tb.index&spm=a215z.7106357.5803581.d4908513\" target=\"_blank\">".$val2."</a>";
								$aribaba[] = "■<a href=\"http://s.1688.com/selloffer/offer_search.htm?keywords=".urlencode($str)."\" target=\"_blank\">".$val2."</a>";

							}
						}
						$val["micro"] = "".implode("<br />",$micro);
						$val["aribaba"] = "".implode("<br />",$aribaba);
					}
				}
		
		
	}
	return $val;
}


function create_str(){
	global $DB_URI;
	$length = 6;

	$char = 'abcdefghijklmnopqrstuvwxyz1234567890';
	 
	$charlen = mb_strlen($char);
	$result = "";
	 
	for($i=1;$i<=$length;$i++){
	  $index = mt_rand(0, $charlen - 1);
	  $result .= mb_substr($char, $index, 1);
	}
	$inst2 = DBConnection::getConnection($DB_URI);
	 //同じ者があれば再生成
	 $sqlr = "select * from `project` where `p`='".$result."'";
	 $rst2 = $inst2->search_sql($sqlr);
	 if($rst2["count"] > 0){
	 	 create_str();
	 }
		return $result;
}

function Get_key_once(){
	global $DB_URI;
	$inst = DBConnection::getConnection($DB_URI);
	//今の時間仕様履歴の無いものを抽出
	$d = date("Y-m-d H");
	$sql = "select * from `amazon_key` where `use_date` LIKE '".$d."' or `use_date` IS NULL limit 1";
	$ret = $inst->search_sql($sql) or die($sql);
	if($ret["count"] > 0){
		$ama = $ret["data"][0];
		$sql = "update `amazon_key` set `use_date`='".$d."',`cnt`=0 where `id`=".$ret["data"][0]["id"]."";
		$inst->db_exec($sql);
		//$inst->db_close();
		return array($ama["access"],$ama["secret"],$ama["associate"],$ama["id"]);
	}else{
		//無ければカウントの低いもの
		$sql = "select * from `amazon_key` order by `cnt`,`error` limit 1";
		$ret2 = $inst->search_sql($sql);
		if($ret2["count"] > 0){
			$ama = $ret2["data"][0];
			$sql = "update `amazon_key` set `use_date`='".$d."',`cnt`=cnt+1 where `id`=".$ret2["data"][0]["id"]."";
			$inst->db_exec($sql);
		}
		//$inst->db_close();
		return array($ama["access"],$ama["secret"],$ama["associate"],$ama["id"]);
	}
	
}
function Get_key_once_neo(){
	global $DB_URI;
	$inst = DBConnection::getConnection($DB_URI);
	//今の時間仕様履歴の無いものを抽出
	$d = date("Y-m-d H");
	$key_list = array();
	$sql = "select * from `amazon_key` order by `error`";
	$ret = $inst->search_sql($sql) or die($sql);
	if($ret["count"] > 0){
		foreach($ret["data"] as $key => $val){
			$key_list[] = array($ama["access"],$ama["secret"],$ama["associate"],$ama["id"]);
		}
	}
	return $key_list;
}
function Get_result_seller($val){
	//キー取得
	$amakey = Get_key_once();
	//ＰＥＡＲのインクルードパスを指定＆SERVICES_AMAZONを読み込み
	$include_path= "../PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	$amazon = new Services_Amazon($amakey[0], $amakey[1],$amakey[2]);
	$response = array();
	$options['IdType'] = 'EAN';
	$options['Version'] = '2010-09-01';
	$options['SearchIndex'] = 'All';
	$options['Condition'] = 'All';
	$options['MerchantId'] = $val;
	$options['ResponseGroup'] = $group;
	$response = $amazon->SellerLookup($options);
	return $response;
}
function Get_result_seller2($val){
	//キー取得
	$amakey = Get_key_once();
	//ＰＥＡＲのインクルードパスを指定＆SERVICES_AMAZONを読み込み
	$include_path= "../PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	$amazon = new Services_Amazon($amakey[0], $amakey[1],$amakey[2]);
	$response = array();
	$options['Version'] = '2010-09-01';
	$options['SearchIndex'] = 'All';
	$options['Condition'] = 'All';
	$options['MerchantId'] = $val;
	$options['ResponseGroup'] = "SellerListing";
	$response = $amazon->ItemLookup($options);
	return $response;
}
function Get_result($keyv,$asin,$type,$c=0,$sw=1){
	global $DB_URI;
	
	//キー取得
	$amakey = Get_key_once();
	//ＰＥＡＲのインクルードパスを指定＆SERVICES_AMAZONを読み込み
	$include_path= "../PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	$c++;
	if($c < 2){
		$response = array();
		//アマゾンオブジェクトを作成
		$amazon = new Services_Amazon($amakey[0], $amakey[1],$amakey[2]);
		if($sw == 1){
			$amazon->setBaseUrl('http://ecs.amazonaws.jp/onca/xml');
			$group = "ItemAttributes,Images,Offers,SalesRank,Reviews";
		}elseif($sw == 2){
			$amazon->setBaseUrl('http://ecs.amazonaws.com/onca/xml');
			$group = "ItemAttributes,Offers,OfferFull,Large";
		}elseif($sw == 3){
			$amazon->setBaseUrl('http://ecs.amazonaws.com/onca/xml');
			$group = "SellerListingLookup";
		}
					
		if($type=="asin"){
			$options = array("ResponseGroup" => $group);
			$response = $amazon->ItemLookup($asin,$options);
		}elseif($type=="ean"){
			$options['IdType'] = 'EAN';
			$options['Version'] = '2010-09-01';
			$options['SearchIndex'] = 'All';
			$options['Condition'] = 'All';
			$options['MerchantId'] = 'All';
			$options['ResponseGroup'] = $group;
			$response = $amazon->ItemLookup(implode(",",$asin),$options);
					
		}

		if(@get_class($response)=="PEAR_Error"){
			echo "jp-";
			//var_dump($response);
			//exit;
			if($sw != 2){
				$inst = DBConnection::getConnection($DB_URI);
				echo "APIエラー[".$amakey[0]."=>".$amakey[1]."=>".$amakey[2]."=>".$asin."=>".$sw."]"."<br />
";
				$sql = "update `amazon_key` set `error`=error+1 where `id`=".$amakey[3]."";
				$inst->db_exec($sql);
				$sql = "insert into `errortb`(`key`,`asin`,`type`) values('','".$asin."','".$keyv."')";
				$inst->db_exec($sql);
				sleep(1);
				Get_result($keyv,$val,$type,$c,$sw);
			}
		}else{
			return $response;
		}
	}
	
	
}

function Get_result_us($keyv,$asin,$type,$c=0){
	global $DB_URI;
	
	//キー取得
	$amakey = Get_key_once();
	//ＰＥＡＲのインクルードパスを指定＆SERVICES_AMAZONを読み込み
	$include_path= "../PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	$c++;
	if($c < 2){
		$response = array();
		//アマゾンオブジェクトを作成
		$amazon = new Services_Amazon($amakey[0], $amakey[1],$amakey[2]);

		$amazon->setBaseUrl('http://ecs.amazonaws.com/onca/xml');
		$group = "ItemAttributes,Offers,OfferFull,Large";

					
		if($type=="asin"){
			$options = array("ResponseGroup" => $group);
			$response = $amazon->ItemLookup($asin,$options);
		}elseif($type=="ean"){
			$options['IdType'] = 'EAN';
			$options['Version'] = '2010-09-01';
			$options['SearchIndex'] = 'All';
			$options['Condition'] = 'All';
			$options['MerchantId'] = 'All';
			$options['ResponseGroup'] = $group;
			$response = $amazon->ItemLookup(implode(",",$asin),$options);
					
		}

		if(@get_class($response)=="PEAR_Error"){
			//var_dump($response);
			//exit;
			if($sw != 2){
				$inst = DBConnection::getConnection($DB_URI);
				echo "APIエラー[".$amakey[0]."=>".$amakey[1]."=>".$amakey[2]."=>".$asin."=>".$sw."]"."<br />
";
				$sql = "update `amazon_key` set `error`=error+1 where `id`=".$amakey[3]."";
				$inst->db_exec($sql);
				sleep(1);
				Get_result_us($keyv,$val,$type,$c);
			}
		}else{
			return $response;
		}
	}
	
	
}


function Get_result_test($keyv,$asin,$type,$c=0,$sw=1){
	global $DB_URI;
	
	//キー取得
	$amakey = Get_key_once();
	
	//ＰＥＡＲのインクルードパスを指定＆SERVICES_AMAZONを読み込み
	$include_path= "../PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	$c++;
	if($c < 2){
		$response = array();
		//アマゾンオブジェクトを作成
		$amazon = new Services_Amazon($amakey[0], $amakey[1],$amakey[2]);
		if($sw == 1){
			$amazon->setBaseUrl('http://ecs.amazonaws.jp/onca/xml');
			$group = "ItemAttributes,Images,Offers,SalesRank,Reviews";
		}elseif($sw == 2){
			$amazon->setBaseUrl('http://ecs.amazonaws.com/onca/xml');
			$group = "ItemAttributes,Offers,SalesRank,Reviews";
		}elseif($sw == 3){
			$amazon->setBaseUrl('http://ecs.amazonaws.com/onca/xml');
			$group = "SellerListingLookup";
		}
					
		if($type=="asin"){
			$options = array("ResponseGroup" => $group);
			$response = $amazon->ItemLookup($asin,$options);
		}elseif($type=="ean"){
			$options['IdType'] = 'EAN';
			$options['Version'] = '2010-09-01';
			$options['SearchIndex'] = 'All';
			$options['Condition'] = 'All';
			$options['MerchantId'] = 'All';
			$options['ResponseGroup'] = $group;
			$response = $amazon->ItemLookup(implode(",",$asin),$options);
					
		}

		if(@get_class($response)=="PEAR_Error"){
			var_dump($response);
			exit;

		}else{
			return $response;
		}
	}
	
	
}
if (!function_exists('json_encode')) {
	require 'JSON.php';
	function json_encode($value) {
		$s = new Services_JSON();
		return $s->encodeUnsafe($value);
	}
	function json_decode($json, $assoc = false) {
		$s = new Services_JSON($assoc ? SERVICES_JSON_LOOSE_TYPE : 0);
		return $s->decode($json);
	}
}
function download_csv($data, $filename){
	header("Content-disposition: attachment; filename=" . $filename);
	header("Content-type: text/x-csv; charset=Shift_JIS");
	header("Cache-Control: public");
	header("Pragma: public");
	foreach ($data as $val) {
		$csv = array();
		foreach ($val as $item) {
			array_push($csv, $item);
		}
		echo mb_convert_encoding(implode(",", $csv), "Shift_Jis", "utf-8") . "\r\n";
	}
	exit;
}

?>