<?php
	
	require_once("./inc/scp.php");
	$micro = "";
	$google="";
	if($_REQUEST["str"]){
		$str = $_REQUEST["str"];
		$micro = getTranslate_m($str,"ja","zh-CHS");
		$google = getTranslate_g($str, "zh-cn");
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- TemplateBeginEditable name="doctitle" -->
<title>無題ドキュメント</title>
<!-- TemplateEndEditable -->
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
</head>

<body>
<form name="form1" method="post" action="trans.php">
  <label for="str">中国語に翻訳</label>
  ワード：
  <input type="text" name="str" size="60px" id="str" value="<?php $str; ?>">
  <input type="submit" name="button" id="button" value="送信">
</form>
<div align="left">Microsoft翻訳結果<br /><p><?php echo $micro; ?></p></div>
<div align="left">Google翻訳結果<br /><p><?php echo $google; ?></p></div>
</body>
</html>
