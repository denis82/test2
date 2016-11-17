<?php 
  
  function show_form($p_name, $p_phone, $p_email, $p_subject, $p_message){
  	return "<br><br>
  	  <a name=\"bottom\"></a>
			<table  id=\"feedback\">
			        <form method=\"post\" action=\"#bottom\">
			            <input type=\"hidden\" name=\"p_cmd\" value=\"send\" />
			            <tr>
			                <td align=right >Имя*</td>
			                <td ><input class=\"input-short\" name=\"p_name\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >E-mail*</td>
			                <td ><input class=\"input-short\" name=\"p_email\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_email))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >Телефон*</td>
			                <td ><input class=\"input-short\" name=\"p_phone\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_phone))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >Тема</td>
			                <td ><input class=\"input\" name=\"p_subject\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_subject))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >Текст сообщения</td>
			                <td ><textarea class=\"input\" name=\"p_message\" cols=\"50\" rows=\"7\">".htmlspecialchars(stripslashes($p_message))."</textarea></td>
			            </tr>
			            <tr>
			                <td></td><td  ><input class=\"button\" type=\"submit\" value=\"Отправить\" /></td>
			            </tr>
			        </form>
			</table>  ";
	}

	$parameter_page["main_text"] = get_value_har($p_content_element["id"], "main_text");
	$parameter_page["name"] = "$p_content_element[name]";

	$p_cmd = get_var_web("p_cmd");
  if($p_cmd){
    $p_name = trim(get_var_web("p_name"));
    $p_phone = trim(get_var_web("p_phone"));
    $p_email = trim(get_var_web("p_email"));
    $p_subject= trim(get_var_web("p_subject"));
    $p_message = trim(get_var_web("p_message"));
    if($p_name&&$p_email&&$p_phone){
      if(check_mail($p_email)){
  		  $message = "<h3>Письмо с сайта</h3>
  		               <table>
  		               <tr><td align=\"right\">Имя: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
  		               <tr><td align=\"right\">E-mail: </td><td><b>".htmlspecialchars(stripslashes($p_email))."</b></td></tr>
  		               <tr><td align=\"right\">Телефон: </td><td><b>".htmlspecialchars(stripslashes($p_phone))."</b></td></tr>
  		               <tr><td align=\"right\">Тема: </td><td><b>".htmlspecialchars(stripslashes($p_subject))."</b></td></tr>
  		               <tr><td align=\"right\">Сообщение: </td><td><b>".htmlspecialchars(stripslashes($p_message))."</b></td></tr>
  		               </table> 
  		             ";
  		  $feedback_email = get_value_option("feedback_email");
  	    //$status = my_mail($feedback_email, add_koi("Письмо с сайта"), $message, add_koi("Сайт $common_options[site_name]")." <admin@$common_options[site_name]>");
  	    $status = @my_mail($feedback_email, "Письмо с сайта", $message, "site@inklimat.ru");
  		  if($status){
  		  	$parameter_page["title"] = "Ваше сообщение отправлено";
  		  	$parameter_page["main_text"] .= show_form("", "", "", "", "");
  		  	$parameter_page["main_text"] .= "<p>Ваше сообщение отправлено.</p>";
  		  }else{
  		  	$parameter_page["title"] = "Ошибка";
  		  	$parameter_page["main_text"] .= show_form($p_name, $p_phone, $p_email, $p_subject, $p_message);
  		  	$parameter_page["main_text"] .= "<p>В данный момент операцию завершить невозможно.<br>Попробуйте через некоторое время.<br>Извините за неудобства.</p>";
  		  }
      }else{
  	  	$parameter_page["title"] = "Ошибка";
  	  	$parameter_page["main_text"] .= show_form($p_name, $p_phone, $p_email, $p_subject, $p_message);
  	  	$parameter_page["main_text"] .= "<p>Неправильный e-mail!</p>";
      }
		}else{
	  	$parameter_page["title"] = "Ошибка";
	  	$parameter_page["main_text"] .= show_form($p_name, $p_phone, $p_email, $p_subject, $p_message);
	  	$parameter_page["main_text"] .= "<p>Пожалуйста, заполните помеченные * поля</p>";
		}
	}else{
  	$parameter_page["main_text"] .= show_form("", "", "", "", "");
	}

	
?>