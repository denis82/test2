<?php

  require "include/inc.php";

	header("Content-type: text/html; charset=windows-1251");

	$output = "<div>".create_input($_GET["p_type"], "prop_".$_GET["p_code"]."[]", "", "")."</div>";
	
	echo $output;


?>