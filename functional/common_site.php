<?php

  function mm($mm){
    //добавляет ноль для номера месяца
    $m = (int)$mm;
    if($m<10){
      $m = "0".$m;
    }
    return $m;
  }
  
  
  function show_array($a, $p, $mode = "select", $name = "", $add = ""){
    //print_r($a);
  	$return = "";
  	foreach ($a as $k=>$v){
  	  if($mode=="select"){
      	if(($k == $p)){
      		$return .= "<option value=\"$k\" selected>$v</option>";
      	}else{
      		$return .= "<option value=\"$k\">$v</option>";
      	}
  	  }elseif ($mode=="radio"){
      	if(($k == $p)){
      		$return .= "<label><input type=\"radio\" name=\"$name\" value=\"$k\" checked> $v </label><br>";
      	}else{
      		$return .= "<label><input type=\"radio\" name=\"$name\" value=\"$k\"> $v </label><br>";
      	}
  	  }
  	}
  	if($mode == "select"){
  		$return = "<select name=\"$name\" $add>$return</select>";
  	}
    return $return;  
  }
  
  function show_list($query, $p_url, $party = 1, $count_on_page = 10, &$find, $group=0){
  	global $common_options;
  	$res = mysql_query($query);
  	$i = 0;
  	$output = "";
  	while($item = mysql_fetch_array($res)){
  		if(get_value_har($item["id"], "is_access")){
				$i++;
				if( (($party-1)*$count_on_page < $i) && ($i <= $party*$count_on_page) ){
				  if($group){
				    $output .= get_short_desc_group($item["id"]);
				  }else{
				    $output .= get_short_desc($item["id"]);
				  }
				}
  		}
  	}
  	if($output){
  	  $find = true;
	  	$output = "	<div class=\"catalog-list clearfix\">$output</div>";
	  	
			if(!$_GET["p_all"]){
	    	$list = array();
	    	for ($j = 0; $j<$i/$count_on_page; $j++){
	    		if($j+1 == $party){
	    			$list[] = "<a class=\"selected\">".($j+1)."</a>";
	    		}else{
	    			$list[] = "<a href=\"".$p_url."p_party=".($j+1)."&pricesort=".$_GET["pricesort"]."&color=".$_GET["color"]."\">".($j+1)."</a>";
	    		}
	    	}
	    	$paginator = "";
	    	if(count($list)>1){
	    		$paginator = "<p class=\"paginator\">".implode("\n", $list)." <a href=\"".$p_url."p_all=1&pricesort=".$_GET["pricesort"]."&brand=".$_GET["color"]."\">показать всё</a></p>";
	    	}
			}
  	}else{
  		$output = "<p>По вашему запросу ничего не найдено.</p>";
  	}
  	
  	$select_color = "";
  	$res_color = mysql_query("select distinct value from har_elements where pe_code='color' order by value");
  	while ($row_color = mysql_fetch_array($res_color)) {
  		if($_GET["color"]==$row_color["value"]){
    		$select_color .= "<option value=\"$row_color[value]\" selected>$row_color[value]</option>";
  		}else{
    		$select_color .= "<option value=\"$row_color[value]\">$row_color[value]</option>";
  		}
  	}
  	$select_color = "<option value=\"\"></option>".$select_color;
  	
  	$list_pages = "
			<div class=\"catalog-control\">
			<form method='get' action='$p_url' id='form-filter'>
				<p class=\"filter\">
					<label>Цвет <select name=\"color\">$select_color</select></label>
					<label>Сортировать по ".show_array($common_options["sort"], $_GET["pricesort"], "select", "pricesort")."</label> <a href=\"#\" onclick='$(\"#form-filter\").submit(); return false;'>ВЫПОЛНИТЬ</a></p>
				</form>
				$paginator
			</div>
		";
  	
  	$output = $list_pages.$output;
		
  	
  	return $output;
		
		
		
  }
  



