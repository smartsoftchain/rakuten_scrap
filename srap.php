<?php
header("Content-Type: text/html; charset=utf-8");
ini_set("memory_limit", "512M");
ini_set('max_execution_time', '3600000');
ini_set( 'display_errors', 0 );
set_time_limit(0);

include_once("lists.php");
	//掃き出しようフォルダ
	$write = "write";
	

	//元データ削除
	exec("rm -rf ".$write."/*.*");

//スクレイピングするサイト
$rakutensites = array(
	array("name"=>"ひゃくえんもん","code"=>"hyakuemon"),
	array("name"=>"万天プラザ","code"=>"mantenpuraza"),
	array("name"=>"EZ-STORE","code"=>"suehiro-cop")
);
$yahoosites = array(
	array("name"=>"100円雑貨＆日用品卸 BABABA","code"=>"kawauchi"),
	array("name"=>"100円ショップ 100均Net","code"=>"100kinnet"),
	array("name"=>"100円ストアYAMANI","code"=>"100enstoreyamani")
);
/*
foreach($rakutensites as $key => $val){
	$rd_keys = array_rand($rakutenkey_list, 1);
	$k = $rakutenkey_list[$rd_keys];
	$rakuten_base_url="https://app.rakuten.co.jp/services/api/IchibaItem/Search/20140222?applicationId=".$k."&hits=30&page=1&format=json&availability=1&shopCode=".$val["code"];
	$rakuten_xml = @file_get_contents($rakuten_base_url);
	$rakuten_xml = json_decode($rakuten_xml,true);
	$rakuten_response = $rakuten_xml['Items'];
	echo $val["name"]."=>".$rakuten_xml["count"]."\n";
//	var_dump($rakuten_xml);

}


foreach($yahoosites as $key => $val){
	$rd_keys = array_rand($yahoo_list, 1);
	$k = $yahoo_list[$rd_keys];
	$yurl = "http://shopping.yahooapis.jp/ShoppingWebService/V1/itemSearch?appid=".$k."&hits=50&offset=1&store_id=".$val["code"];
	//echo $yurl."<br />\n";
		
	$yxml = simplexml_load_file($yurl);
	echo $val["name"]."=>".$yxml["totalResultsAvailable"]."\n";

	
}

exit;
*/


$fh = fopen($write.'/start.csv', "a");
$fha = fopen($write.'/second_jp_asin_ari.csv', "a");
$fhn = fopen($write.'/second_jp_asin_nashi.csv', "a");
$fhusa = fopen($write.'/third_jp_asin_ari_us_asin_ari.csv', "a");
$fhusn = fopen($write.'/third_jp_asin_ari_us_asin_nashi.csv', "a");
$fhusa2 = fopen($write.'/fourth_jp_asin_nashi_us_asin_ari.csv', "a");
$fhusn2 = fopen($write.'/fourth_jp_asin_nashi_us_asin_nashi.csv', "a");


