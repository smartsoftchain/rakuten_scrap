<?php

function Get_Ua(){
	
	$ua = $_SERVER['HTTP_USER_AGENT'];

	if (strpos($ua, 'Android') !== false && strpos($ua, 'Mobile') === false){
	    return '1';
	}elseif (strpos($ua, 'Android') !== false && strpos($ua, 'Mobile') !== false){
	    return '3';
	}elseif (strpos($ua, 'iPhone') !== false){
	    return '2';
	}elseif (strpos($ua, 'iPad') !== false){
	    return '0';
	}elseif (strpos($ua, 'iPod') !== false){
	    return '1';
	}else{
	    return '0';
	}
	
	exit;
	
	/*
	include './inc/Mobile_Detect.php';
	$detect = new Mobile_Detect();
	
	if ($detect->isMobile()) {
		if($detect->isiPhone()){
			return "iphone";
		}else{
			return "Android-etc";
		}
	 
	}elseif($detect->isTablet()){//タブレット
		return "tab";
	}elseif(!$detect->isMobile() && !$detect->isTablet()){//その他PC
		return "pc";
	}
	*/
}
?>