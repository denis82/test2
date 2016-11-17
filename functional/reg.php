<?php

  function remem_form() {
  	global $common_options;
		$output .= "<form method=\"post\">";
		$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"remem\">";
		$output .= "<table class='basket-table'>";
		$output .= "
      <tr>
        <td align=\"left\" class=\"name\">E-mail:</td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
        <td align=\"left\"><input type=\"text\" name=\"p_email\" size=\"30\" class=\"input\"></td>
      </tr>
      <tr>
        <td align=\"right\" colspan=3><input type=\"submit\"  size=\"50\" class=\"button\" value=\"Восстановить\"></td>
      </tr>

    ";
		$output .= "</table></form>";
		
		return $output;
  }

  function auth_form() {
  	global $common_options;
		$output .= "<form method=\"post\">";
		$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"auth\">";
		$output .= "<table class='basket-table' border=0>";
		$output .= "
      <tr>
        <td align=\"left\" class=\"name\">Логин:</td>
        <td align=\"left\" class=\"name\">Пароль:</td>
        <td><img src=\"/images/1x1.gif\" width=\"15\" height=\"1\"></td>
      </tr>
      <tr>
        <td align=\"left\"><input type=\"text\" name=\"login\" size=\"20\" class=\"input\"></td>
        <td align=\"left\"><input type=\"password\" name=\"password\" size=\"20\" class=\"input\"></td>
        <td><input type=\"submit\"  class=\"button\" value=\"Войти\"></td>
      </tr>
      <tr>
        <td align=\"left\" ></td>
        <td align=\"left\" colspan=2><a href=\"?p_cmd=remem\">Вы забыли логин или пароль</a></td>
      </tr>

    ";
		$output .= "</table></form>";
		
		return $output;
  }


  function edit_user($p_status="", $user = array()) {
  	global $common_options;
  	$reg_e_id  = get_functional_element("reg");
  	if($_SESSION["u_id"]){
  		$user = get_user($_SESSION["u_id"]);
  		$name_button = "Изменить данные";
  	}else{
  		$name_button = "Зарегистрироваться";
  	}
  	//print_r($reg_e_id);
		$output .= "<form action=\"".get_url_element($reg_e_id)."\" method=\"post\">";
		$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"save\">";
		$output .= "<table class='basket-table'>";
		if(!$_SESSION["u_id"]){
			$output .= "
			  <tr><td align=\"right\" class=\"name\">Логин* </td><td><input title=\"\" type=\"text\" name=\"login\" class=\"input\" value=\"".check_var_web($user["login"])."\" size=\"30\"></td></tr>
			  <tr><td align=\"right\" class=\"name\">Пароль* </td><td><input title=\"\" type=\"password\" name=\"password\" class=\"input\" size=\"30\"></td></tr>
			  <tr><td align=\"right\" class=\"name\">Подтвердите пароль* </td><td><input title=\"\" type=\"password\" name=\"retry_password\" class=\"input\" size=\"30\"></td></tr>
			  <tr height=20><td colspan=\"2\"></td></tr>
			";
		}
		$output .= "
      <tr><td align=\"right\" class=\"name\">Фамилия </td><td><input title=\"\" type=\"text\" name=\"lastname\" size=\"50\" value=\"".check_var_web($user["lastname"])."\" class=\"input-long\"></td></tr>
      <tr><td align=\"right\" class=\"name\">Имя </td><td><input title=\"\" type=\"text\" name=\"firstname\" size=\"50\" value=\"".check_var_web($user["firstname"])."\" class=\"input-long\"></td></tr>
      <tr><td align=\"right\" class=\"name\">Отчество </td><td><input title=\"\" type=\"text\" name=\"middlename\" size=\"50\" value=\"".check_var_web($user["middlename"])."\" class=\"input-long\"></td></tr>
      <tr><td align=\"right\" class=\"name\">Адрес доставки </td><td><input title=\"\" type=\"text\" name=\"address\" size=\"50\" value=\"".check_var_web($user["address"])."\" class=\"input-long\"></td></tr>
      <tr><td align=\"right\" class=\"name\">E-mail* </td><td><input title=\"\" type=\"text\" name=\"email\" value=\"".check_var_web($user["email"])."\" class=\"input\"></td></tr>
      <tr><td align=\"right\" class=\"name\">Телефон </td><td><input title=\"\" type=\"text\" name=\"phone\"  value=\"".check_var_web($user["phone"])."\" class=\"input\"></td></tr>";
		$output .= "<tr><td></td><td><input type=\"submit\" value=\"$name_button\" class=\"button\"></td></tr>";
		$output .= "</table></form>";
		if($p_status=="*"){
			$output .= "<p class=\"error\">Поля, помеченные *, обязательны к заполнению!</p>";
		}elseif ($p_status=="ok") {
			$output .= "<p>Анкета успешно обновлена.</p>";
		}elseif ($p_status=="login") {
			$output .= "<p class=\"error\">Такой логин уже имеется в системе!</p>";
		}elseif ($p_status=="length") {
			$output .= "<p class=\"error\">Длина пароля должна быть не менее 5 символов!</p>";
		}elseif ($p_status=="retry") {
			$output .= "<p class=\"error\">Ошибка пароля. Пожалуйста, введите пароль еще раз.</p>";
		}
		
		return $output;
  }

  $cmd = get_var_web("p_cmd");
	$reg_e_id = get_functional_element("reg");

	$count_cmd=1;
	$user = array();
	for($number_cmd=0; $number_cmd < $count_cmd; $number_cmd++){
	  if($cmd=="auth"){
	  	$login = check_var_web($_POST["login"]);
	  	$password = check_var_web($_POST["password"]);
	  	$res = mysql_query("select * from users where login='$login' and password='$password'");
	  	if(mysql_num_rows($res)){
	  		$u = mysql_fetch_array($res);
	  		$_SESSION["u_id"] = $u["id"];
  			if(mysql_num_rows(mysql_query("select * from string_baskets sb where sb.b_id = '$_SESSION[b_id]'"))){
  				delete_basket_user();
  				if($_SESSION["b_id"]){
	  				mysql_query("update baskets set u_id = '$_SESSION[u_id]' where id = '$_SESSION[b_id]'");
  				}
  			}else {
  				$b = mysql_fetch_array(mysql_query("select * from baskets where u_id = '$_SESSION[u_id]' order by date1 desc limit 1"));
  				$_SESSION["b_id"] = $b["id"];
  			}
	  		$t = get_value_option("template_auth_ok");
	  		$t = replace_keywords($t);
		  	$parameter_page["title"] = $parameter_page["name"] = "Приветствуем на нашем сайте";
		  	$parameter_page["main_text"] = $t;
	  	}else {
	  		$t = get_value_option("template_auth_text");
	  		$t = replace_keywords($t);
	  		$t = str_replace("%error%", "<div class=\"error\">Неверные авторизационные данные.<br>Попробуйте ввести логин и пароль еще раз, либо воспользуйтесь механизмом восстановлния пароля.</div>", $t);
	  		$t = str_replace("%auth_form%", auth_form(), $t);
		  	$parameter_page["title"] = $parameter_page["name"] = "Ошибка входа";
		  	$parameter_page["main_text"] = $t;
	  	}
	  	
	  }elseif ($cmd=="modify"){
	  	$parameter_page["name"] = "Изменение анкеты";
	  	$parameter_page["title"] = "Изменение анкеты";
	  	$parameter_page["main_text"] = edit_user($status);
	
	  }elseif ($cmd=="save"){
	  	$lastname = str_replace("'", "\'", stripslashes($_POST["lastname"]));
	  	$firstname = str_replace("'", "\'", stripslashes($_POST["firstname"]));
	  	$middlename = str_replace("'", "\'", stripslashes($_POST["middlename"]));
	  	$address = str_replace("'", "\'", stripslashes($_POST["address"]));
	  	$phone = str_replace("'", "\'", stripslashes($_POST["phone"]));
	  	$email = str_replace("'", "\'", stripslashes($_POST["email"]));
	  	$login = str_replace("'", "\'", stripslashes($_POST["login"]));
	  	$password = str_replace("'", "\'", stripslashes($_POST["password"]));
	  	$retry_password = str_replace("'", "\'", stripslashes($_POST["retry_password"]));
	  	if($_SESSION["u_id"]){
    		if($email){
    			mysql_query("
    			  update users set 
    			    lastname = '$lastname',
    			    firstname = '$firstname',
    			    middlename = '$middlename',
    			    address = '$address',
    			    email = '$email',
    			    phone = '$phone'
    			  where id = '$_SESSION[u_id]'
    			");	  		
  	  			$count_cmd = 2;
  	  			$status = "ok";
  	  			$cmd = "";
	  		}else {
	  			$count_cmd = 2;
	  			$status = "*";
	  			$cmd = "post";
	  		}
	  	}else {
	  		if($login&&$password&&$email){
	  			if(strlen($password)>4){
	  				if($password==$retry_password){
			  			mysql_query("
			  			  insert into users (date, login, password, lastname, firstname, middlename, address, email, phone)
			  			    values (now(), '$login', '$password', '$lastname', '$firstname', '$middlename', '$address', '$email', '$phone')
			  			");	  			
			  			if(mysql_errno()){
				  			$count_cmd = 2;
				  			$status = "login";
				  			$cmd = "post";
			  			}else{
			  			  session_start();
			  				$_SESSION["u_id"] = mysql_insert_id();
			  				if($_SESSION["b_id"]){
				  				mysql_query("update baskets set u_id = '$_SESSION[u_id]' where id = '$_SESSION[b_id]'");
			  				}
			  				$message = replace_keywords(get_value_option("template_reg_ok"), $_SESSION["u_id"]);
//			  				$message = str_replace("%login%", $login, $message);
//			  				$message = str_replace("%password%", $password, $message);
//			  				$message = str_replace("%name%", $lastname." ".$firstname." ".$middlename, $message);
			  				if($email){
				  				my_mail($email, "Успешная регистрация на сайте edison-gift.ru", $message, get_value_option("feedback_email"));
			  				}
						  	$parameter_page["main_text"] = replace_keywords(get_value_option("reg_ok"), $_SESSION["u_id"]);
			  			}
		  			}else {
			  			$count_cmd = 2;
			  			$status = "retry";
			  			$cmd = "post";
			  		}
	  			}else {
		  			$count_cmd = 2;
		  			$status = "length";
		  			$cmd = "post";
		  		}
	  		}else {
	  			$count_cmd = 2;
	  			$status = "*";
	  			$cmd = "post";
	  		}
	  	}

	  }elseif ($cmd=="post"){
	  	if($_SESSION["u_id"]){
		  	$parameter_page["name"] = "Изменение анкеты";
		  	$parameter_page["title"] = "Изменение анкеты";
		  	$user = $_POST;
		  	$parameter_page["main_text"] = get_value_har($parameter_page["id"], "main_text");
	  	}else{
		  	$parameter_page["name"] = "Регистрация";
		  	$parameter_page["title"] = "Регистрация";
		  	$user = $_POST;
		  	$parameter_page["main_text"] = get_value_har($parameter_page["id"], "main_text");
	  	}
	  	$parameter_page["main_text"] .= edit_user($status, $user);

	  }elseif ($cmd=="logout"){
	  	$_SESSION["u_id"] = null;
	  	$_SESSION["b_id"] = null;
	  	header("Location: /");
	  	die();

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
	  	if($_SESSION["u_id"]){
		  	$parameter_page["name"] = "Изменение анкеты";
		  	$parameter_page["title"] = "Изменение анкеты";
		  	$parameter_page["main_text"] = "";
		  	$user = get_user($_SESSION["u_id"]);
	  	}else{
		  	$parameter_page["name"] = "Регистрация";
		  	$parameter_page["title"] = "Регистрация";
		  	$parameter_page["main_text"] = get_value_har($parameter_page["id"], "main_text");
		  	$user = array();
	  	}
	  	$parameter_page["main_text"] .= edit_user($status, $user);
	
	  }
	}
	$parameter_page["template"] = "func";
?>