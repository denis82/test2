<?php 
  
  function show_form($p_name, $p_phone, $p_email, $p_subject, $p_message){
  	return "<br><br>
  	  <a name=\"bottom\"></a>
			<table  id=\"feedback\">
			        <form method=\"post\" action=\"#bottom\">
			            <input type=\"hidden\" name=\"p_cmd\" value=\"send\" />
			            <tr>
			                <td align=right >���*</td>
			                <td ><input class=\"input-short\" name=\"p_name\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >E-mail*</td>
			                <td ><input class=\"input-short\" name=\"p_email\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_email))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >�������*</td>
			                <td ><input class=\"input-short\" name=\"p_phone\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_phone))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >����</td>
			                <td ><input class=\"input\" name=\"p_subject\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_subject))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >����� ���������</td>
			                <td ><textarea class=\"input\" name=\"p_message\" cols=\"50\" rows=\"7\">".htmlspecialchars(stripslashes($p_message))."</textarea></td>
			            </tr>
			            <tr>
			                <td></td><td  ><input class=\"button\" type=\"submit\" value=\"���������\" /></td>
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
  		  $message = "<h3>������ � �����</h3>
  		               <table>
  		               <tr><td align=\"right\">���: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
  		               <tr><td align=\"right\">E-mail: </td><td><b>".htmlspecialchars(stripslashes($p_email))."</b></td></tr>
  		               <tr><td align=\"right\">�������: </td><td><b>".htmlspecialchars(stripslashes($p_phone))."</b></td></tr>
  		               <tr><td align=\"right\">����: </td><td><b>".htmlspecialchars(stripslashes($p_subject))."</b></td></tr>
  		               <tr><td align=\"right\">���������: </td><td><b>".htmlspecialchars(stripslashes($p_message))."</b></td></tr>
  		               </table> 
  		             ";
  		  $feedback_email = get_value_option("feedback_email");
  	    //$status = my_mail($feedback_email, add_koi("������ � �����"), $message, add_koi("���� $common_options[site_name]")." <admin@$common_options[site_name]>");
  	    $status = @my_mail($feedback_email, "������ � �����", $message, "site@inklimat.ru");
  		  if($status){
  		  	$parameter_page["title"] = "���� ��������� ����������";
  		  	$parameter_page["main_text"] .= show_form("", "", "", "", "");
  		  	$parameter_page["main_text"] .= "<p>���� ��������� ����������.</p>";
  		  }else{
  		  	$parameter_page["title"] = "������";
  		  	$parameter_page["main_text"] .= show_form($p_name, $p_phone, $p_email, $p_subject, $p_message);
  		  	$parameter_page["main_text"] .= "<p>� ������ ������ �������� ��������� ����������.<br>���������� ����� ��������� �����.<br>�������� �� ����������.</p>";
  		  }
      }else{
  	  	$parameter_page["title"] = "������";
  	  	$parameter_page["main_text"] .= show_form($p_name, $p_phone, $p_email, $p_subject, $p_message);
  	  	$parameter_page["main_text"] .= "<p>������������ e-mail!</p>";
      }
		}else{
	  	$parameter_page["title"] = "������";
	  	$parameter_page["main_text"] .= show_form($p_name, $p_phone, $p_email, $p_subject, $p_message);
	  	$parameter_page["main_text"] .= "<p>����������, ��������� ���������� * ����</p>";
		}
	}else{
  	$parameter_page["main_text"] .= show_form("", "", "", "", "");
	}

	
?>