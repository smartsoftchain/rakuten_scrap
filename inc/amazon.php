<?php
	//amazonキーワード検索
header("Content-Type: text/html;charset=utf-8"); 
ini_set("memory_limit", "1024M");
ini_set('max_execution_time', '360000');
ini_set( 'display_errors', 1 );
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set( "log_errors", "On" );
ini_set( "error_log", "../log/".date("Y-m-d")."_php.log" );

$sec = 1;
set_time_limit(0);
$context = stream_context_create(array('http' => array(
'method' => 'GET',
'header' => 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
)));

$argv = $_SERVER["argv"];

	define("SCRIPT_ENCODING", "UTF-8");
	// データベースの漢字コード
	define("DB_ENCODING", "UTF-8");
	// メールの漢字コード(UTF-8かJIS)
	define("MAIL_ENCODING", "JIS");

	$path = "../inc";
	require_once($path."/conf.php");
	require_once($path."/scp.php");
	require_once($path."/my_db.inc");
	require_once($path."/htmltemplate.inc");
	require_once($path."/errlog.inc");
	
	$pid = $argv[1];
	$keyword = $argv[2];

	//検索結果ページ
	$base_url = "http://www.amazon.co.jp/s/keywords=".$keyword;
	//詳細ページURL
	$detail_url = "http://www.amazon.co.jp/dp/";
	
	$contents = file_get_contents($base_url);
	$contents = mb_convert_encoding($contents,'shift-jis','auto');
	$contents = str_replace(array("\r\n","\r","\n"), '', $contents);
	//ページ数取得
	$match1 = array();
	preg_match_all("/<span class=\"pagnDisabled\">(.*?)<\/span>/", $contents, $match1);
	if($match1[1][0]){
		$max = $match1[1][0];
	}else{
		$max = 1;
	}
	echo '<br>---- MAX '.$max.'ページ ----<br><br>';
	//$max = 1;
	echo "----------- ".date("H:i:s")." -----------";
	for($n=1;$n<=$max;$n++){ 
		$para = $base_url."&page=".$n."";
		echo '<br>---- '.$para.'ページ 開始 ----<br><br>';
		$get_contents = file_get_contents($para, false, $context);
		$get_contents = mb_convert_encoding($get_contents,'UTF-8','auto');
		$get_contents = str_replace(array("\r\n","\r","\n"), '', $get_contents);
		//このページの一覧のアマゾンコードを取得
		$pattern="/<div id=\"result_\d+\" class=\".*?\" name=\"(.*?)\">/is";
		$matches = array();
		preg_match_all($pattern, $get_contents, $matches); 
		foreach($matches[1] as $key => $val){
			if(trim($val)){
				echo "<br />------ asin ".$val."--------------";
				$response = Get_result($keyv,$val,"asin",0);
				//var_dump($response);
				//詳細ページ取得
				$contents = file_get_contents($detail_url.$val, false, $context);
				$contents = mb_convert_encoding($contents,'UTF-8','auto');
				$contents = str_replace(array("\r\n","\r","\n"), '', $contents);
				//詳細ページ情報取得
					$list = array();
					$list["asin"] = $val;
					$list["jan"] = $response['Item'][0]['ItemAttributes']["EAN"];
					//商品タイトル
					
						$match1 = array();
						preg_match_all('/<span id=\"btAsinTitle\" >(.*?)<\/span> <\!\-\-aoeui\-\->/', $contents, $match1);
						$convmap = array (0x0, 0xffff, 0, 0xffff);
						//$list["title"] = mb_decode_numericentity($match1[1][0], $convmap,'shift-jis');
						$list["item_name"] = strip_tags($match1[1][0]);
					
					//メーカー名
					if($response['Item'][0]["ItemAttributes"]["Brand"]){
						$list["brand"] = $response['Item'][0]["ItemAttributes"]["Brand"];
					}else{
						$match1 = array();
						preg_match_all('/<\/h1>.*?<a href=\".*?\">(.*?)<\/a>/', $contents, $match1);
						$list["brand"] = $match1[1][0];
					}
					
					//評価の数
					$match1 = array();
					preg_match_all('/<span class=\"swSprite s_star.*?\" title=\"5つ星のうち ([+-]?[0-9]*[\.]?[0-9]+)\".*?><span>5つ星のうち/', $contents, $match1);
					$list["vote"] = trim($match1[1][0]);
					//最安値
					if($response['Item'][0]["ItemAttributes"]["TradeInValue"]["Amount"]){
						$list["saiyasu_price"] = $response['Item'][0]["ItemAttributes"]["TradeInValue"]["Amount"];
					}else{
						$match1 = array();
						preg_match_all("/<b class=\"priceLarge\">.*?(\b\d{1,3}(,\d{3})*\b)/", $contents, $match1);
						if($match1[1][0]){
							$list["saiyasu_price"] = trim(str_replace(array("￥","\\",","),"",$match1[1][0]));
						}else{
							$match1 = array();
							preg_match_all("/<span id=\"priceblock_ourprice\" class=\"a\-size-medium a\-color\-price\">￥ (.*?)<\/span>/", $contents, $match1);
							$list["saiyasu_price"] = str_replace(",","",$match1[1][0]);
						}
					}
					//大きさ
					$match1 = array();
					preg_match_all('/size\-weight\"><td .*?>.*?<\/td><td class=\"value\">(.*?)<\/td><\/tr>/', $contents, $match1);
					if(count($match1[1]) == 1){
						$list["size"] = $match1[1][0];
					}else{
						$list["size"] = $match1[1][1];
					}
					//重さ
					$match1 = array();
					preg_match_all('/<tr class=\"shipping\-weight\"><td class=\"label\">.*?<\/td><td class=\"value\">(.*?)<\/td>/', $contents, $match1);
					if($match1[1][0]){
						$list["weight"] = $match1[1][0];
					}else{
						$match1 = array();
						preg_match_all('/<li><b>発送重量:<\/b> (.*?)<\/li>/', $contents, $match1);
						$list["weight"] = $match1[1][0];
					}
					//カテゴリ取得
					$match1 = array();
					preg_match_all('/<span class=\"zg_hrsr_ladder\">(.*?)<\/a><\/span>/', str_replace(array("<b>","</b>"),"",$contents), $match1);
					$cate = "";
					if($match1){
						foreach($match1[1] as $keyc => $valc){
							$cate .= "■".str_replace(array("─&nbsp;","&gt;"),array("",">"),strip_tags($valc))." ";
						}
					}
					$list["category"] = $cate;
					//ランキング
					$match1 = array();
					preg_match_all('/<span class=\"zg_hrsr_rank\">(\d+)位<\/span>/', $contents, $match1);
					$list["ranking"] = $match1[1][0];
					//出品者数
					$match1 = array();
					preg_match_all('/([0-9]+)の新品\/中古品の出品を見る<\/a>/', $contents, $match1);
					if($match1[1][0]){
						$list["member"] = $match1[1][0];
					}else{
						$match1 = array();
						preg_match_all('/新品の出品：([0-9]+)/', $contents, $match1);
						$list["member"] = $match1[1][0];
					}
					
					//画像の取得
					if($response['Item'][0]["LargeImage"]["URL"]){
						$list["img"] = $response['Item'][0]["LargeImage"]["URL"];
					}else{
						//まずはLeftCoｌのデータを取ってくる
						$match1 = array();
						preg_match_all('/<div id=\"leftCol\" class=\"leftCol\">(.*?)<div id=\"centerCol\"/', $contents, $match1);
						$leftcol = $match1[1][0];
						
						$match1 = array();
						preg_match_all('/main\"\:\{\"(.*?)\"\:\[.*?\]\}/', $leftcol, $match1);
						if($match1[1]){
							$cnt = 0;
							foreach($match1[1] as $keym => $valm){
								if($list["img"] and $cnt > 0){
									break;
								}
								$list["img"] = $valm;
								$cnt++;
							}
						}
					}
					
					//このデータが無ければインサート
					if($list){
						$inst = DBConnection::getConnection($DB_URI);
						$sql = "select * from `plan_data` where `asin`='".$val."' and `pid`=".$pid."";
						$ret = $inst->search_sql($sql);
						if($ret["count"] == 0){
							$sql = "insert into `plan_data`(`pid`,`item_name`,`img`,`brand`,`vote`,`asin`,`jan`,`us_price`,`price`,`saiyasu_price`,`size`,`weight`,`category`,`ranking`,`member`,`type`) values ";
							$sql .= "(".$pid.",'".$list["item_name"]."','".$list["img"]."','".$list["brand"]."','".$list["vote"]."','".$val."','".$list["jan"]."',0,0,'".$list["saiyasu_price"]."','".$list["size"]."','".$list["weight"]."','".$list["category"]."','".$list["ranking"]."','".$list["member"]."','')";
							$ret = $inst->db_exec($sql) or die($sql);
						}
					}
					print_r($list);
					ob_flush();
					flush();
					sleep($sec);
					//var_dump($list);
			}
			
		}
	}
	echo "<br>------- 完了 -------";
	