function init_basket(){
  	if(!$_SESSION["b_id"]){
  		mysql_query("insert into baskets (date1, u_id) values (now(), ".nvl($_SESSION["u_id"], "null").")");
  		$_SESSION["b_id"] = mysql_insert_id();
  	}
  }

  function get_sum_basket(){
  	$total_price = 0;
  	$res = mysql_query("select sb.number, e.id from string_baskets sb, elements e where sb.b_id = '$_SESSION[b_id]' and sb.e_id = e.id");
  	while($row = mysql_fetch_array($res)){
  		$price = (int)get_value_har($row["id"], "price");
			$total_price += ($row["number"] * $price);
  	}
  	return $total_price;  	
  }
  
  function get_basket_mail(){
  	$message = "";

	  $price_delivery = get_value_option("delivery_cost");
  	$total_price = get_sum_basket();
  	$res = mysql_query("select sb.number, e.name, e.id from string_baskets sb, elements e where sb.b_id = '$_SESSION[b_id]' and sb.e_id = e.id");
  	while($row = mysql_fetch_array($res)){
  		$articul = get_value_har($row["id"], "articul");
			$message .= "$articul  ";
			$message .= "$row[name]  ";
  		$price = (int)get_value_har($row["id"], "price");
			$message .= "$price р. ";
			$message .= "$row[number]  \n";
			if(get_value_har($row["id"], "delivery_cost")){
			  //бесплатно
			  $price_delivery = 0;
			}
  	}
  	$message .= "\nОбщая стоимость $total_price р.\n";
		return $message;
  	
  }
  
	function state_basket(&$empty){
		global $common_options, $parameter_page;
  	$res = mysql_query("select sum(number) n from string_baskets where b_id = '$_SESSION[b_id]'");
  	$n = mysql_fetch_array($res);
  	if ($n["n"]) {
  		$total_price = get_sum_basket();
  		$total_count = $n["n"];
  		if($total_count>10&&$total_count<21){
  			$name = "Товаров";
  		}elseif ($total_count%10 == 1) {
  			$name = "Товар";
  		}elseif (($total_count%10)<5 && ($total_count%10)>1) {
  			$name = "товара";
  		}else {
  			$name = "товаров";
  		}
  		$output .= "
  		  <p class=\"h2\"><a href=\"$common_options[basket_url]\">Моя корзина</a></p>
				<p>Товаров: $total_count шт.  Стоимость: $total_price руб.</p>
  		";
  	}else{
  		$output .= "
  		  <p class=\"h2\"><a href=\"$common_options[basket_url]\">Моя корзина</a></p>
				<p>Пока ничего не добавлено</p>
  		";
  		$empty = "empty";
  	}
  	
  	
  	return $output;
  }
  
  
  function show_basket($p_mode = "write"){
  	$res = mysql_query("select sb.number, e.name, e.id, sb.id sb_id from string_baskets sb, elements e where sb.b_id = '$_SESSION[b_id]' and sb.e_id = e.id");
  	if(mysql_num_rows($res)){  		
			$url_basket = get_url_element(get_functional_element("basket"));
			if($p_mode=="write"){
				$output .= "<form action=\"$url_basket\" method=\"post\" id=\"bform\" onkeydown=\"javascript:if(13==event.keyCode){this.submit();}\">";
				$output .= "<input type=\"hidden\" name=\"p_cmd\" value=\"upd\" id=\"cmd\">";
			}
			$output .= "
					<table class=\"basket-table\">
						<thead>
							<tr>
								<th class='nobordr' width=\"9%\"></th>
								<th width=\"12%\">АРТИКУЛ</th>
								<th width=\"26%\">НАЗВАНИЕ</th>
								<th width=\"14%\">ЦВЕТ</th>
								<th width=\"16%\">КОЛИЧЕСТВО</th>
								<th width=\"18%\">ЦЕНА</th>
					";
				if($p_mode=="write"){
					$output .= "<th width=\"5%\" class='nobordr'></th>";
				}
				$output .= "
							</tr>
						</thead>
						<tbody>
			";
			$n = 0;
			$total_price = get_sum_basket();
	  	while($row = mysql_fetch_array($res)){
	  		$price = (int)get_value_har($row["id"], "price");
	  		$url = get_url_element(get_element($row["id"]));
				if($p_mode=="write"){
					$output .= "<input type=\"hidden\" name=\"p_id[]\" value=\"$row[sb_id]\">";
				}
  	  	$micro_pic = get_value_har($row["id"], "middle-pic");
  	  	if($micro_pic){
		  		$file_info = get_file_info($micro_pic);	
	  			$img = "<img src=\"$file_info[link]\" width=\"70\" />";
  	  	}
				
				$output .= "
					<tr>
						<td class='nobordr'>$img</td>
						<td>".get_value_har($row["id"], "articul")."</td>
						<td class=\"name\"><a href='$url'>$row[name]</a></td>
						<td>".get_value_har($row["id"], "color")."</td>
						<td class=\"value\">$row[number]</td>
						<td>".($price * $row["number"])." руб.</td>
					";
				if($p_mode=="write"){
					$output .= "<td><a href=\"/basket?p_cmd=del&p_id=$row[sb_id]\"><img src=\"/images/ico-del.gif\" alt=\"удалить товар из корзины\" title=\"удалить товар из корзины\" width=\"11\" height=\"11\" /></a></td>";
				}
				$output .= "
					</tr>
				";
	  	}
	  	$output .="
					</tbody>
				</table>
				<div class=\"total\">ИТОГО:  $total_price руб.</div>
					";
				if($p_mode=="write"){
					$output .= "<div class=\"button-order\"><a href=\"/basket?p_cmd=order2\">Оформить заказ</a></div>";
				}
				$output .= "</form>";
  	}else{
  		$output = "Корзина пуста.";
  	}
		
		return $output;
  }
  
  function show_order($param, $p_status = ""){
  	global $common_options;
  	
		$count_goods = mysql_result(mysql_query("select count(*) n from string_baskets sb, elements e where sb.b_id = '$_SESSION[b_id]' and sb.e_id = e.id"), "n");
		$output = "";
		
		if($count_goods){
  		$output .= "
  		<p><br><b>Чтобы завершить оформление заказа, необходимо заполнить следующие поля:</b></p>
  								<div id=\"form\"><form class=\"order-form\" id=\"order-form\" method=\"post\" action=\"".$common_options["basket_url"]."\">";
  		$output .= "<input type='hidden' name='p_cmd' value='save_order'>				  <input type='hidden' name='p_res' value='5' id='res'>";
  		if($p_status=="*"){
  			$output .= "<p class=\"error\">Поля, помеченные *, обязательны к заполнению!</p>";
  		}
  		$output .= "<table class='order-table'>";
  		
  		$other_city = "";
  		if($param["city"]==2){
  		  $other_city= "";
  		}else{
  		  $other_city= "style=\"display:none;\"";
  		}
  		if(($param["delivery"]==2)&&($param["city"]==2)){
  		  $delivery_cost= "<a  href=\"#\" onclick=\"JavaScript:calc_delivery();return false;\">Рассчитать стоимость доставки</a> <span style=\"font-weight:bold;\" id=\"del_cost\"></span>";
  		}else{
  		  $delivery_cost= "Стоимость доставки: 0р.";
  		}
  		$output .= "
	  		<script>
	  			var text_calc = '<a href=\"#\" onclick=\"JavaScript:calc_delivery();return false;\">Рассчитать стоимость доставки</a> <span style=\"font-weight:bold;\" id=\"del_cost\"></span>';
	  			function calc_delivery() {
	  			  city_name = \$('#oc_input').val();
	  			  if(city_name){
							$.ajax({
							  type: \"POST\",
							  url: \"/calc.php\",
							  data: ({ p_city: city_name }),
								success: function(msg){
								  if(msg==0){
								    \$('#del_cost').html('Невозможно рассчитать');
								  }else{
								  	\$('#del_cost').html(msg+' руб.');
								  }
							  }							  
							});	  			
						}
					}
	  			
	  		</script>
        <tr><td class=\"name\">Ваше имя* </td></tr><tr><td><input title=\"\" type=\"text\" name=\"firstname\" size=\"50\" value=\"".check_var_web($param["firstname"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">Телефон для связи* </td></tr><tr><td><input title=\"\" type=\"text\" name=\"phone\" size=\"50\"  value=\"".check_var_web($param["phone"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">E-mail </td></tr><tr><td><input title=\"\" type=\"text\" name=\"email\" size=\"50\" value=\"".check_var_web($param["email"])."\" class=\"input\"><br><input type=\"checkbox\" name=\"subscribe\" value=\"1\"> Подписаться на новости</td></tr>
        <tr><td class=\"name\">Город* </td></tr><tr><td>".show_array($common_options["city"], nvl(check_var_web($param["city"]), 1), "select", "city", "onChange='if(this.selectedIndex==1){\$(\"#other_city\").show();}else{\$(\"#other_city\").hide();}'")." <span  $other_city id=\"other_city\">Название города: <input type=\"text\" id=\"oc_input\" name=\"other_city\" size=\"30\" value=\"".check_var_web($param["other_city"])."\" class=\"shortinput\"></span></td></tr>
        <tr><td class=\"name\">Адрес доставки (улица, дом, квартира)* </td></tr><tr><td><input title=\"\" type=\"text\" name=\"address\" size=\"50\"  value=\"".check_var_web($param["address"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">Способ доставки </td></tr><tr><td>".show_array($common_options["delivery"], nvl(check_var_web($param["delivery"]), 1), "select", "delivery", "onChange='if(this.selectedIndex==0){\$(\"#delivery_cost\").html(\"Стоимость доставки: 0р.\");}else{\$(\"#delivery_cost\").html(text_calc);}'")." <span id=\"delivery_cost\">$delivery_cost</span></td></tr>
        <tr><td class=\"name\">Способ оплаты</td></tr><tr><td>".show_array($common_options["oplata"], nvl(check_var_web($param["oplata"]), 1), "select", "oplata")."</td></tr>
        <tr><td class=\"name\" valign=top>Комментарии к заказу</td></tr><tr><td><textarea name=\"comment\" class=\"textarea\" rows=\"5\" cols=\"30\">".check_var_web($param["comment"])."</textarea></td></tr>";
  		$output .= "</table></form></div>";
  		$output .= "<div class=\"button button-order\" style=\"margin-left:0px;\"><a href=\"#\" onClick=\"JavaScript:document.getElementById('res').value='4'; var f=document.getElementById('order-form'); f.submit(); return false;\">Оформить заказ</a></div>";
  		if($p_status=="*"){
  			$output .= "<p class=\"error\">Поля, помеченные *, обязательны к заполнению!</p>";
  		}
		}
		return $output;
  }
  
  function delete_basket_user(){
		$res = mysql_query("select * from baskets where u_id = '$_SESSION[u_id]'");
		while ($b = mysql_fetch_array($res)) {
			mysql_query("delete from string_baskets where b_id = '$b[id]'");
			mysql_query("delete from baskets where id = '$b[id]'");
		}
  }

  function get_user ($p_id){
  	$res = mysql_query("select * from users where id = '$p_id'");
  	$u =  mysql_fetch_array($res);
  	$u["fio"] = $u["lastname"]." ".$u["firstname"]." ".$u["middlename"];
  	return $u;
  }
  
  function replace_keywords($p_text, $p_u_id = null)  {
  	$text = $p_text;
  	if($p_u_id){
	  	$user = get_user($p_u_id);
  	}else{
	  	$user = get_user($_SESSION["u_id"]);
  	}
		$text = str_replace("%login%", $user["login"], $text);
		$text = str_replace("%password%", $user["password"], $text);
		$text = str_replace("%fio%", $user["fio"], $text);
		$text = str_replace("%name%", $user["fio"], $text);
		$text = str_replace("%io%", $user["firstname"]." ".$user["middlename"], $text);
		
		return $text;
  }


  function get_short_desc($id, $mode = "user", $p_url = "", $p_sravn=1){
    global $common_options; 
    
    $take = false;
    
  	$element = get_element($id);
  	
    if($element["main_id"]){
      $url = get_url_element($element);
      $element = get_element($element["main_id"]);
    }
      
    if(!$url){
      $url = get_url_element($element);
    }
    
  	$small_pic = get_value_har($element["id"], "small-pic");
		if($small_pic){
			$small_pic_info = get_file_info($small_pic);
		  $img = "<a href=\"$url\"><img src=\"$small_pic_info[link]\" width=\"$small_pic_info[width]\" height=\"$small_pic_info[height]\"></a>";
		}
    
    $anons = str_replace("\n", "<br>", get_value_har($element["id"], "anons"));
      
    $price = (int)get_value_har($element["id"], "price");
    $old_price = (int)get_value_har($element["id"], "oldprice");
    if($old_price){
    	$op = "<del>$old_price р.</del>";
    }else {
    	$op = "";
    }
    
      
		$output .= "
			<div class=\"catalog-item\">
				<div class=\"picture\"><div><span>$img</span></div></div>
				<p><a href=\"$url\">$element[name]</a></p>
				<p class=\"price\">$price р. $op</p>
			</div>
  	";

  	return $output;
  }
  
  function get_short_short_desc($id, $mode = "user", $p_url = "", $p_sravn=1){
    global $common_options; 
    
    $take = false;
    
  	$element = get_element($id);
  	
    if($element["main_id"]){
      $url = get_url_element($element);
      $element = get_element($element["main_id"]);
    }
      
    if(!$url){
      $url = get_url_element($element);
    }
    
  	$small_pic = get_value_har($element["id"], "small-pic");
		if($small_pic){
			$small_pic_info = get_file_info($small_pic);
		  $img = "<a href=\"$url\"><img src=\"$small_pic_info[link]\" width=\"$small_pic_info[width]\" height=\"$small_pic_info[height]\"></a>";
		}
    
    $anons = str_replace("\n", "<br>", get_value_har($element["id"], "anons"));
      
    $price = (int)get_value_har($element["id"], "price");
    $nal = (int)get_value_har($element["id"], "sklad");
    $sklad = "<i>".$common_options["sklad"][$nal]."</i>";
    $old_price = (int)get_value_har($element["id"], "oldprice");
    if($old_price){
    	$op = "<del>$old_price <span class=\"rub\">a</span></del>";
    }
      
		$output .= "
			<div class=\"recitem\"> $img
				<h2><a href=\"$url\">$element[name]</a></h2>
				$op
				$sklad
				<p>$price <span class=\"rub\">a</span></p>
			</div>
			
    	";

  	return $output;
  }  

  function get_short_desc_group($id, $mode = "user", $p_url = "", $p_sravn=1){
    global $common_options; 
    
    $take = false;
    
  	$element = get_element($id);
  	
    if($element["main_id"]){
      $url = get_url_element($element);
      $element = get_element($element["main_id"]);
    }
      
    if(!$url){
      $url = get_url_element($element);
    }
    
  	$small_pic = get_value_har($element["id"], "small-pic");
		if($small_pic){
			$small_pic_info = get_file_info($small_pic);
		  $img = "<a href=\"$url\"><img src=\"$small_pic_info[link]\" width=\"$small_pic_info[width]\" height=\"$small_pic_info[height]\"></a>";
		}
    
    $anons = str_replace("\n", "<br>", get_value_har($element["id"], "anons"));
      
      
		$output .= "
			<div class=\"catalog-item catalog-item-group\">
				<h2><a href=\"$url\">$element[name]</a></h2>
				<div class=\"picture\">$img</div>
				<div class=\"text-block\">
				$anons
				</div>
			</div>
    	";

  	return $output;
  }  
  
  function get_js_fd($e_id, $big_pic, $index, $number_big_pic){
    $js = "";
  	if($big_pic){
			$big_pic_info = get_file_info($big_pic);
  	  $js .= "images[$number_big_pic] = '$big_pic_info[link]'; ";
  		$original_pic = get_value_har($e_id, "original_pic$index");
			$original_pic_info = get_file_info($original_pic);
  	  $js .= "big_images[$number_big_pic] = '$original_pic'; ";
  	  $js .= "big_width[$number_big_pic] = '$original_pic_info[width]'; ";
		  if($original_pic_info["height"]>600){
		    $original_pic_info["height"] = 600;
		  }
  	  $js .= "big_height[$number_big_pic] = '$original_pic_info[height]'; ";
  	}  	
  	return $js;
  }

  function get_str_photos($number_big_pic){
    $photos_str = "";
  	if($number_big_pic==1){
  	  $photos_class = "a";
  	}else{
  	  $photos_class = "na";
  	}

  	$photos_str .= "<span class=\"$photos_class\" id=\"span$number_big_pic\"><a href=\"#\" onClick=\"JavaScript:show_pic($number_big_pic);return false;\">$number_big_pic</a></span>";
  	return $photos_str;
  }



  
  function get_full_desc_new($id, &$meta){
    global $common_options;
  	$element_sort = $element = get_element($id);
  	
  	
    if($element["main_id"]){
      $element = get_element($element["main_id"]);
    }
    
  	$main_element_url = get_url_element($element);
  	
  	$photos_str = "";
		$fb_width = 0;
		$fb_height = 0;
		
		$parent_url = get_url_element(get_element($element["e_id"]));
  	
  	$big_pic = get_value_har($element["id"], "big-pic");
  	if($big_pic){
			$big_pic_info = get_file_info($big_pic);
    	$original_pic = get_value_har($element["id"], "original_pic");
			$original_pic_info = get_file_info($original_pic);
		  $img = "<a href=\"$original_pic_info[link]\" class=\"fb-group\" rel=\"photo\"><img src=\"$big_pic_info[link]\" height=\"$big_pic_info[height]\" ><i></i></a>";
  	}
    
    $articul = "";
    $articul_har = get_value_har($element["id"], "articul");
    if($articul_har){
      $articul = "<p class=\"articul\">Артикул $articul_har</p>";
    }
    $nal = (int)get_value_har($element["id"], "sklad");
    $sklad = "<p class=\"nali4ie\">".$common_options["sklad"][$nal]."</p>";
    
    $old_price = (int)get_value_har($element["id"], "oldprice");
    if($old_price){
    	$op = "<del>$old_price р.</del>";
    }else {
    	$op = "";
    }
		
		$output .= "
		
					<div class=\"clearfix\">
						<div class=\"col-wrap1\">
							<div class=\"col-wrap2\">
								<div class=\"col1\">
									<div class=\"product-picture\">
										<div class=\"picture\">
											<span>$img</span>
										</div>
									</div>
									<div class=\"product-characteristics clearfix\">
										<table class=\"right-back\" >
											<tr>
												<td>
													<fb:like href=\"http://za-za-zu.com".$main_element_url."\" send=\"false\" layout=\"button_count\" width=\"150\" show_faces=\"false\"></fb:like>												
											</tr>
											<tr>
												<td><script type=\"text/javascript\"><!--
document.write(VK.Share.button(false,{type: \"round_nocount\", text: \"Поделиться\"}));
--></script></td>
											</tr>
											<tr>
												<td>&lt; <a href=\"$parent_url\">перейти в каталог</a></td>
											</tr>
										</table>
										<table>
				";
		$meta = "
		<meta property=\"og:title\" content=\"$element[name]\" />
		<meta property=\"og:type\" content=\"product\" />
		<meta property=\"og:url\" content=\"http://za-za-zu.com".$main_element_url."\" />
		<meta property=\"og:image\" content=\"http://za-za-zu.com".$big_pic_info["link"]."\" />
		<meta property=\"og:site_name\" content=\"ZA-ZA-ZU.com\" />
		<meta property=\"fb:admins\" content=\"100002130543697\" />
		";
//		
		$attr = get_value_har($element["id"], "height_r");
		if($attr){
			$output .= "
											<tr>
												<td>Высота с ручкой</td>
												<td>$attr</td>
											</tr>
			";
		}
		$attr = get_value_har($element["id"], "height");
		if($attr){
			$output .= "
											<tr>
												<td>Высота без ручки</td>
												<td>$attr</td>
											</tr>
			";
		}
		$attr = get_value_har($element["id"], "width");
		if($attr){
			$output .= "
											<tr>
												<td>Ширина</td>
												<td>$attr</td>
											</tr>
			";
		}
		$attr = get_value_har($element["id"], "bottom");
		if($attr){
			$output .= "
											<tr>
												<td>Дно</td>
												<td>$attr</td>
											</tr>
			";
		}
		$output .= "
										</table>
									</div>
								</div>
								<div class=\"col2\">
									<div class=\"prodict-details\">
										<p class=\"articul\">$articul</p>
										<h2>$element[name]</h2>
										<p class=\"mini\">".get_value_har($element["id"], "brand")."</p>
										$sklad
										<p class=\"mini2\">".get_value_har($element["id"], "anons")."</p>
										<p class=\"color\">Цвет: ".get_value_har($element["id"], "color")."</p>
										<p class=\"price\">".get_value_har($element["id"], "price")."р. $op</p>
										<div class=\"button\"><a href=\"$common_options[basket_url]?p_cmd=add&p_id=$element[id]&p_order=1\">В корзину</a></div>
									</div>
								</div>
								<div class=\"callback\"><a href=\"/feedback2\">Обратный звонок</a></div>
								<div class=\"clear\"></div>
							</div>
						</div>
					</div>		
		";
		


		
  	return $output;
  }

  function get_path_elements($p_element){
  	global $common_options;
  	$tree_elements = array();
  	get_tree_elements($p_element["id"], $tree_elements);
  	$main = get_element(396);
  	$main["name"] = "Главная";
  	$tree_elements[] = $main;
  	$path = "";
  	for($i=count($tree_elements)-1; $i>=0; $i--){
  		if($i==0){
  			//последний
  			//$path .= $tree_elements[$i]["name"];
	 		}else{
  			if($tree_elements[$i]["name_eng"]!="catalog"){
	  			$path .= "<span><a href=\"".get_url_element($tree_elements[$i])."\">".$tree_elements[$i]["name"]."</a>";
		  		if($i > 0){
		  			$path .= " /";
		  		}
	  			$path .= "</span> ";
  			}
  		}
  		
  	}
  	
  	return "$path";
  }


  function process_error($error){
		global $common_options;

		$return_page["title"] = "Страница не найдена";
		$return_page["name"] = "Страница не найдена";
		$return_page["main_text"] = "Такой страницы не существует.<br>Возможно Вы просто неточно набрали адрес.<br>Попробуйте начать поиск с главной страницы <a href=\"$common_options[site_url]\">$common_options[site_name]</a>";
		//$return_page["error"] = $error;
		$return_page["error"] = "404";
  	
  	return $return_page;
  }
  
  
  function get_main_parameter_page($element, $parameter_page = array()){
  	global $common_options;
  	$parameter_page["id"] = $element["id"];
  	$parameter_page["e_id"] = $element["e_id"];
  	$parameter_page["te_id"] = $element["te_id"];
  	if($element["te_id"]==102){
    	$parameter_page["e_id"] = $element["id"];
  	}
  	$parameter_page["name"] = $element["name"];
  	$parameter_page["url"] = get_url_element($element, 0);
  	$parameter_page["main_text"] = get_value_har($element["id"], "main_text");
  	$parameter_page["title"] = nvl(get_value_har($element["id"], "title"), $element["name"]);
  	$parameter_page["keywords"] = get_value_har($element["id"], "keywords");
  	$parameter_page["description"] = get_value_har($element["id"], "description");
  	if($parameter_page["id"]==$common_options["index"]){
  	  $parameter_page["template"] = "index";
  	}else{
  	  $parameter_page["template"] = "main";
  	}
  	
  	
  	return $parameter_page;
  }
  
  
  function get_add_parameter_page($parameter_page){
  	global $common_options, $timer;
  	
  	
  	$main_element = get_element($parameter_page["id"]);

  	//получаем список элементов в главном меню
  	$i = 0;
  	$j = 0;
  	$menu = array();
  	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and e.te_id and he.pe_code = 'is_menu' and he.value='1' and e.te_id in (102) order by e.sort");	
  	while($group = mysql_fetch_array($res)){
  		if(get_value_har($group["id"], "is_access")){
  		  $i++;
  			if($parameter_page["id"]==$group["id"]){
  				$menu[] = "<td><a href=\"$url\">".strtoupper($group["name"])."</a></td>";
  			}else{
    			$url = get_url_element($group);
  				$menu[] = "<td><a href=\"$url\">".strtoupper($group["name"])."</a></td>";
  			}
  		}
  	}
  	$parameter_page["menu"] = implode("<td class=\"col\"></td>", $menu);
  	
  	//получаем список элементов в нижнем меню
  	$i = 0;
  	$j = 0;
  	$menu = array();
  	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and e.te_id and he.pe_code = 'is_bottommenu' and he.value='1' and e.te_id in (102) order by e.sort");	
  	while($group = mysql_fetch_array($res)){
  		if(get_value_har($group["id"], "is_access")){
  		  $i++;
  			if($parameter_page["id"]==$group["id"]){
  				$menu[] = "<li><a href=\"$url\">".strtoupper($group["name"])."</a></li>\r\n";
  			}else{
    			$url = get_url_element($group);
  				$menu[] = "<li><a href=\"$url\">".strtoupper($group["name"])."</a></li>\r\n";
  			}
  		}
  	}
  	$parameter_page["footermenu"] = implode("<li>|</li>\r\n", $menu);
  	
  	
  	//первый уровень товаров с иконками
  	$list = "";
		$query = "select e.* from elements e, har_elements he where  e.te_id = 301 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'  order by e.sort ";
		$res = mysql_query($query);
  	while(($item = mysql_fetch_array($res))){
			$i++;
			$url = get_url_element($item);
			if(strtolower($_SERVER["REQUEST_URI"])==$url){
      	$list .= "<li class=\"ico_$item[name_eng]\"><a>$item[name]</a></li>";
			}elseif (strtolower(substr($_SERVER["REQUEST_URI"], 0, strlen($url)))==$url){
      	$list .= "<li class=\"ico_$item[name_eng] selected\"><a href=\"$url\">$item[name]</a></li>";
			}else{
      	$list .= "<li class=\"ico_$item[name_eng]\"><a href=\"$url\">$item[name]</a></li>";
			}
  	}
  	$parameter_page["index_flc"] = $list;


  	
  	if($main_element["id"] == $common_options["index"]){
  		
  		//баннеры
  		for ($i=2; $i<3; $i++){
	  		$banner_text= "";
	  		$banner1 = get_value_har($main_element["id"], "banner$i");
	  		$banner1_url = get_value_har($main_element["id"], "url_banner$i");
	  		if($banner1){
	  			$banner1_info = get_file_info($banner1);
	  			if($banner1_url){
		  			$banner_text = "<div class=\"tb1\"><a href=\"$banner1_url\"><img src=\"$banner1_info[link]\" width=\"$banner1_info[width]\" height=\"$banner1_info[height]\" /></a></div>";
	  			}else{
		  			$banner_text = "<div class=\"tb1\"><img src=\"$banner1_info[link]\" width=\"$banner1_info[width]\" height=\"$banner1_info[height]\" /></div>";
	  			}
	  		}
		  	$parameter_page["banner$i"] = $banner_text;
  		}
  		
  		//слайд
    	$slide = "";
  		$query = "select e.* from elements e, har_elements he where e.e_id=$main_element[id] and  e.te_id = 106 and he.e_id = e.id and he.value = '1' and he.pe_code = 'type'  order by sort ";
  		$res = mysql_query($query);
  		$i = 0;
    	while(($item = mysql_fetch_array($res))){
	  		if(get_value_har($item["id"], "is_access")){
		  		$banner1 = get_value_har($item["id"], "banner");
		  		$banner1_url = get_value_har($item["id"], "url_banner");
		  		if($banner1){
		  			$banner1_info = get_file_info($banner1);
		  			if($banner1_url){
			  			$slide .= "<li class=\"slide\"><a href=\"$banner1_url\"><img src=\"$banner1_info[link]\" width=\"$banner1_info[width]\" height=\"$banner1_info[height]\" /></a></li>";
		  			}else{
			  			$slide .= "<li class=\"slide\"><img src=\"$banner1_info[link]\" width=\"$banner1_info[width]\" height=\"$banner1_info[height]\" /></li>";
		  			}
		  		}
	  		}
    	}
    	$parameter_page["slide"] = "
				<div id=\"title-slider\">
					<ul class=\"spec-main\">
					$slide
					</ul>
					<a href=\"#\" id=\"title-slider-right\"></a>
					<a href=\"#\" id=\"title-slider-left\"></a>
				</div>
			";
    	
  		//банер верхний правый
    	$slide = "";
  		$query = "select e.* from elements e, har_elements he where e.e_id=$main_element[id] and  e.te_id = 106 and he.e_id = e.id and he.value = '2' and he.pe_code = 'type'  order by rand() ";
  		$res = mysql_query($query);
  		$i = 0;
  		$banner_text= "";
    	while(($item = mysql_fetch_array($res))&&($i<1)){
	  		if(get_value_har($item["id"], "is_access")){
	  			$i++;
		  		$banner1 = get_value_har($item["id"], "banner");
		  		$banner1_url = get_value_har($item["id"], "url_banner");
		  		if($banner1){
		  			$banner1_info = get_file_info($banner1);
		  			if($banner1_url){
			  			$banner_text = "<div class=\"tb1\"><a href=\"$banner1_url\"><img src=\"$banner1_info[link]\" width=\"$banner1_info[width]\" height=\"$banner1_info[height]\" /></a></div>";
		  			}else{
			  			$banner_text = "<div class=\"tb1\"><img src=\"$banner1_info[link]\" width=\"$banner1_info[width]\" height=\"$banner1_info[height]\" /></div>";
		  			}
		  		}
	  		}
    	}
	  	$parameter_page["banner1"] = $banner_text;
    	
    	//хиты продаж
    	$list = "";
  		$query = "select e.* from elements e, har_elements he where  e.te_id = 203 and he.e_id = e.id and he.value = '1' and he.pe_code = 'show_titul'  order by rand() ";
  		$res = mysql_query($query);
  		$i = 0;
    	while(($item = mysql_fetch_array($res))&&$i<4){
	  		if(get_value_har($item["id"], "is_access")){
		  		$pic = get_value_har($item["id"], "middle-pic");
		  		if($pic){
		  			$i++;
		  			$pic_info = get_file_info($pic);
		  			$url = get_url_element($item);
		  			$price = get_value_har($item["id"], "price")." р.";
		  			$list .= "<div class=\"catalog-title-item\"><a href=\"$url\"><img src=\"$pic_info[link]\" width=\"$pic_info[width]\" height=\"$pic_info[height]\" /><span>$price</span></a></div>";
		  		}
	  		}
    	}
    	$parameter_page["hits"] = "
				<div class=\"catalog-title-list clearfix\">
				$list
				</div>
    	";
    	
    }else {
    	//то что не используется на главной странице
    	
    	$parameter_page["path"] = get_path_elements($main_element);
    	
    }
    
  	if($main_element["id"] == $common_options["spec_page"]){
    	//страница спецпредложения
    	$list = "";
  		$query = "select e.* from elements e, har_elements he where  e.te_id = 203 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_spec'  order by e.id ";
  		$res = mysql_query($query);
  		$i = 0;
    	while(($item = mysql_fetch_array($res))){
	  		if(get_value_har($item["id"], "is_access")){
  				$list .= get_short_desc($item["id"]);
	  		}
    	}
    	
    	$parameter_page["main_text"] = "<div class=\"catalog-list\">$list</div>";
  	}
    
  	if($main_element["id"] == $common_options["new_page"]){
    	//страница новинки
    	$list = "";
  		$query = "select e.* from elements e, har_elements he where  e.te_id = 203 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_new'  order by e.id ";
  		$res = mysql_query($query);
  		$i = 0;
    	while(($item = mysql_fetch_array($res))){
	  		if(get_value_har($item["id"], "is_access")){
  				$list .= get_short_desc($item["id"]);
	  		}
    	}
    	
    	$parameter_page["main_text"] = "<div class=\"catalog-list\">$list</div>";
  	}
    
  	
  	//получаем список новостей
  	$i = 0;
  	$action = "";
  	$res = mysql_query("select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id=501  order by e.date desc limit 3");
  	while($item = mysql_fetch_array($res)){
			$i++;
			$url = get_url_element($item);
    	$action .= "
				<dt>".(intval($item["day"]).".".$item["month"].".".$item["year"])."</dt>
				<dd><a href=\"$url\">$item[name]</a></dd>
			";
  	}
  	if($action){
  	  $action = "
				<p class=\"h2\">Новости</p>
				<dl class=\"news-list\">
  	  		$action
				</dl>
			";
  	}
  	$parameter_page["firstnews"] = $action;
  	

  	//получаем список элементов в левом меню 
  	  	
  	if(!$parameter_page["flc"]){
    	$i = 0;
    	$menu = "";
    	$catalog_root_id = get_main_element($common_options["catalog_te_id"]);
    	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id = 201 and e.e_id='$catalog_root_id'  order by e.sort");	
    	while($group = mysql_fetch_array($res)){
  			$url = get_url_element($group);
				if(strtolower($_SERVER["REQUEST_URI"])==$url){
	      	$menu .= "<li class=\"selected\">$group[name]</li>";
				}elseif (strtolower(substr($_SERVER["REQUEST_URI"], 0, strlen($url)))==$url){
	      	$menu .= "<li class=\"selected\"><a href=\"$url\">$group[name]</a></li>";
				}else{
	      	$menu .= "<li><a href=\"$url\">$group[name]</a></li>";
				}
  			
  			
    	}
    	$parameter_page["flc"] = $menu;
    	
  	}

  	
  	
  	
  	
  	if($_SESSION["u_id"]){
  		$u = get_user($_SESSION["u_id"]);
  		$parameter_page["auth"] = "<p class=\"login\">Здравствуйте, <strong><a href=\"/reg.htm?p_cmd=modify\">".nvl(trim($u["firstname"]), $u["login"])."</a></strong></p>";
  	}else{
  		$parameter_page["auth"] = "
  		
					<p class=\"login\"><a href=\"/reg.htm?p_cmd=auth\" onclick=\"login('#login-form'); return false\">Авторизация</a></p>
					<p class=\"red\"><a href=\"/reg.htm\">Регистрация</a></p>
			<div id=\"login-form\">
	<form id=\"form1\" name=\"form1\" method=\"post\" action=\"/reg.htm\"  onkeydown=\"javascript:if(13==event.keyCode){document.getElementById('form1').submit();}\">
	<input type=\"hidden\" name=\"p_cmd\" value=\"auth\" />
			<a href='#'  onclick=\"JavaScript: $('#login-form').animate({height: 'hide'}, 300); return false\"><img src=\"/images/close.png\" alt=\"Закрыть\" width=\"10\" height=\"10\" id=\"close-button\"  /></a>
			<table width=\"100%\">
				<tr>
					<td>Логин</td>
					<td width=\"10%\"><input name=\"login\" type=\"text\" class=\"login-field\" /></td>
				</tr>
				<tr>
					<td>Пароль</td>
					<td><input name=\"password\" type=\"password\" class=\"login-field\" /></td>
				</tr>
				<tr>
					<td colspan=\"2\"><a href=\"#\" class=\"enter\" onclick=\"JavaScript:document.getElementById('form1').submit(); return false; \">Вход</a><a href=\"/reg.htm?p_cmd=remem\" class=\"amnesia\">Забыл пароль</a></td>
				</tr>
			</table>
				</form>
				</div>
  		";
  	}
  	
  	
  	return $parameter_page;
  }
  
  
  
  function get_page($parameter_page){
  	global $common_options, $timer;
  	if($parameter_page["template"]){
  		$template = get_content_file($common_options["tpl_folder"].$parameter_page["template"].".tpl");
  	}else{
  		$template = get_content_file($common_options["tpl_folder"]."main.tpl");
  	}
  	$page = $template;
  	
  	if($parameter_page["error"]=="404"){
  		header("HTTP/1.x 404 Not Found");
  	}else{
  		header("HTTP/1.x 200 OK");
  	}
  	
  	
  	
  	$empty = "";
  	
		$page = str_replace("{@TITLE@}", $parameter_page["title"], $page);
		$page = str_replace("{@NAME@}", $parameter_page["name"], $page);
		$page = str_replace("{@KEYWORDS@}", $parameter_page["keywords"], $page);
		$page = str_replace("{@DESCRIPTION@}", $parameter_page["description"], $page);
		$page = str_replace("{@MENU@}", $parameter_page["menu"], $page);
		$page = str_replace("{@FOOTER_MENU@}", $parameter_page["footermenu"], $page);
		$page = str_replace("{@BANNER1@}", $parameter_page["banner1"], $page);
		$page = str_replace("{@BANNER2@}", $parameter_page["banner2"], $page);
		$page = str_replace("{@SLIDE@}", $parameter_page["slide"], $page);
		$page = str_replace("{@CONTACTS@}", get_value_option("address"), $page);
		$page = str_replace("{@ADDRESS2@}", get_value_option("address2"), $page);
		$page = str_replace("{@SOC_CARDS@}", get_value_option("soc_cards"), $page);
		$page = str_replace("{@CATALOG_MENU@}", $parameter_page["catalog_menu"], $page);
		$page = str_replace("{@FIRSTNEWS@}", $parameter_page["firstnews"], $page);
		$page = str_replace("{@FLC@}", $parameter_page["flc"], $page);
		$page = str_replace("{@PATH@}", $parameter_page["path"], $page);
		$page = str_replace("{@MAINTEXT@}", $parameter_page["main_text"], $page);
		$page = str_replace("{@META@}", $parameter_page["meta"], $page);
		$page = str_replace("{@HITS@}", $parameter_page["hits"], $page);
		$page = str_replace("{@SPEC@}", $parameter_page["spec"], $page);
		$page = str_replace("{@STATEBASKET@}", state_basket($empty), $page);
  	$page = str_replace("{@AUTH@}", $parameter_page["auth"], $page);
		$page = str_replace("{@MAPYANDEX@}", get_value_option("map"), $page);

		return $page;
		
		
  	
  }
  
  
?>