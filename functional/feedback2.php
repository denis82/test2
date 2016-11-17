<?php 
  
  function show_form($p_name, $p_phone){
  	return "<br><br>
  	  <a name=\"bottom\"></a>
			<table  id=\"feedback\"  class='order-table'>
			        <form method=\"post\" action=\"#bottom\" id='form-feedback2'>
			            <input type=\"hidden\" name=\"p_cmd\" value=\"send\" />
			            <tr>
			                <td align=right >Ваше имя*</td>
			                <td ><input class=\"input-short\" name=\"p_name\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >Телефон для связи*</td>
			                <td ><input class=\"input-short\" name=\"p_phone\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_phone))."\"/></td>
			            </tr>
			        </form>
			</table>  
			<div class=\"button button-order\"  style=\"margin-left:0px; margin-top:10px;\"><a href='#' onclick='$(\"#form-feedback2\").submit(); return false;'>Отправить</a></div>
			<p><br><br><i>Услуга доступна ежедневно с 10.00 до 20.00 (мск).</i></p>
			";
	}

	$parameter_page["main_text"] = get_value_har($p_content_element["id"], "main_text");
	$parameter_page["name"] = "$p_content_element[name]";

	$p_cmd = get_var_web("p_cmd");
  if($p_cmd){
    $p_name = trim(get_var_web("p_name"));
    $p_phone = trim(get_var_web("p_phone"));
    if($p_name&&$p_phone){
  		  $message = "<h3>Обратный звонок</h3>
  		               <table>
  		               <tr><td align=\"right\">Имя: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
  		               <tr><td align=\"right\">Телефон: </td><td><b>".htmlspecialchars(stripslashes($p_phone))."</b></td></tr>
  		               </table> 
  		             ";
  		  $feedback_email = get_value_option("feedback_email");
  	    $status = @my_mail_html($feedback_email, "Обратный звонок с сайта", $message, $common_options["from_email"]);
  		  if($status){
  		  	$parameter_page["title"] = "Ваш запрос отправлен";
  		  	//$parameter_page["main_text"] .= show_form("", "");
  		  	$parameter_page["main_text"] .= "<p><br><br>Ваш запрос отправлен.</p>";
  		  }else{
  		  	$parameter_page["title"] = "Ошибка";
  		  	$parameter_page["main_text"] .= show_form($p_name, $p_phone);
  		  	$parameter_page["main_text"] .= "<p>В данный момент операцию завершить невозможно.<br>Попробуйте через некоторое время.<br>Извините за неудобства.</p>";
  		  }
		}else{
	  	$parameter_page["title"] = "Ошибка";
	  	$parameter_page["main_text"] .= show_form($p_name, $p_phone);
	  	$parameter_page["main_text"] .= "<p class=\"error\">Поля, помеченные *, обязательны к заполнению!</p>";
		}
	}else{
  	$parameter_page["main_text"] .= show_form("", "");
	}

	
?>