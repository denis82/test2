<?php 
  
  function show_form($p_name, $p_phone){
  	return "<br><br>
  	  <a name=\"bottom\"></a>
			<table  id=\"feedback\"  class='order-table'>
			        <form method=\"post\" action=\"#bottom\" id='form-feedback2'>
			            <input type=\"hidden\" name=\"p_cmd\" value=\"send\" />
			            <tr>
			                <td align=right >���� ���*</td>
			                <td ><input class=\"input-short\" name=\"p_name\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >������� ��� �����*</td>
			                <td ><input class=\"input-short\" name=\"p_phone\" size=\"50\" value=\"".htmlspecialchars(stripslashes($p_phone))."\"/></td>
			            </tr>
			        </form>
			</table>  
			<div class=\"button button-order\"  style=\"margin-left:0px; margin-top:10px;\"><a href='#' onclick='$(\"#form-feedback2\").submit(); return false;'>���������</a></div>
			<p><br><br><i>������ �������� ��������� � 10.00 �� 20.00 (���).</i></p>
			";
	}

	$parameter_page["main_text"] = get_value_har($p_content_element["id"], "main_text");
	$parameter_page["name"] = "$p_content_element[name]";

	$p_cmd = get_var_web("p_cmd");
  if($p_cmd){
    $p_name = trim(get_var_web("p_name"));
    $p_phone = trim(get_var_web("p_phone"));
    if($p_name&&$p_phone){
  		  $message = "<h3>�������� ������</h3>
  		               <table>
  		               <tr><td align=\"right\">���: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
  		               <tr><td align=\"right\">�������: </td><td><b>".htmlspecialchars(stripslashes($p_phone))."</b></td></tr>
  		               </table> 
  		             ";
  		  $feedback_email = get_value_option("feedback_email");
  	    $status = @my_mail_html($feedback_email, "�������� ������ � �����", $message, $common_options["from_email"]);
  		  if($status){
  		  	$parameter_page["title"] = "��� ������ ���������";
  		  	//$parameter_page["main_text"] .= show_form("", "");
  		  	$parameter_page["main_text"] .= "<p><br><br>��� ������ ���������.</p>";
  		  }else{
  		  	$parameter_page["title"] = "������";
  		  	$parameter_page["main_text"] .= show_form($p_name, $p_phone);
  		  	$parameter_page["main_text"] .= "<p>� ������ ������ �������� ��������� ����������.<br>���������� ����� ��������� �����.<br>�������� �� ����������.</p>";
  		  }
		}else{
	  	$parameter_page["title"] = "������";
	  	$parameter_page["main_text"] .= show_form($p_name, $p_phone);
	  	$parameter_page["main_text"] .= "<p class=\"error\">����, ���������� *, ����������� � ����������!</p>";
		}
	}else{
  	$parameter_page["main_text"] .= show_form("", "");
	}

	
?>