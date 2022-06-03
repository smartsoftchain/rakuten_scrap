<?php
header("Content-Type: text/html; charset=utf-8");
ini_set("memory_limit", "512M");
ini_set('max_execution_time', '3600000');
ini_set( 'display_errors', 0 );
set_time_limit(0);

include_once("lists.php");
	//掃き出しようフォルダ
	$write = "write2";
	

	//元データ削除
	exec("rm -rf ".$write."/*.*");

$url = "http://d-kokuya.shop-pro.jp/?mode=srh&cid=&keyword=&page=";

$shop_name = "縁日玩具卸 株式会社大国屋の通販サイト";

$fh = fopen($write.'/start.csv', "a");
$fha = fopen($write.'/second_jp_asin_ari.csv', "a");
$fhn = fopen($write.'/second_jp_asin_nashi.csv', "a");
$fhusa = fopen($write.'/third_jp_asin_ari_us_asin_ari.csv', "a");
$fhusn = fopen($write.'/third_jp_asin_ari_us_asin_nashi.csv', "a");
$fhusa2 = fopen($write.'/fourth_jp_asin_nashi_us_asin_ari.csv', "a");
$fhusn2 = fopen($write.'/fourth_jp_asin_nashi_us_asin_nashi.csv', "a");


$a =  array("ショップ名","JANコード","商品名","商品画像URL1","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL");
fputcsv($fh, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL");
fputcsv($fha, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL");
fputcsv($fhn, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusa, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusn, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL","ASIN","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusa2, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品画像URL2","商品画像URL3","商品画像URL4","商品価格","商品ページURL","ASIN","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusn2, Decode_array2($a));

	$context = stream_context_create(array('http' => array(
	'method' => 'GET',
	'header' => 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
	)));


//スクレイピング開始。

for($i=1;$i<=38;$i++){
	$urls = $url.$i;
	echo '<br>---- '.$para.'ページ 開始 ----<br><br>';
	$get_contents = file_get_contents($urls, false, $context);
	$get_contents = mb_convert_encoding($get_contents,'UTF-8','auto');
	$get_contents = str_replace(array("\r\n","\r","\n"), '', $get_contents);
	
	$matches = array();
	preg_match_all("/<a href=\"\?pid=(\d+)\"><div class=\"expand\">/is", $get_contents, $matches); 
	foreach($matches[1] as $key => $val){
		echo $val."<br />";
		$contents = file_get_contents("http://d-kokuya.shop-pro.jp/?pid=".$val, false, $context);
		$contents = mb_convert_encoding($contents,'UTF-8','auto');
		$contents = str_replace(array("\r\n","\r","\n"), '', $contents);
		$list = array();
		
		//型番
		$match1 = array();
		preg_match('/<td class=\"cell_2\"><div class=\"cell_mar\">(\d+)<\/div><\/td>/', $contents, $match1);
		$list["jan"] = $match1[1];
		
		//商品名
		$match1 = array();
		preg_match('/<H1>(.*?)<\/H1>/', $contents, $match1);
		$list["title"] = Decode_str($match1[1]);
		//商品画像　メイン
		$match1 = array();
		preg_match('/<TD>[\s　]*<img src=\"(.*?)\" class=\"main_img\" alt/is', $contents, $match1);
		$list["img0"] = $match1[1];
		
		//その他画像
		$matches1 = array();
		preg_match_all("/<div class=\"sub\"><a target=\"_blank\" href=\"(.*?)\"><div class=\"expand\">/is", $contents, $matches1); 
		foreach($matches1[1] as $key => $val){
			$list["img".($key+1)] = $val;
		}
		//商品価格
		$match1 = array();
		preg_match('/<p class=\"price_detail\">(.*?)円\(本体/', $contents, $match1);
		$list["price"] = str_replace(",","",$match1[1]);
		//商品ページ
		$list["url"] = "http://d-kokuya.shop-pro.jp/?pid=".$val;

		fputcsv($fh, $list);
		$amazon = SearchAmazon($list["jan"]);
					if($amazon[0]){
						$csva = array();
						$csva[] = Decode_str($shop_name);
						$csva[] = $list["jan"];
						$csva[] = $list["title"];
						$csva[] = $list["img0"];
						$csva[] = $list["img1"];
						$csva[] = $list["img2"];
						$csva[] = $list["img3"];
						$csva[] = $list["price"];
						$csva[] = $list["url"];
						$csva[] = $amazon[0];
						$csva[] = Decode_str($amazon[1]);
						$csva[] = $amazon[2];
						$csva[] = $amazon[3];
						fputcsv($fha, $csva);
						
						$amazon_us = SearchAmazon_us($amazon[0],"asin");
						if($amazon_us[0]){
							$csvusa = array();
							$csvusa[] = Decode_str($shop_name);
							$csvusa[] = $list["jan"];
							$csvusa[] = $list["title"];
							$csvusa[] = $list["img0"];
							$csvusa[] = $list["img1"];
							$csvusa[] = $list["img2"];
							$csvusa[] = $list["img3"];
							$csvusa[] = $list["price"];
							$csvusa[] = $list["url"];
							$csvusa[] = $amazon[0];
							$csvusa[] = Decode_str($amazon[1]);
							$csvusa[] = $amazon[2];
							$csvusa[] = $amazon[3];
							$csvusa[] = Decode_str($amazon_us[1]);
							$csvusa[] = $amazon_us[2];
							fputcsv($fhusa, $csvusa);
							
						}else{
							$csvusn = array();
							$csvusn[] = Decode_str($shop_name);
							$csvusn[] = $list["jan"];
							$csvusn[] = $list["title"];
							$csvusn[] = $list["img0"];
							$csvusn[] = $list["img1"];
							$csvusn[] = $list["img2"];
							$csvusn[] = $list["img3"];
							$csvusn[] = $list["price"];
							$csvusn[] = $list["url"];
							$csvusn[] = $amazon[0];
							$csvusn[] = Decode_str($amazon[1]);
							$csvusn[] = $amazon[2];
							$csvusn[] = $amazon[3];
							$csvusn[] = Decode_str($amazon_us[1]);
							$csvusn[] = $amazon_us[2];
							fputcsv($fhusn, $csvusn);
						}
						
						
					}else{
						$csvn = array();
						$csvn[] = Decode_str($shop_name);
						$csvn[] = $list["jan"];
						$csvn[] = $list["title"];
						$csvn[] = $list["img0"];
						$csvn[] = $list["img1"];
						$csvn[] = $list["img2"];
						$csvn[] = $list["img3"];
						$csvn[] = $list["price"];
						$csvn[] = $list["url"];
						$csvn[] = $amazon[0];
						$csvn[] = Decode_str($amazon[1]);
						$csvn[] = $amazon[2];
						$csvn[] = $amazon[3];
						fputcsv($fhn, $csvn);
						//fource
						$amazon_us = SearchAmazon_us($list["jan"],"ean");
						if($amazon_us[0]){
							$csvusa2 = array();
							$csvusa2[] = Decode_str($shop_name);
							$csvusa2[] = $list["jan"];
							$csvusa2[] = $list["title"];
							$csvusa2[] = $list["img0"];
							$csvusa2[] = $list["img1"];
							$csvusa2[] = $list["img2"];
							$csvusa2[] = $list["img3"];
							$csvusa2[] = $list["price"];
							$csvusa2[] = $list["url"];
							$csvusa2[] = $amazon_us[0];
							$csvusa2[] = Decode_str($amazon_us[1]);
							$csvusa2[] = $amazon_us[2];
							fputcsv($fhusa2, $csvusa2);
							
						}else{
							$csvusn2 = array();
							$csvusn2[] = Decode_str($shop_name);
							$csvusn2[] = $list["jan"];
							$csvusn2[] = $list["title"];
							$csvusn2[] = $list["img0"];
							$csvusn2[] = $list["img1"];
							$csvusn2[] = $list["img2"];
							$csvusn2[] = $list["img3"];
							$csvusn2[] = $list["price"];
							$csvusn2[] = $list["url"];
							$csvusn2[] = $amazon_us[0];
							$csvusn2[] = Decode_str($amazon_us[1]);
							$csvusn2[] = $amazon_us[2];
							fputcsv($fhusn2, $csvusn2);
						}
					}
	
	}
}


fclose($fh);
fclose($fha);
fclose($fhn);
fclose($fhusa);
fclose($fhusn);
fclose($fhusa2);
fclose($fhusn2);


//amazonにあるかどうか
function SearchAmazon($jan){
	$include_path= "/var/www/html/rakuten_scrap/PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	
	for($j=21; $j<100; $j++){
		$amazon_app = file_get_contents("common/amazon_access_key{$j}.txt");//アマゾンアクセスキー
		$amazon_app_sec = file_get_contents("common/amazon_secret{$j}.txt");//アマゾンシークレット
		if(isEmpty2($amazon_app)==TRUE && isEmpty2($amazon_app_sec)==TRUE){
			$amazon_app_id_list[$j] = $amazon_app;
			$amazon_app_sec_list[$j]= $amazon_app_sec;
		}
	}
	$keyArray = array_rand($amazon_app_id_list,1);
	$amazon_access_key = $amazon_app_id_list[$keyArray];
	$amazon_secret = $amazon_app_sec_list[$keyArray];

	$keyArray = array_rand($amazon_app_id_list,1);
	$amazon_access_key2 = $amazon_app_id_list[$keyArray];
	$amazon_secret2 = $amazon_app_sec_list[$keyArray];
	
		$amazon = new Services_Amazon($amazon_access_key, $amazon_secret,"vc-22");
		$amazon->setBaseUrl('http://ecs.amazonaws.jp/onca/xml');
		$type = "ean";
		if($type=="asin"){
			$response = $amazon->ItemLookup(implode(",",$val),array('ResponseGroup' => 'ItemAttributes'));
		}elseif($type=="ean"){
			$options['IdType'] = 'EAN';
			$options['Version'] = '2010-09-01';
			$options['SearchIndex'] = 'All';
			$options['Condition'] = 'All';
			$options['MerchantId'] = 'All';
			$options['ResponseGroup'] = 'Large,OfferFull,Offers';
			//$options['ResponseGroup'] = 'Small';
			$response = $amazon->ItemLookup($jan,$options);
			if(@get_class($response)!="PEAR_Error"){
				$item = $response["Item"][0];
				if($item["LargeImage"]["URL"]){
					$img = $item["LargeImage"]["URL"];
				}elseif($item["MediumImage"]["URL"]){
					$img = $item["MediumImage"]["URL"];
				}elseif($item["SmallImage"]["URL"]){
					$img = $item["SmallImage"]["URL"];
				}
				$title = $item["ItemAttributes"]["Title"];
				$price = $item["OfferSummary"]["LowestNewPrice"]["Amount"];
				$asin = $item["ASIN"];
			}else{
				return array();
			}
			
		}
		return array($asin,$title,$price,$img);
}

function SearchAmazon_us($jan,$type){
	$include_path= "/var/www/html/rakuten_scrap/PEAR";
	ini_set('include_path', $include_path);
	require_once 'Services/Amazon.php';
	
	for($j=21; $j<100; $j++){
		$amazon_app = file_get_contents("common/amazon_access_key{$j}.txt");//アマゾンアクセスキー
		$amazon_app_sec = file_get_contents("common/amazon_secret{$j}.txt");//アマゾンシークレット
		if(isEmpty2($amazon_app)==TRUE && isEmpty2($amazon_app_sec)==TRUE){
			$amazon_app_id_list[$j] = $amazon_app;
			$amazon_app_sec_list[$j]= $amazon_app_sec;
		}
	}
	$keyArray = array_rand($amazon_app_id_list,1);
	$amazon_access_key = $amazon_app_id_list[$keyArray];
	$amazon_secret = $amazon_app_sec_list[$keyArray];

	$keyArray = array_rand($amazon_app_id_list,1);
	$amazon_access_key2 = $amazon_app_id_list[$keyArray];
	$amazon_secret2 = $amazon_app_sec_list[$keyArray];
	
		$amazon = new Services_Amazon($amazon_access_key, $amazon_secret,"vc-22");
		$amazon->setBaseUrl('http://ecs.amazonaws.com/onca/xml');
		//$type = "ean";
		if($type=="asin"){
			$response = $amazon->ItemLookup($jan,array('ResponseGroup' => 'Large,OfferFull,Offers'));
			//$item = $response["Item"][0];
		}elseif($type=="ean"){
			$options['IdType'] = 'EAN';
			$options['Version'] = '2010-09-01';
			$options['SearchIndex'] = 'All';
			$options['Condition'] = 'All';
			$options['MerchantId'] = 'All';
			$options['ResponseGroup'] = 'Large,OfferFull,Offers';
			//$options['ResponseGroup'] = 'Small';
			$response = $amazon->ItemLookup($jan,$options);
			
		}
			if(@get_class($response)!="PEAR_Error"){
				$item = $response["Item"][0];
				if($item["LargeImage"]["URL"]){
					$img = $item["LargeImage"]["URL"];
				}elseif($item["MediumImage"]["URL"]){
					$img = $item["MediumImage"]["URL"];
				}elseif($item["SmallImage"]["URL"]){
					$img = $item["SmallImage"]["URL"];
				}
				$title = $item["ItemAttributes"]["Title"];
				$price = $item["OfferSummary"]["LowestNewPrice"]["Amount"];
				$asin = $item["ASIN"];
				return array($asin,$title,$price,$img);
			}else{
				return array();
			}
		//return array($asin,$title,$price,$img);
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
//入力されているかチェック
function isEmpty2($vali){
	
	if(strlen($vali)>1){
		
		if($vali=="商品名・型番などを入力してください"){
			return false;	
		}else{
			return true;
		}
	
	}else{
		return false;
	}//if(strlen($vali)>1)
	
}
function Decode_str($val){
	$str = mb_convert_encoding($val, "shift-jis", "UTF-8");
	return $str;
}
function Decode_array2($val){
	$new_val = array();
	foreach($val as $key => $val2){
		$new_val[$key] = Decode_str($val2);
	}
	return $new_val;
}
?>