<?php

  function auth_form() {
  	global $common_options;
		$output .= "<form method=\"post\">";
		$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"auth\">";
		$output .= "<table class='reg-form'>";
		$output .= "
      <tr>
        <td align=\"left\" class=\"name\">Логин:</td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
        <td align=\"left\" class=\"name\">Пароль:</td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
      </tr>
      <tr>
        <td align=\"left\"><input type=\"text\" name=\"login\" size=\"50\" class=\"input\"></td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
        <td align=\"left\"><input type=\"password\" name=\"password\" size=\"50\" class=\"input\"></td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
        <td><input type=\"submit\"  size=\"50\" class=\"button\" value=\"Авторизоваться\"></td>
      </tr>
      <tr>
        <td align=\"left\" colspan=2></td>
        <td align=\"left\" colspan=3><a href=\"?p_cmd=remem\">Вы забыли логин и пароль</a></td>
      </tr>

    ";
		$output .= "</table></form>";
		
		return $output;
  }

  function remem_form() {
  	global $common_options;
		$output .= "<form method=\"post\">";
		$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"remem\">";
		$output .= "<table class='reg-form'>";
		$output .= "
      <tr>
        <td align=\"left\" class=\"name\">Введите Ваш e-mail, <br>указанный при регистрации:</td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
        <td align=\"left\"><input type=\"text\" name=\"p_email\" size=\"50\" class=\"input\"></td>
      </tr>
      <tr>
        <td align=\"right\" colspan=3><input type=\"submit\"  size=\"50\" class=\"button\" value=\"Восстановить\"></td>
      </tr>

    ";
		$output .= "</table></form>";
		
		return $output;
  }

  $cmd = get_var_web("p_cmd");
	$count_cmd=1;

	for($number_cmd=0; $number_cmd < $count_cmd; $number_cmd++){
	  if($cmd=="auth"){
	  	$login = check_var_web($_POST["login"]);
	  	$password = check_var_web($_POST["password"]);
	  	$res = mysql_query("select * from users where login='$login' and password='$password'");
	  	if(mysql_num_rows($res)){
	  		$u = mysql_fetch_array($res);
	  		$_SESSION["u_id"] = $u["id"];
	  		$t = get_value_option("template_auth_ok");
	  		$t = replace_keywords($t);
		  	$parameter_page["title"] = "Успешная авторизация";
		  	$parameter_page["main_text"] = $t;
	  	}else {
	  		$t = get_value_option("template_auth_text");
	  		$t = replace_keywords($t);
	  		$t = str_replace("%error%", "<div class=\"error\">Неверные авторизационные данные.<br>Попробуйте ввести логин и пароль еще раз, либо воспользуйтесь механизмом восстановлния пароля.</div>", $t);
	  		$t = str_replace("%auth_form%", auth_form(), $t);
		  	$parameter_page["title"] = "Ошибка авторизации";
		  	$parameter_page["main_text"] = $t;
	  	}
	  }elseif ($cmd == "remem"){
	  	$parameter_page["title"] = $parameter_page["name"] = "Восстановление пароля";
	  	$email = check_var_web($_POST["p_email"]);
	  	if($email){
	  		$res = mysql_query("select * from users where email = '$email'");
	  		if(mysql_num_rows($res)){
	  			$u = mysql_fetch_array($res);

	  			$message = get_value_option("template_mail_remem_ok");
		  		$message = replace_keywords($message, $u["id"]);
  				my_mail($u["email"], "Восстановление пароля", $message, get_value_option("feedback_email"));
	  			
	  			
		  		$t = get_value_option("template_remem_ok");
		  		$t = replace_keywords($t);
			  	$parameter_page["title"] = "Пароль отправлен на указанный e-mail";
			  	$parameter_page["main_text"] = $t;
	  		}else{
		  		$t = get_value_option("template_remem_text");
		  		$t = str_replace("%form%", remem_form(), $t);
		  		$t = str_replace("%error%", "<div class=\"error\">Указанный e-mail отсутсвует в списке пользователей</div>", $t);
			  	$parameter_page["main_text"] = $t;
	  		}
	  	}else{
	  		$t = get_value_option("template_remem_text");
	  		$t = str_replace("%form%", remem_form(), $t);
	  		$t = str_replace("%error%", "", $t);
		  	$parameter_page["main_text"] = $t;
	  	}
	  	
	  }else{
  		$t = get_value_option("template_auth_text");
  		$t = str_replace("%auth_form%", auth_form(), $t);
  		$t = str_replace("%error%", "", $t);
	  	$parameter_page["main_text"] = $t;
		}
	}
?>