<?php

  if($_GET["logout"]){
  	unset($_SESSION["admin_login"]);
  	unset($_SESSION["admin_password"]);
  }

  /*if(($_SESSION["admin_login"]!=$admin_options["admin_login"])||($_SESSION["admin_password"]!=$admin_options["admin_password"])){
  	if(($_POST["p_login"]!=$admin_options["admin_login"])||($_POST["p_password"]!=$admin_options["admin_password"])){
  		$output .= "
				<html><head><title>Вход</title>
				<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" />
				<link href=\"$admin_options[url]css/style.css\" rel=\"stylesheet\" type=\"text/css\">
				</head>
				<body>
				<center>
				<form action=\"index.php\" method=\"POST\">
				<table style=\"border:1px solid black\">
				<input type=\"hidden\" name=\"p_cmd\" value=\"login\">
				<tr><td align=\"right\">Логин:</td><td><input type=\"text\" name=\"p_login\" size=\"20\" class=\"text\"></td></tr>
				<tr><td align=\"right\">Пароль:</td><td><input type=\"password\" name=\"p_password\" size=\"20\" class=\"text\"></td></tr>
				<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"Войти\" class=\"button\"></td></tr>
				</table>
				</form>
				</center>
				</body>
				</html>
  		";
  		echo $output;
  		die();
  	}else{
			$_SESSION["admin_login"]=$admin_options["admin_login"];
			$_SESSION["admin_password"]=$admin_options["admin_password"];
  	}
  }
*/
?>