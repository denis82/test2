<?php
  

  function order_form($p_status="", $values = array()) {
  	global $common_options;
  	global $dost;
  	$reg_e_id  = get_functional_element("reg");
  	if($_SESSION["u_id"]){
  		$user = get_user($_SESSION["u_id"]);
  		$values["name_company"] = $user["name_company"];
  		$values["contact"] = $user["fio"]." ".$user["email"];
  	}
  	$output .= "
			<script language=\"JavaScript\">
			  function reload_form(item, cmd){
			  	if(cmd==\"commit\"){
			  		document.f.p_cmd.value = \"commit\";
			  	}
			  	document.f.submit();
			  }
			</script>
  	";
  	$output .= $p_status;
		$output .= "<form method=\"post\" name=\"f\">";
		$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"calc\">";
		$output .= "<table class='reg-form'>";
		$output .= "
      <tr><td align=\"right\" class=\"name\">Наименование<br>компании* </td><td><input title=\"\" type=\"text\" name=\"name_company\" size=\"50\" value=\"".check_var_web($values["name_company"])."\" class=\"input-long\"></td></tr>
      <tr><td align=\"right\" class=\"name\">Сведения о контакте<br>(ФИО; тел или e-mail)* </td><td><input title=\"\" type=\"text\" name=\"contact\" size=\"50\" value=\"".check_var_web($values["contact"])."\" class=\"input-long\"></td></tr>";
		$res = mysql_query("select e.id, e.e_id, e.name from elements e, har_elements he where e.id = he.e_id and e.te_id and he.pe_code = 'is_access' and he.value='1'  and e.te_id=202 order by e.e_id, e.name");
		while ($row = mysql_fetch_array($res)) {
			if(!get_value_har($row["id"], "only_catalog")){
				$g[$row["e_id"]] = 1;
				$goods[$row["e_id"]][] = $row;
			}
		}
		
		$list_g = array();
		foreach ($g as $key => $value) {
			$list_g[] = $key;
		}
		
		$groups[] = array("id"=>-1, "name"=>"Не выбрано");
		if(count($list_g)){
			$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and e.te_id and he.pe_code = 'is_access' and he.value='1'  and e.te_id=201 and e.id in (".implode(",",$list_g).") order by binary(e.name)");
			while ($row = mysql_fetch_array($res)) {
				$groups[] = $row;
			}
		}
		$values["group_id"][] = -1;
		$i = 0;
		$j=0;
		foreach ($values["group_id"] as $group_id) {
			$i++;
			if($group_id==-1){
				$j++;
			}
			if(($j>1)&&($group_id==-1)){
				
			}else{
				$output .= "
		      <tr><td align=\"right\" class=\"name\" >";
				if($i==1){
					$output .= "Выберите группу товара*";
				}else{
					$output .= "Выберите еще группу товара";
				}
				$output .= "
				  </td><td><select name=\"group_id[]\" onChange=\"JavaScript:reload_form(this, '');\">";
				foreach ($groups as $group) {
					if($group_id == $group["id"]){
						$checked = "selected";
					}else {
						$checked = "";
					}
					$output .= "<option value=\"$group[id]\" $checked >$group[name]</option>";
				}
				$output .= "
				  </select></td></tr>";
				if($group_id>0){
					//показать содеражание группы
					$output .= "
			      <tr>
			        <td></td>
			        <td><table border=0>";
					$sum = 0;
					$unit = get_unit(get_value_har($group_id, "unit"));
					foreach ($goods[$group_id] as $good) {
						$output .= "<tr><td align=left valign=top>$good[name]</td><td align=center valign=top>$unit[header_volume]</td><td align=center valign=top>$unit[header_count]</td></tr>";
						$volume = get_value_har($good["id"], "volume");
						$volums = explode(";", $volume);
						foreach ($volums as $v) {
							$var_name = "good_$good[id]_".translate($v);
							$sum += $values[$var_name];
							$output .= "<tr><td></td><td align=center>$v</td><td align=center>".create_input("number", "$var_name", $values[$var_name])."</td></tr>";
						}
					}
					$output .= "
						<tr><td colspan=2><a href=\"#\" onClick=\"JavaScript:reload_form();\">Подсчитать итого</a></td><td align=center>".create_input("number-disabled", "", $sum)."</td></tr>
			      </table></td></tr>";
				}
			}
		}
			
		$output .= "
      <tr><td align=\"right\" class=\"name\" >Желаемые условия поставки* </td><td valign=bottom>";
		foreach ($common_options["dost"] as $key => $value) {
			if($key == $values["dost"]){
				$checked = "checked";
				if($values["dost"]==2){
					$dis = " disabled ";
				}
			}else {
				$checked = "";
			}
			if($key==1){
				$output .= "<input type=\"radio\" name=\"dost\"  value=\"$key\" $checked  onClick=\"JavaScript:this.form.punkt.disabled=false;this.form.srok.disabled=false;\">$value&nbsp;&nbsp;&nbsp;";
			}else{
				$output .= "<input type=\"radio\" name=\"dost\"  value=\"$key\" $checked  onClick=\"JavaScript:this.form.punkt.disabled=true;this.form.srok.disabled=true;\">$value&nbsp;&nbsp;&nbsp;";
			}
		}
		$output .= "</td></tr>
    ";
		$output .= "
      <tr><td align=\"right\" class=\"name\">Пункт назначения </td><td><input title=\"\" $dis type=\"text\" name=\"punkt\" size=\"50\" value=\"".check_var_web($values["punkt"])."\" class=\"input-long\"></td></tr>
      <tr><td align=\"right\" class=\"name\">Желаемые сроки<br>поставки </td><td><input title=\"\" $dis type=\"text\" name=\"srok\" size=\"50\" value=\"".check_var_web($values["srok"])."\" class=\"input-long\"></td></tr>
     ";
		$output .= "<tr><td></td><td><p>Поля, помеченные *, обязательны к заполнению!</p></td></tr>";
		$output .= "<tr><td></td><td><input type=\"submit\" value=\"Отправить\" class=\"button\"  onClick=\"JavaScript:reload_form(this, 'commit');\"></td></tr>";
		$output .= "</table></form>";
		return $output;
  }
  
  function order_message($values = array()) {
  	global $common_options;
		$output .= "<table>";
		$output .= "
      <tr><td align=\"right\">Наименование<br>компании </td><td>".check_var_web($values["name_company"])."</td></tr>
      <tr><td align=\"right\">Сведения о контакте </td><td>".check_var_web($values["contact"])."</td></tr>";
		$res = mysql_query("select e.id, e.e_id, e.name from elements e, har_elements he where e.id = he.e_id and e.te_id and he.pe_code = 'is_access' and he.value='1'  and e.te_id=202 order by e.e_id, e.name");
		while ($row = mysql_fetch_array($res)) {
			if(!get_value_har($row["id"], "only_catalog")){
				$g[$row["e_id"]] = 1;
				$goods[$row["e_id"]][] = $row;
			}
		}
		
		$list_g = array();
		foreach ($g as $key => $value) {
			$list_g[] = $key;
		}
		
		$groups[] = array("id"=>-1, "name"=>"Не выбрано");
		if(count($list_g)){
			$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and e.te_id and he.pe_code = 'is_access' and he.value='1'  and e.te_id=201 and e.id in (".implode(",",$list_g).") order by binary(e.name)");
			while ($row = mysql_fetch_array($res)) {
				$groups[] = $row;
			}
		}
		$values["group_id"][] = -1;
		$i = 0;
		foreach ($values["group_id"] as $group_id) {
			if($group_id>0){
				$i++;
				$group = get_element($group_id);
				$output .= "
		      <tr>
		        <td></td>
		        <td><table border=0>";
				$sum = 0;
				$unit = get_unit(get_value_har($group_id, "unit"));
				foreach ($goods[$group_id] as $good) {
					$volume = get_value_har($good["id"], "volume");
					$volums = explode(";", $volume);
					$tmp = "";
					foreach ($volums as $v) {
						$var_name = "good_$good[id]_".translate($v);
						if($values[$var_name]){
							$sum += $values[$var_name];
							$tmp .= "<tr><td></td><td align=center>$v</td><td align=center>".$values[$var_name]."</td></tr>";
						}
					}
					if($tmp){
						$output .= "<tr><td align=left valign=top>$good[name]</td><td align=center valign=top>$unit[header_volume]</td><td align=center valign=top>$unit[header_count]</td></tr>".$tmp;
					}
				}
				$output .= "</table></td></tr>";
			}
		}
		
		$output .= "
      <tr><td >Желаемые условия поставки </td><td valign=bottom>".$common_options["dost"][trim($values["dost"])]."</td></tr>";
		$output .= "
      <tr><td align=\"right\" class=\"name\">Пункт назначения </td><td>".check_var_web($values["punkt"])."</td></tr>
      <tr><td align=\"right\" class=\"name\">Желаемые сроки<br>поставки </td><td>".check_var_web($values["srok"])."</td></tr>
     ";
		$output .= "</table>";
		return $output;
  }

  

  $cmd = get_var_web("p_cmd");
	$reg_e_id = get_functional_element("reg");
	if($reg_e_id["id"]!=$_SESSION["last_e_id"]){
		$_SESSION["prev_auth_e_id"] = $_SESSION["last_e_id"];
	}

	$count_cmd=1;
	$user = array();
	for($number_cmd=0; $number_cmd < $count_cmd; $number_cmd++){
	  if($cmd=="calc"){
	  	//пересчитать форму
	  	$parameter_page["main_text"] .= order_form('', $_POST);
	  }elseif ($cmd == "commit") {
	  	//оформить заказ
      $name_company = htmlspecialchars(trim($_POST["name_company"]));
      $contact = htmlspecialchars(trim($_POST["contact"]));
      $dost = $common_options["dost"][trim($_POST["dost"])];
      $punkt = htmlspecialchars(trim($_POST["punkt"]));
      $srok = htmlspecialchars(trim($_POST["srok"]));
      if($name_company&&$contact&&((trim($_POST["dost"])=="1"&&$punkt&&$srok)||(trim($_POST["dost"])=="2"))){
		  	$message = order_message($_POST);
		  	$feedback_email = get_value_option("feedback_email");
		  	$out_email = get_value_option("out_email");
				my_mail($feedback_email, "Новый заказ", $message, $out_email);
				if($_SESSION["u_id"]){
					mysql_query("insert into orders (date, u_id, text) 
					  values (now(), '$_SESSION[u_id]', '$message')");
				}
		  	$parameter_page["main_text"] .= get_value_option("order_ok");
      }else{
		  	$parameter_page["main_text"] .= order_form("<p class=\"error\"><b>Поля, помеченные *, обязательны к заполнению!</b></p>", $_POST);
      }
	  }else{
	  	$parameter_page["main_text"] .= order_form();
	  }
	}
?>