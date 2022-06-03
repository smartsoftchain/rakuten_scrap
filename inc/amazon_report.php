<?php
header("Content-Type: text/html;charset=utf-8"); 
mb_language('Japanese');
ini_set("memory_limit", "512M");
ini_set('max_execution_time', '360000');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$dirs = dirname(__FILE__);
chdir($dirs);

$path = $dirs;
require_once($path."/conf.php");
require_once($path."/scp.php");
require_once($path."/my_db.inc");
require_once($path."/ua.php");
	require_once($path."/tag.php");

//$response = Get_result($keyv,"B00IIHR334","asin",0);
//var_dump($response);
//exit;

//スクレイピングURL
$url = "http://aws.typepad.com/marketplace_jp/ranking_data/";

$c=0;$c2=0;
$buf = @file_get_contents($url);
$data = array();
if($buf){
	for($i=1;$i<=4;$i++){
		$matches = array();
		if($i == 1){
			preg_match_all( "/<ul><div class\=\"ASJreportTitle\">(.*?)<\/ul>/is",  $buf,  $matches,  PREG_PATTERN_ORDER );
		}else{
			preg_match_all( "/<ul><div class\=\"ASJreportTitle".$i."\">(.*?)<\/ul>/is",  $buf,  $matches,  PREG_PATTERN_ORDER );
		}
		//var_dump($matches);
		foreach($matches[1] as $key => $val){
			//var_dump($val);
			//カテゴリ取得
			$matches2 = array();
			preg_match_all( "/<span>(.*?)<\/span>/is",  $val,  $matches2,  PREG_PATTERN_ORDER );
			if($matches2){
				if($matches2[1][0] != "本"
				 and $matches2[1][0] != "PCソフト"
				  and $matches2[1][0] != "美容"
				   and $matches2[1][0] != "アパレル"
				   ){
				//	echo $matches2[1][0]."\n";
					$data[$c]["category"] = strip_tags($matches2[1][0]);
				}
			}
			//リンクを取得
			$matches2 = array();
			//preg_match_all( "/<li class\=\"\w+\"><a href\=\"(.*?)\" target\=\"_blank\">(.*?)<\/a><\/li>/is",  strip_tags($val,'<li><a>'),  $matches2,  PREG_PATTERN_ORDER );
			preg_match_all( "/<a href\=\"(.*?)\".*?>(.*?)<\/a>/is",  strip_tags($val,'<a>'),  $matches2,  PREG_PATTERN_ORDER );
			if($matches2){
				
				foreach($matches2[1] as $key => $val2){
					$str = strip_tags($matches2[2][$key],'<li><a>');

						$ok=0;
						if($data[$c]["category"] == "ミュージック" and $str == "売上額TOP1000（新品・中古合計）"){
							$ok=1;
						}elseif($data[$c]["category"] == "DVD" and $str == "売上数上位商品（新品・アダルト以外）"){
							$ok=1;
						}elseif($data[$c]["category"] == "TVゲーム" and $str == "ゲームソフト"){
							$ok=1;
						}elseif($data[$c]["category"] == "おもちゃ&ホビー" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "家電・AV機器" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "カメラ" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "パソコン・周辺機器" and $str == "周辺機器"){
							$ok=1;
						}elseif($data[$c]["category"] == "パソコン・周辺機器" and $str == "PCアクセサリ"){
							$ok=1;
						}elseif($data[$c]["category"] == "ホーム＆キッチン" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "文房具・オフィス用品" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "DIY・工具" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "カー＆バイク用品" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "スポーツ" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "シューズ&バッグ" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "ジュエリー" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "時計" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "ヘルスケア" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "ビューティー" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "ペット用品" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "ベビー・マタニティ用品" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($data[$c]["category"] == "食品＆飲料" and $str == "売上数TOP1000"){
							$ok=1;
						}elseif($str == "売上数TOP1000"){
							$ok=1;
						}
						
						//echo $data[$c]["category"]."=>".$str."\n";
						
						if($ok == 1){
							echo $data[$c]["category"]."=>".$str."\n";
							$pos = strpos($val2, "start");
							if($pos === false){
								$data[$c]["list"][] = array("link"=>urlencode($val2),"value"=>$str);
							}
						}
					}
					$c2++;
				
				//var_dump($matches2[2]);
				
			}
			
				$c++;
		}
		
	}
}
Shuffle($data);

if($data){
	//asinリストの作成
	$list = array();
	foreach($data as $key => $val){
		//カテゴリ
		//$list = array();
		$category = $val["category"];
		if(strlen($category) > 0){
		echo $category."-start\n";
		if($val["list"]){
			foreach($val["list"] as $key2 => $val2){
				if($val2["value"]){
					//小カテゴリ
					$category2 = $val2["value"];
					echo "\t".$category2."-start\n";
					//ファイルの読み込み////////////////////////////////////////////////
					$handle = fopen (urldecode($val2["link"]), "r");
					if($handle){
					$cnt=0;$koumoku = array();
					while (!feof ($handle)) {
				    	$str = fgets($handle, 4096);
				    	$ckey = explode("	",$str);
				    	if($cnt == 0){
				    		foreach($ckey as $key3 => $val3){
				    			if(strtolower($val3) == "asin"){
				    				$asin_key = $key3;
				    			}elseif(strtolower($val3) == "jan"){
				    				$jan_key = $key3;
				    			}
				    		}
				    	}else{
				    		//echo $ckey[$asin_key];
				    		//データが無いもののみ、巡回
				    		$inst = DBConnection::getConnection($DB_URI);
							$sql = "select * from `category_data` where `big_category`='".$category."' and `small_category`='".$category2."' and `asin`='".$ckey[$asin_key]."'";
							$ret = $inst->search_sql($sql);
							if($ret["count"] == 0){
				    		
				    			$list[] = array("big_category"=>$category,"small_category"=>$category2,"asin"=>$ckey[$asin_key],"jan"=>$ckey[$jan_key]);
				    		}
				    		//exit;
				    	}
				    	$cnt++;
					}
					fclose($handle);
					}else{
						//exit;
					}
					//ファイルの読み込み////////////////////////////////////////////////
				}
			}
		}
		}
	}
	
	//詳細ページURL
	$detail_url = "http://www.amazon.co.jp/dp/";
	if($list){
		foreach($list as $key => $val){
			if($val["asin"]){
				echo '<br>---- '.$val["big_category"]."=>".$val["small_category"]."=>".$val["asin"].'-start ----\n';
			$response = Get_result($keyv,$val["asin"],"asin",0);
			//sleep(1);
			$response_us = Get_result($keyv,$val["asin"],"asin",0,2);
			
				//失敗した場合再度取得
				if(strlen($response['Item'][0]['ItemAttributes']["Title"]) == 0){
					$response = Get_result($keyv,$val["asin"],"asin",0);
					sleep(1);
					$response_us = Get_result($keyv,$val["asin"],"asin",0,2);
				}
				//それでもない場合は飛ばす
				if(strlen($response['Item'][0]['ItemAttributes']["Title"]) > 0){
				
				

					//詳細ページ取得
					$contents = file_get_contents($detail_url.$val["asin"], false, $context);
					$contents = mb_convert_encoding($contents,'UTF-8','auto');
					$contents = str_replace(array("\r\n","\r","\n"), '', $contents);
					//詳細ページ情報取得
						$list2 = array();
						$list2["asin"] = $val["asin"];
						if($val["jan"]){
							$list2["jan"] = $val["jan"];
						}else{
							$list2["jan"] = $response['Item'][0]['ItemAttributes']["EAN"];
						}
						//商品タイトル
						
							$match1 = array();
							preg_match_all('/<span id=\"btAsinTitle\" >(.*?)<\/span> <\!\-\-aoeui\-\->/', $contents, $match1);
							$convmap = array (0x0, 0xffff, 0, 0xffff);
							//$list["title"] = mb_decode_numericentity($match1[1][0], $convmap,'shift-jis');
							
							//if(strlen($list2["item_name"]) == 0){
								//$list2["item_name"] = $response['Item'][0]["ItemAttributes"]["Title"];
								$list2["item_name"] = strip_tags(mb_decode_numericentity($match1[1][0], $convmap,'shift-jis'));
							//}
						if(strlen($list2["item_name"]) == 0){
							$match1 = array();
							preg_match_all('/title\":\"(.*?)\"/', $contents, $match1);
							$list2["item_name"] = strip_tags($match1[1][0]);
						}
						if(strlen($match1[1][0]) == 0){
							$list2["item_name"] = $response['Item'][0]['ItemAttributes']["Title"];
						}
						//メーカー名
						if($response['Item'][0]["ItemAttributes"]["Brand"]){
							$list2["brand"] = $response['Item'][0]["ItemAttributes"]["Brand"];
						}else{
							//$match1 = array();
							//preg_match_all('/<\/h1>.*?<a href=\".*?\">(.*?)<\/a>/', $contents, $match1);
							//$list["brand"] = $match1[1][0];
						}
						
						//評価の数
						$match1 = array();
						preg_match_all('/<span class=\"swSprite s_star.*?\" title=\"5つ星のうち ([+-]?[0-9]*[\.]?[0-9]+)\".*?><span>5つ星のうち/', $contents, $match1);
						$list2["vote"] = trim($match1[1][0]);
						//最安値
						if($response['Item'][0]["ItemAttributes"]["TradeInValue"]["Amount"]){
							$list2["saiyasu_price"] = $response['Item'][0]["OfferSummary"]["Offer"]["LowestNewPrice"]["Amount"];
						}else{
							$match1 = array();
							preg_match_all("/<b class=\"priceLarge\">.*?(\b\d{1,3}(,\d{3})*\b)/", $contents, $match1);
							if($match1[1][0]){
								$list["saiyasu_price"] = trim(str_replace(array("￥","\\",","),"",$match1[1][0]));
							}else{
								$match1 = array();
								preg_match_all("/<span id=\"priceblock_ourprice\" class=\"a\-size-medium a\-color\-price\">￥ (.*?)<\/span>/", $contents, $match1);
								$list2["saiyasu_price"] = str_replace(",","",$match1[1][0]);
							}
						}
						//カート価格
						//if($response['Item'][0]["Offers"]["Offer"]["OfferListing"]["Price"]["Amount"]){
							$list2["price"] = $response['Item'][0]["Offers"]["Offer"]["OfferListing"]["Price"]["Amount"];
						//}
						//USカート価格
						//if($response_us['Item'][0]["Offers"]["Offer"]["OfferListing"]["Price"]["FormattedPrice"]){
							
							if($response_us['Item'][0]["Offers"]["Offer"]["OfferListing"]["Price"]["FormattedPrice"]){
								$list2["us_price"] = $response_us['Item'][0]["Offers"]["Offer"]["OfferListing"]["Price"]["FormattedPrice"];
							}else{
								$list2["us_price"] = $response_us['Item'][0]["OfferSummary"]["Offer"]["LowestNewPrice"]["FormattedPrice"];
							}
							if($list2["us_price"] == "Too low to display"){
								$list2["us_price"] = $response_us['Item'][0]["ItemAttributes"]["ListPrice"]["FormattedPrice"];
							}
							
							$list2["us_price"] = str_replace("$","",$list2["us_price"]);
							$list2["us_price"] = str_replace("Too low to display","",$list2["us_price"]);
						//}
						//大きさ
						$match1 = array();
						preg_match_all('/size\-weight\"><td .*?>.*?<\/td><td class=\"value\">(.*?)<\/td><\/tr>/', $contents, $match1);
						if(count($match1[1]) == 1){
							$list2["size"] = $match1[1][0];
						}else{
							$list2["size"] = $match1[1][1];
						}
						//重さ
						$match1 = array();
						preg_match_all('/<tr class=\"shipping\-weight\"><td class=\"label\">.*?<\/td><td class=\"value\">(.*?)<\/td>/', $contents, $match1);
						if($match1[1][0]){
							$list2["weight"] = $match1[1][0];
						}else{
							$match1 = array();
							preg_match_all('/<li><b>発送重量:<\/b> (.*?)<\/li>/', $contents, $match1);
							$list2["weight"] = $match1[1][0];
						}
						//カテゴリ取得
					if($response_us['Item'][0]["ItemAttributes"]["ProductGroup"]){
						$list["category"] = $category_array[$response_us['Item'][0]["ItemAttributes"]["ProductGroup"]];
						echo $response_us['Item'][0]["ItemAttributes"]["ProductGroup"]."\n";
					}else{
						$match1 = array();
						preg_match_all('/<option current=\"parent\" selected=\"selected\" value=\"search-alias=.*?\">(.*?)<\/option>/',$contents, $match1);
						if($match1[1][0]){
							$list2["category"] = strip_tags($match1[1][0]);
						}else{
							$match1 = array();
							preg_match_all('/^(.*?) - \d+位 \(<a href=/',$contents, $match1);
							if($match1[1][0]){
								$list2["category"] = strip_tags($match1[1][0]);
							
							}
						}
					}
						//ランキング
						if($response['Item'][0]["SalesRank"]){
							$list2["ranking"] = $response['Item'][0]["SalesRank"];
						}else{
							$match1 = array();
							preg_match_all('/<span class=\"zg_hrsr_rank\">(\d+)位<\/span>/', $contents, $match1);
							$list2["ranking"] = $match1[1][0];
						}
						//ランキングURLが取得できればレビュー件数を取得 typeカラムを使用
						if($response['Item'][0]["CustomerReviews"]["IFrameURL"]){
							$contents2 = file_get_contents($response['Item'][0]["CustomerReviews"]["IFrameURL"], false, $context);
							$contents2 = mb_convert_encoding($contents2,'UTF-8','shift-jis');
							$contents2 = str_replace(array("\r\n","\r","\n"), '', $contents2);
							$match1 = array();
							preg_match_all('/<b>(.*?)レビュー<\/b>/', $contents2, $match1);
							$list2["type"] = str_replace(",","",$match1[1][0]);
						}
						//出品者数
						$match1 = array();
						preg_match_all('/([0-9]+)の新品\/中古品の出品を見る<\/a>/', $contents, $match1);
						if($match1[1][0]){
							$list2["member"] = $match1[1][0];
						}else{
							$match1 = array();
							preg_match_all('/新品の出品：([0-9]+)/', $contents, $match1);
							$list2["member"] = $match1[1][0];
						}
						
						//画像の取得
						if($response['Item'][0]["LargeImage"]["URL"]){
							$list2["img"] = $response['Item'][0]["LargeImage"]["URL"];
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
									if($list2["img"] and $cnt > 0){
										break;
									}
									$list2["img"] = $valm;
									$cnt++;
								}
							}
						}
						
						//このデータが無ければインサート
						if($list2["item_name"]){
							$inst = DBConnection::getConnection($DB_URI);
							$sql = "select * from `category_data` where `big_category`='".$val["big_category"]."' and `small_category`='".$val["small_category"]."' and `asin`='".$val["asin"]."'";
							$ret = $inst->search_sql($sql);
							if($ret["count"] == 0){
								//フレーズをわけ中国語に変換し、登録する
								//タイトルを中国語に(MSN)
								$china_title = getTranslate_m($list2["item_name"],"ja","zh-CHS");
								if(strstr($china_title,"AppId is over the quota")){
									$china_title = "";
								}
								//タイトル分解
								$micro = array();$china_key = "";
								$phrase = explode(",",Insert_tag2($list2["item_name"]));
								if($phrase){
									foreach($phrase as $val2){
										$micro[] = getTranslate_m($val2,"ja","zh-CHS");
									}
									if($micro){
										$china_key = implode(",",$micro);
									}
								}
								$sql = "insert into `category_data`(`big_category`,`small_category`,`item_name`,`img`,`brand`,`vote`,`asin`,`jan`,`us_price`,`price`,`saiyasu_price`,`size`,`weight`,`category`,`ranking`,`member`,`type`,`china_title`,`china_key`) values ";
								$sql .= "('".$val["big_category"]."','".$val["small_category"]."','".q($list2["item_name"])."','".$list2["img"]."','".q($list2["brand"])."','".$list2["vote"]."','".$val["asin"]."','".$list2["jan"]."','".$list2["us_price"]."','".$list2["price"]."','".$list2["saiyasu_price"]."','".$list2["size"]."','".$list2["weight"]."','".$list2["category"]."','".$list2["ranking"]."','".$list2["member"]."','".$list2["type"]."','".$china_title."','".$china_key."')";
								$ret = $inst->db_exec($sql);
							}
						}
						//print_r($list2);
						//sleep(1);
				}else{
					continue;
				}
			}
		}
	
		
	}
}
?>