$a =  array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL");
fputcsv($fh, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL");
fputcsv($fha, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL");
fputcsv($fhn, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusa, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL","ASIN","日本アマゾンでの商品名","日本アマゾンでの商品価格","日本アマゾン商品画像URL","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusn, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL","ASIN","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusa2, Decode_array2($a));

$a = array("ショップ名","JANコード","商品名","商品画像URL","商品価格","商品ページURL","ASIN","USアマゾンでの商品名","USアマゾンでの商品価格");
fputcsv($fhusn2, Decode_array2($a));


//楽天開始

echo "rakuten---start\n";
foreach($rakutensites as $key => $val){
	echo $val["name"]."---start\n";
	for($i=0;$i<100;$i++){
		$rd_keys = array_rand($rakutenkey_list, 1);
		$k = $rakutenkey_list[$rd_keys];
		$rakuten_base_url="https://app.rakuten.co.jp/services/api/IchibaItem/Search/20140222?applicationId=".$k."&hits=30&page=".($i+1)."&format=json&availability=1&shopCode=".$val["code"];
		echo $rakuten_base_url."<br />";
		$rakuten_xml = @file_get_contents($rakuten_base_url);
		$rakuten_xml = json_decode($rakuten_xml,true);
		$rakuten_response = $rakuten_xml['Items'];

		if($rakuten_response){
			foreach($rakuten_response as $rakuten_value){
				$item = $rakuten_value["Item"];
			
					//$janarray = explode(":",$item["itemCode"]);
					$jan="";
					$contents = @file_get_contents($item['itemUrl']);
					$contents = mb_convert_encoding($contents,'shift-jis','auto');
					$contents = str_replace(array("\r\n","\r","\n"), '', $contents);
					$match1 = array();
					preg_match('/<td nowrap><span class=\"item_number\">(\d+)<\/span><\/td>/',$contents, $match1);
					if($match1){
						$jan = $match1[1];
					}
					
					//初期データ
					$csv = array();
					$csv[] = Decode_str($val["name"]);
					$csv[] = trim($jan);
					$csv[] = Decode_str($item["itemName"]);
					$csv[] = $item['mediumImageUrls'][0]["imageUrl"];
					$csv[] = $item['itemPrice'];
					$csv[] = $item['itemUrl'];
					fputcsv($fh, $csv);
					//JANからamazonを検索　ASINが存在するかどうか
					$amazon = SearchAmazon(trim($jan));
					if($amazon[0]){
						$csva = array();
						$csva[] = Decode_str($val["name"]);
						$csva[] = trim($jan);
						$csva[] = Decode_str($item["itemName"]);
						$csva[] = $item['mediumImageUrls'][0]["imageUrl"];
						$csva[] = $item['itemPrice'];
						$csva[] = $item['itemUrl'];
						$csva[] = $amazon[0];
						$csva[] = Decode_str($amazon[1]);
						$csva[] = $amazon[2];
						$csva[] = $amazon[3];
						fputcsv($fha, $csva);
						
						$amazon_us = SearchAmazon_us($amazon[0],"asin");
						if($amazon_us[0]){
							$csvusa = array();
							$csvusa[] = Decode_str($val["name"]);
							$csvusa[] = trim($jan);
							$csvusa[] = Decode_str($item["itemName"]);
							$csvusa[] = $item['mediumImageUrls'][0]["imageUrl"];
							$csvusa[] = $item['itemPrice'];
							$csvusa[] = $item['itemUrl'];
							$csvusa[] = $amazon[0];
							$csvusa[] = Decode_str($amazon[1]);
							$csvusa[] = $amazon[2];
							$csvusa[] = $amazon[3];
							$csvusa[] = Decode_str($amazon_us[1]);
							$csvusa[] = $amazon_us[2];
							fputcsv($fhusa, $csvusa);
							
						}else{
							$csvusn = array();
							$csvusn[] = Decode_str($val["name"]);
							$csvusn[] = trim($jan);
							$csvusn[] = Decode_str($item["itemName"]);
							$csvusn[] = $item['mediumImageUrls'][0]["imageUrl"];
							$csvusn[] = $item['itemPrice'];
							$csvusn[] = $item['itemUrl'];
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
						$csvn[] = Decode_str($val["name"]);
						$csvn[] = trim($jan);
						$csvn[] = Decode_str($item["itemName"]);
						$csvn[] = $item['mediumImageUrls'][0]["imageUrl"];
						$csvn[] = $item['itemPrice'];
						$csvn[] = $item['itemUrl'];
						$csvn[] = $amazon[0];
						$csvn[] = Decode_str($amazon[1]);
						$csvn[] = $amazon[2];
						$csvn[] = $amazon[3];
						fputcsv($fhn, $csvn);
						//fource
						$amazon_us = SearchAmazon_us(trim($jan),"ean");
						if($amazon_us[0]){
							$csvusa2 = array();
							$csvusa2[] = Decode_str($val["name"]);
							$csvusa2[] = trim($jan);
							$csvusa2[] = Decode_str($item["itemName"]);
							$csvusa2[] = $item['mediumImageUrls'][0]["imageUrl"];
							$csvusa2[] = $item['itemPrice'];
							$csvusa2[] = $item['itemUrl'];
							$csvusa2[] = $amazon_us[0];
							$csvusa2[] = Decode_str($amazon_us[1]);
							$csvusa2[] = $amazon_us[2];
							fputcsv($fhusa2, $csvusa2);
							
						}else{
							$csvusn2 = array();
							$csvusn2[] = Decode_str($val["name"]);
							$csvusn2[] = trim($jan);
							$csvusn2[] = Decode_str($item["itemName"]);
							$csvusn2[] = $item['mediumImageUrls'][0]["imageUrl"];
							$csvusn2[] = $item['itemPrice'];
							$csvusn2[] = $item['itemUrl'];
							$csvusn2[] = $amazon_us[0];
							$csvusn2[] = Decode_str($amazon_us[1]);
							$csvusn2[] = $amazon_us[2];
							fputcsv($fhusn2, $csvusn2);
						}
						
						
						
						
					}
			}
		}else{
			break;
		}
	}
}

//Yahoo開始


echo "yahoo---start\n";
foreach($yahoosites as $key => $val){
echo $val["name"]."---start\n";
	for($i=0;$i<100;$i++){
		$rd_keys = array_rand($yahoo_list, 1);
		$k = $yahoo_list[$rd_keys];
		$offset = $i*50;
		$yurl = "http://shopping.yahooapis.jp/ShoppingWebService/V1/itemSearch?appid=".$k."&hits=50&offset=".$offset."&store_id=".$val["code"];
		echo $yurl."<br />\n";
		
		$yxml = simplexml_load_file($yurl);
		
		if ($yxml["totalResultsReturned"] != 0) {//検索件数が0件でない場合,変数$hitsに検索結果を格納します。
			
			//$hits = $yxml->Result->Hit;
			$hits = (array)$yxml->Result;
			$hits = $hits["Hit"];
			//var_dump($hits);
			if($hits){
				foreach($hits as $hit){
					
					$hit = (array)$hit;
					echo $hit["JanCode"]."<br />";
					$amazon = SearchAmazon($hit["JanCode"]);
					$hit['Name'];//商品名
					$hit['Url'];//商品詳細ＵＲＬ（通常）
					$hit['Store']->Name."（Yahooショッピング）";//店名
					$hit['Image']->Medium;//画像
					$hit['Price'];//価格
						
					$csv = array();
					$csv[] = Decode_str($val["name"]);
					$csv[] = $hit["JanCode"];
					$csv[] = Decode_str($hit['Name']);
					$csv[] = $hit['Image']->Medium;
					$csv[] = $hit['Price'];
					$csv[] = $hit['Url'];
					fputcsv($fh, $csv);
					
					if($amazon[0]){
						$csva = array();
						$csva[] = Decode_str($val["name"]);
						$csva[] = $hit["JanCode"];
						$csva[] = Decode_str($hit['Name']);
						$csva[] = $hit['Image']->Medium;
						$csva[] = $hit['Price'];
						$csva[] = $hit['Url'];
						$csva[] = $amazon[0];
						$csva[] = Decode_str($amazon[1]);
						$csva[] = $amazon[2];
						$csva[] = $amazon[3];
						fputcsv($fha, $csva);
						
						$amazon_us = SearchAmazon_us($amazon[0],"asin");
						if($amazon_us[0]){
							$csvusa = array();
							$csvusa[] = Decode_str($val["name"]);
							$csvusa[] = $hit["JanCode"];
							$csvusa[] = Decode_str($hit['Name']);
							$csvusa[] = $hit['Image']->Medium;
							$csvusa[] = $hit['Price'];
							$csvusa[] = $hit['Url'];
							$csvusa[] = $amazon_us[0];
							$csvusa[] = Decode_str($amazon_us[1]);
							$csvusa[] = $amazon_us[2];
							$csvusa[] = $amazon_us[3];
							fputcsv($fhusa, $csvusa);
							
						}else{
							$csvusn = array();
							$csvusn[] = Decode_str($val["name"]);
							$csvusn[] = $hit["JanCode"];
							$csvusn[] = Decode_str($hit['Name']);
							$csvusn[] = $hit['Image']->Medium;
							$csvusn[] = $hit['Price'];
							$csvusn[] = $hit['Url'];
							$csvusn[] = $amazon_us[0];
							$csvusn[] = Decode_str($amazon_us[1]);
							$csvusn[] = $amazon_us[2];
							$csvusn[] = $amazon_us[3];
							fputcsv($fhusn, $csvusn);
						}
					}else{
						$csvn = array();
						$csvn[] = Decode_str($val["name"]);
						$csvn[] = $hit["JanCode"];
						$csvn[] = Decode_str($hit['Name']);
						$csvn[] = $hit['Image']->Medium;
						$csvn[] = $hit['Price'];
						$csvn[] = $hit['Url'];
						$csvn[] = $amazon[0];
						$csvn[] = Decode_str($amazon[1]);
						$csvn[] = $amazon[2];
						$csvn[] = $amazon[3];
						fputcsv($fhn, $csvn);

						//fource
						$amazon_us = SearchAmazon_us($hit["JanCode"],"ean");
						if($amazon_us[0]){
							$csvusa2 = array();
							$csvusa2[] = Decode_str($val["name"]);
							$csvusa2[] = $hit["JanCode"];
							$csvusa2[] = Decode_str($hit['Name']);
							$csvusa2[] = $hit['Image']->Medium;
							$csvusa2[] = $hit['Price'];
							$csvusa2[] = $hit['Url'];
							$csvusa2[] = $amazon_us[0];
							$csvusa2[] = Decode_str($amazon_us[1]);
							$csvusa2[] = $amazon_us[2];
							fputcsv($fhusa2, $csvusa2);
							
						}else{
							$csvusn2 = array();
							$csvusn2[] = Decode_str($val["name"]);
							$csvusn2[] = $hit["JanCode"];
							$csvusn2[] = Decode_str($hit['Name']);
							$csvusn2[] = $hit['Image']->Medium;
							$csvusn2[] = $hit['Price'];
							$csvusn2[] = $hit['Url'];
							$csvusn2[] = $amazon_us[0];
							$csvusn2[] = Decode_str($amazon_us[1]);
							$csvusn2[] = $amazon_us[2];
							fputcsv($fhusn2, $csvusn2);
						}
					}
				}
			}
		}else{
			break;
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