function Get_result($keyv,$val,$type,$c=0){
	//ＰＥＡＲのインクルードパスを指定＆SERVICES_AMAZONを読み込み
	$include_path= "../PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	$c++;
	if($c < 2){
		$response = array();
		/*
		$rand_key = array_rand($keyv, 1);
		$amazon_key = trim($keyv[$rand_key][0]);
		$amazon_skey = trim($keyv[$rand_key][1]);
		*/
		//アマゾンオブジェクトを作成
		//$amazon = new Services_Amazon($amazon_key, $amazon_skey,"infobreak-22");
		$amazon = new Services_Amazon("AKIAJ3LKPK5RBTWH32TA", "OSGq08zjMuC/C2o8C3Hzu3GpSeLqHO5e/G0+csTm","mtta61124-22");
		$amazon->setBaseUrl('http://ecs.amazonaws.jp/onca/xml');
					
		if($type=="asin"){
			$options = array(
                 "ResponseGroup" => 'ItemAttributes,Images,OfferFull');
			$response = $amazon->ItemLookup($val,$options);
			//$response = $amazon->ItemLookup($val,'All');
		}elseif($type=="ean"){
			$options['IdType'] = 'EAN';
			$options['Version'] = '2010-09-01';
			$options['SearchIndex'] = 'All';
			$options['Condition'] = 'All';
			$options['MerchantId'] = 'All';
			$options['ResponseGroup'] = 'Large,OfferFull,Offers';
			$response = $amazon->ItemLookup(implode(",",$val),$options);
					
		}
		
		if(@get_class($response)=="PEAR_Error"){
			echo "APIエラー[".$rand_key."]".$amazon_key."=>" .$amazon_skey."<br />";
			Get_result($keyv,$val,$type,$c);
		}else{
			return $response;
		}
	}
	
	
}
	
?>