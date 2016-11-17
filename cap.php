<?php

include($_SERVER['DOCUMENT_ROOT'].'/admin/include/kcaptcha/kcaptcha.php');

session_start();

$captcha = new KCAPTCHA();

//if($_REQUEST[session_name()]){
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
	
//}
var_dump($captcha->getKeyString());die();
?>