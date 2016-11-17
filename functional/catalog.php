<?php


  function get_price_sort($url){
  	global $parameter_page;
  	
  	$output = "";
  	$output .= "<table class=\"pricesort\"><tr><td>Сортировать по цене: </td><td><a href=\"$url?pricesort=1&p_all=1\"><img src=\"/images/down.gif\" title=\"по убыванию\"></a></td><td><a href=\"$url?pricesort=2&p_all=1\"><img src=\"/images/up.gif\" title=\"по возрастанию\"></a></td></tr></table>";
  	
  	return $output;
  }

	function get_titulgroup($group_id){
    $list = array($group_id);
    $output = "";
    $res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id = 202 and e.e_id='$group_id' order by e.sort");
  	while($group = mysql_fetch_array($res)){
  	  $list[] = $group["id"];
  	}

  	if(count($list)){
	  	if($_GET["pricesort"]){
	  		$e_id = implode(",", $list);
  	  	$query = "select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show_grouptitul' and he.value='1' and e.te_id = 203 and e.e_id in ($e_id) order by ";
  	  	if($_GET["pricesort"]==1){
  	  		$query .= "e.price desc";
  	  	}elseif ($_GET["pricesort"]==2){
  	  		$query .= "e.price asc";
  	  	}
        $res = mysql_query($query);
      	while($item = mysql_fetch_array($res)){
      	  if (get_value_har($item["id"], "is_access")) {
        	  $output .= get_short_desc($item["id"]);
      	  }
      	}
	  	}else{
	  	  foreach ($list as $e_id) {
	  	  	$query = "select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show_grouptitul' and he.value='1' and e.te_id = 203 and e.e_id = '$e_id' order by e.sort";
	        $res = mysql_query($query);
	      	while($item = mysql_fetch_array($res)){
	      	  if (get_value_har($item["id"], "is_access")) {
	        	  $output .= get_short_desc($item["id"]);
	      	  }
	      	}
	  	  }
	  	}
  	}
  	
  	return $output;

  }


  
  function get_level($p_element, $p_tree, $p_current_id){
  	global  $common_options, $parameter_page;
  	$output = "";
		$query = "select e.* from elements e, har_elements he 
	            where e.e_id = '$p_element[id]' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'
	              and e.te_id in (201, 202) 
	            order by e.sort";
		
   	$res = mysql_query($query);
   	if(mysql_num_rows($res)){
   		if($p_element["te_id"] == $common_options["catalog_te_id"]){
	   		$output .= "<ul  id=\"catalog-tree\">";
   		}else{
	   		$output .= "<ul>";
   		}
	   	while ($row = mysql_fetch_array($res)) {
   	    $class = "";
   	    if(array_search($row["id"], $p_tree)!==false){
	   	    $class = " class=\"selected\"";
   	    }
	   		if($p_element["te_id"] == $common_options["catalog_te_id"]){
	  			if($p_current_id==$row["id"]){
	  				$output .= "<li><h3>$row[name]</h3>";
	  			}else{
	    			$url = get_url_element($row);
	  				$output .= "<li ><h3><a href=\"$url\" $class>$row[name]</a></h3>";
	  			}
	   		}else{
	  			if($p_current_id==$row["id"]){
	  				$output .= "<li>$row[name]";
	  			}else{
	    			$url = get_url_element($row);
	  				$output .= "<li ><a href=\"$url\" $class>$row[name]</a>";
	  			}
	   		}
  			if(array_search($row["id"], $p_tree)!==false){
     			$output .= get_level($row, $p_tree, $p_current_id);
  			}
	   		$output .= "</li>";
	   	}
   		$output .= "</ul>";
   	}
   	
   	return $output;
  }

  

  function get_flc($id)	{    
    global $common_options;
    get_tree_elements($id, $tree);
    $new_tree = array();
    foreach ($tree as $v) {
      $new_tree[] = $v["id"];
    }
    
    
    $flc = "";
    
    $root = get_element(get_main_element($common_options["catalog_te_id"]));
    $flc = get_level($root, $new_tree, $id);
  	
  	return $flc;
  }
  
		$parent_element = array();
		$error = false;
  
  
		for($number_elem_url = 1; ($number_elem_url < count($p_urls))&&(!$error); $number_elem_url++){
			$elem_url = get_page_name($p_urls[$number_elem_url]);
			if($elem_url){
				if(!$parent_element["id"]){
					$catalog = get_main_element($common_options["catalog_te_id"]);
					$query = "select e.* from elements e, har_elements he 
					            where e.e_id = '$catalog' and e.name_eng = '$elem_url' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access' ";
				}else{
					$query = "select e.* from elements e, har_elements he 
					            where e.e_id = '$parent_element[id]' and e.name_eng = '$elem_url' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'";
				}
				$res = mysql_query($query);
				if(mysql_num_rows($res)){
					$parent_element = mysql_fetch_array($res);
					
				}else{
					$parameter_page["error"] = "404";
					$error = true;
				}
			}
		}
		
		
		if($parameter_page["error"]){
			$parameter_page = process_error($error);
		}else{

			if(!$parent_element["e_id"]){
				$parent_element["id"] = get_main_element($common_options["catalog_te_id"]);
				$parent_element["te_id"] = $common_options["catalog_te_id"];
			}
			
			if($parent_element["te_id"]==$common_options["catalog_te_id"]){
				$parameter_page["id"] = $parent_element["id"];
				$parameter_page["title"] = "Подарочные сертификаты";

				$parameter_page["name"] = $parent_element["name"];
				
				$p_color = get_var_web("color", "web");
				
				if($p_color){
					$query = "select e.* from elements e, har_elements he where e.te_id = 203 and he.e_id = e.id and he.value = '$p_color' and he.pe_code = 'color'  order by ";
				}else{
					$query = "select e.* from elements e, har_elements he where e.te_id = 203 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'  order by ";
				}
				
				$party = $_GET["p_party"];
				if(!$party){
					$party = 1;
				}
				
                if($_GET["pricesort"]=="asc"){
                    $query .= "e.price asc";
                }elseif ($_GET["pricesort"]=="desc"){
                    $query .= "e.price desc";
                }else {
                    $query .= "e.sort asc";
                }
				
				
				$url = get_url_element($parent_element);
				$number_on_page = nvl(get_value_option("number_on_page"), 10);
				if($_GET["p_all"]){
					$parameter_page["main_text"] .= show_list($query, $url."?", 1, 100000, $find);
				}else {
					$parameter_page["main_text"] .= show_list($query, $url."?", $party, $number_on_page, $find);
				}
				
				$parameter_page["template"] = "catalog";
				
			} elseif (($parent_element["te_id"]=="201")) {
			  //вывод основной группы
				$parameter_page["name"] = $parent_element["name"];
				$parameter_page["id"] = $parent_element["id"];
				$parameter_page["template"] = "catalog";
				$parameter_page["title"] = $parent_element["name"];

				
				$party = $_GET["p_party"];
				if(!$party){
					$party = 1;
				}

				$p_color = get_var_web("color", "web");
				
				if($p_color){
					$query = "select e.* from elements e, har_elements he where e.e_id = '$parent_element[id]' and e.te_id = 203 and he.e_id = e.id and he.value = '$p_color' and he.pe_code = 'color'  order by ";
				}else{
					$query = "select e.* from elements e, har_elements he where e.e_id = '$parent_element[id]' and e.te_id = 203 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'  order by ";
				}
				
                if($_GET["pricesort"]=="asc"){
                    $query .= "e.price asc";
                }elseif ($_GET["pricesort"]=="desc"){
                    $query .= "e.price desc";
                }else {
                    $query .= "e.sort asc";
                }
				$find = false;
				$list_goods = "";
				$number_on_page = nvl(get_value_option("number_on_page"), 10);
				if($_GET["p_all"]){
				  $list_goods .= show_list($query, $url."?", 1, 100000, $find);
				}else {
				  $list_goods .= show_list($query, $url."?", $party, $number_on_page, $find);
				}

				  $parameter_page["main_text"] = $list_goods;

			}elseif ($parent_element["te_id"]=="203") {
			  //вывод товара
				$parameter_page["title"] = nvl($parent_element["name"]);
				$parameter_page["id"] = $parent_element["id"];
				$parameter_page["template"] = "catalog";
			  if($_GET["cmd"]=="print"){
  				$parameter_page["template"] = "print";
  				$parameter_page["name"] = $parent_element["name"];
        	$big_pic = get_value_har($parent_element["id"], "big-pic");
      		if($big_pic){
      			$big_pic_info = get_file_info($big_pic);
      		  $img = "<img src=\"$big_pic_info[link]\"  width=\"$big_pic_info[width]\" height=\"$big_pic_info[height]\">";
      		}
  				$parameter_page["main_text"] = "
            <td class=\"photo\"><div>$img<div><div class=\"price\">Артикул ".get_value_har($parent_element["id"], "articul")."<br>Цена&nbsp;<b style=\"font-size: 18px;\">".get_value_har($parent_element["id"], "price")."&nbsp;p.</b></div></td>
            <td class=\"desc\" width=\"100%\">".get_value_har($parent_element["id"], "main_text")."
            </td>
          ";
  				
  				
  				
  				
			  }else {
  			  $group = get_element($parent_element["e_id"]);
  			  $bf = (int)$_GET["bf"];
  			  $sf = (int)$_GET["sf"];
  			  $cmd = get_var_web("cmd");
			  	$output = "";
  			  
  			  if($cmd=="quest"){
  			  	
  			  	
  			  	$output .= "
  						<div id=\"form-quest\">
  							<p><strong>Задать вопрос по товару:</strong></p>
  							<form method='post' id='quest' action='".get_url_element($parent_element)."'>
  							<input name=\"cmd\"  value=\"quest-send\" type=\"hidden\" />
  							<input name=\"goodid\"  value=\"$parent_element[id]\" type=\"hidden\" />
  							<table>
  								<tr>
  									<td>Ваше имя</td>
  									<td><input name=\"name\" type=\"text\" class=\"fb-input\" /></td>
  								</tr>
  								<tr>
  									<td>E-mail</td>
  									<td><input name=\"email\" type=\"text\"  class=\"fb-input\"/></td>
  								</tr>
  								<tr>
  									<td>Контактный телефон</td>
  									<td><input name=\"phone\" type=\"text\"  class=\"fb-input\"/></td>
  								</tr>
  							</table>  							
  							<table style=\"margin-top: 18px;\">
  								<tr>
  									<td colspan=\"2\">Пожалуйста, задайте Ваши вопросы относительно товара<br />$parent_element[name]:
  									</td>
  								</tr>
  								<tr>
  									<td colspan=\"2\">
  										<textarea name=\"message\" cols=\"\" rows=\"\" class=\"fb-textarea\"></textarea>
  									</td>
  								</tr>
  								<tr>
  									<td colspan=\"2\" class=\"fq-send\"><a href=\"#\" onClick=\"JavaScript:var f=document.getElementById('quest'); if(f.email.value||f.phone.value){f.submit(); return false;}else{alert('Необходимо заполнить хотя бы один из контактов'); return false;}\">Отправить</a></td>
  								</tr>
  							</form>
  						</div>  			  	
  			  	";
  			  	
  			  	
  			  	$pp = array("title"=>"Задать вопрос по товару", "main_text" => $output, "template"=>"fb_white");
  			  	echo get_page($pp);

					  die();


  			  }elseif ($cmd=="quest-send"){				
      	    $p_name = trim(get_var_web("name"));
      	    $p_phone = trim(get_var_web("phone"));
      	    $p_email = trim(get_var_web("email"));
      	    $p_message = trim(get_var_web("message"));
      	    if($p_email||$p_phone){
    	  		  $message = "<h3>Вопрос по товару $parent_element[name]</h3>
    	  		               <table>
    	  		               <tr><td align=\"right\">Имя: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
    	  		               <tr><td align=\"right\">E-mail: </td><td><b>".htmlspecialchars(stripslashes($p_email))."</b></td></tr>
    	  		               <tr><td align=\"right\">Телефон: </td><td><b>".htmlspecialchars(stripslashes($p_phone))."</b></td></tr>
    	  		               <tr><td align=\"right\">Сообщение: </td><td><b>".htmlspecialchars(stripslashes($p_message))."</b></td></tr>
    	  		               </table> 
    	  		             ";
    	  		  $feedback_email = get_value_option("feedback_email");
    	  	    $status = @my_mail_html($feedback_email, "Вопрос по товару $parent_element[name]", $message, "site@izh-nozh.ru");
    	  	    $output = "<p><strong>Задать вопрос по товару</strong></p><p>Ваш вопрос отправлен. В ближайшее время мы свяжемся с Вами.</p>";
      			}else{
    	  	    $output = "<p><strong>Задать вопрос по товару</strong></p><p>Не все поля заполнены. Необходимо вернуться <a href=\"JavaScript: history.back()\">назад</a></p>";
      			}
  			  	$pp = array("title"=>"Задать вопрос по товару", "main_text" => $output, "template"=>"fb_white");
  			  	echo get_page($pp);

					  die();
          	
  			    
  			  }elseif ($bf){
  			  	$output = "";
						$pic_info = get_file_info($bf);
					  $img = "<img src=\"$pic_info[link]\" id=\"fb-bf\"/>";
				  	$photos_str = "";
			  	
				  	for ($i=1; $i<7; $i++){
				  		if($i==1){
				  			$index = "";
				  		}else{
				  			$index = $i;
				  		}
				  		echo $parent_element["id"];
			  		
					  	$big_pic = get_value_har($parent_element["id"], "original_pic$index");
					  	$micro_pic = get_value_har($parent_element["id"], "micro-pic$index");
					  	if($big_pic&&$micro_pic){
					  		$file_info = get_file_info($micro_pic);	
					  		$big_file_info = get_file_info($big_pic);	
					  		if($sf == $micro_pic){
					  			$photos_str .= "
					  			<div class='fb-selected' id='sf$micro_pic'>
										<div class=\"fb-mini-preview-top	\">
											<div class=\"fb-mini-preview-bottom\">
												<img src=\"$file_info[link]\" width=\"$file_info[width]\" height=\"$file_info[height]\" onClick=\"JavaScript: change_pic('$big_file_info[link]', 'sf$micro_pic');\" /> </div>
										</div>
										</div>
									<script language=\"JavaScript\">
										var current_sf = 'sf$micro_pic';
									</script>
										
					  			";
					  		}else{
					  			$photos_str .= "
					  			<div id='sf$micro_pic'>
										<div class=\"fb-mini-preview-top	\">
											<div class=\"fb-mini-preview-bottom\">
												<img src=\"$file_info[link]\" width=\"$file_info[width]\" height=\"$file_info[height]\"  onClick=\"JavaScript: change_pic('$big_file_info[link]', 'sf$micro_pic');\"/> </div>
										</div>
										</div>
					  			";
					  		}
						  }
				  	}
				  	
					  
					  $output .= "
  			  		<table width='100%' height='100%'><tr>
  			  	  <td align='center'  valign='middle' style='vertical-align: middle !important;' >$img</td>
  			  	  <td align='right' style='padding: 100px 20px 0 20px;'>$photos_str</td>
  			  	  </tr></table>";
  			  	
  			  	$pp = array("title"=>"Фото товара", "main_text" => $output, "template"=>"fb_white");
  			  	echo get_page($pp);

					  die();


					   			  	
  			  }else{
	  			  $parent_group = get_element($group["e_id"]);
	  				$parameter_page["name"] = $parent_element["name"];
	  				$meta = "";
	  				$parameter_page["main_text"] .= get_full_desc_new($parent_element["id"], $meta);
	  				$parameter_page["meta"] = $meta;
	  				

  			  }
  				
			  }
			}
			
		}
  



?>