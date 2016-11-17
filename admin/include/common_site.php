<?php

function show_list_photos($query, $type = "alb"){
  	global $common_options;
  	$res = mysql_query($query);
  	$i = 0;
  	while($item = mysql_fetch_array($res)){
  		$smallpic = get_value_har($item["id"], "small-pic");
  		if($smallpic){
  			$file_info = get_file_info($smallpic);
				$i++;
	  		if(($i-1)%3==0) {
	  			$output .= "<tr>";
	  		}
    	  if($type=="alb"){
  	  		$output .= "<td><a href=\"".get_url_element($item)."\" ><img src=\"$file_info[link]\" width=\"$file_info[width]\" height=\"$file_info[height]\"/></a></td>";
    	  }else {
      		$big_pic1 = get_value_har($item["id"], "big-pic");
      		if($big_pic1){
      			$big_photo_info = get_file_info($big_pic1);
    	  		$output .= "<td><a href=\"$big_photo_info[link]\"  rel=\"fb-group\" class=\"fb\" ><img src=\"$file_info[link]\" width=\"$file_info[width]\" height=\"$file_info[height]\"/></a></td>";
      		}else {
    	  		$output .= "<td><img src=\"$file_info[link]\" /></td>";
      		}
    	  }
	  		if($i%3==0) {
	  			$output .= "</tr>";
	  		}
  		}
  	}
  	if($i%3){
  		$output .= str_repeat("<td></td>", 3-$i%3);
  		$output . "</tr>";
  	}
  	if($output){
  		$output = "<table  class=\"pics\">".$output."</table>";
  	}
		return $output;
  }



  function  search($query) {
  
    $output .= "<p>Искали: $query</p>";
    if(strlen($query)>2){
    	$i = 0;    	
    	$query_sql = "
    	  select distinct e.id, e.name, he.e_id 
    	  from elements e, har_elements he 
    	  where e.id = he.e_id 
    	    and (he.value like '%".$query."%' or e.name like '%".$query."%') 
    	  order by e.name
    	";
  	  $res = mysql_query($query_sql);
  	  while ($item = mysql_fetch_array($res)) {
  	  	if(get_value_har($item["id"], "is_access")){
  	  		$i++;
  	  		$url = get_url_element($item);
			$output .= "<div class=\"search_item\">";
  	  		//$output .= "<a href=\"$url\">".str_replace($query, "<b>".$query."</b>", $item["name"])."</a>";
  	  		$main_text = strip_tags(get_value_har($item["id"], "main_text"));
  
          $start = strpos($main_text, $query);
          if ($start < 100) {
                  $start = 0;
                  $text = "";
          } else {
                  $start = $start - 100;
                  $text = "...";
          }
          if ($start + 250 > strlen($main_text)) {
                  $text.= substr($main_text, $start);
          } else {
                  $text.= substr($main_text, $start, 250)."...";
          }
          $text = "<a href=\"$url\">".str_replace($query, "<b>".$query."</b>", $item["name"])."</a>" . str_replace($query, "<b>".$query."</b>", $text);
		  
		  $icon = get_value_har($item["id"], "icon");	
		  if($icon){
			$file_info = get_file_info($icon);
			//$output .= $file_info[link];
			$output .= "<div class=\"search_image\"><a href=\"$url\"><img src=\"$file_info[link]\"></img></a></div>";
		  } else {
			  $output .= "<div class=\"search_image\"></div>";
		  }
          $output .= "<div class=\"search_text\">". $text . "</div>";
          $output .= "</div>";
  	  	}
  	  }
  	  if($i == 0){
  	  	$output .= "По Вашему запросу ничего не найдено!";
  	  }
    }else{
    	$output .= "Слишком короткая фраза для поиска!\nПопробуйте ввести не менее 3-х символов.";
    }
    
    return $output;
    	
  }


  function show_form($p_name, $p_email, $p_message){
  	/*return "<br><br><a name=\"bottom\"></a>
  	  <p><b>                                 </b></p>
			<table  id=\"feedback\" class=\"no-border\">
			        <form method=\"post\" action=\"#bottom\">
			            <input type=\"hidden\" name=\"p_cmd\" value=\"send\" />
			            <tr>
			                <td align=right >Имя*</td>
			                <td ><input class=\"input-short\" name=\"p_name\" size=\"40\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >E-mail*</td>
			                <td ><input class=\"input-short\" name=\"p_email\" size=\"40\" value=\"".htmlspecialchars(stripslashes($p_email))."\"/></td>
			            </tr>
			            <tr>
			                <td  align=right >Комментарий</td>
			                <td ><textarea class=\"input\" name=\"p_message\" cols=\"35\" rows=\"7\">".htmlspecialchars(stripslashes($p_message))."</textarea></td>
			            </tr>
			            <tr>
			                <td></td><td  ><input class=\"button\" type=\"submit\" value=\"Отправить\" /></td>
			            </tr>
			        </form>
			</table>  ";
     */
    return "";    
	}


  function get_level($p_element, $p_level = 0){
  	global  $common_options;
  	$output = "";
		$query = "select e.* from elements e, har_elements he 
	            where e.e_id = '$p_element[id]' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'
	              and e.te_id in (100, 101, 102, 103, 201, 202, 203, 500, 105)
	              and sys<>1
	            order by e.sort";
		
   	$res = mysql_query($query);
   	if(mysql_num_rows($res)){
   		if($p_level){
   			$in = "_in";
   		}else{
   			$in = "";
   		}
   		$output .= "<ul class=\"map$in\">";
	   	while ($row = mysql_fetch_array($res)) {
	   		$url = get_url_element($row);
	   		$output .= "<li><a href=\"$url\">$row[name]</a>";
	   		$output .= "</li>";
   			$output .= get_level($row, $p_level+1);
	   	}
   		$output .= "</ul>";
   	}
   	
   	return $output;
  }

  function get_icon($p_element){
  	$icon = get_value_har($p_element["id"], "icon");
  	if($icon){
  	  return $icon;
  	}else{
  	  if($p_element["e_id"]){
  	    $e = get_element($p_element["e_id"]);
  	    return get_icon($e);
  	  }
  	}
  }

  function get_path_elements($p_element){
  	global $common_options;
  	$tree_elements = array();
  	get_tree_elements($p_element["id"], $tree_elements);
  	if($p_element["id"] != $common_options["index"]){
  	  $main = get_element($common_options["index"]);
    	$tree_elements[] = $main;
  	}
  	$path = "";
  	for($i=count($tree_elements)-1; $i>=0; $i--){
  		if($i==0){
  			//         
  			$path .= $tree_elements[$i]["name"];
  		}else{
  		  if ($i==count($tree_elements)-1){
    			$path .= "<a href=\"".get_url_element($tree_elements[$i])."\" class=\"first\">".$tree_elements[$i]["name"]."</a>";
  		  }else{
    			$path .= "<a href=\"".get_url_element($tree_elements[$i])."\">".$tree_elements[$i]["name"]."</a>";
  		  }
  		}
  		
  		if($i > 0){
  			$path .= " / ";
  		}
  	}
  	
  	return "$path";
  }


  function process_error($error){
		global $common_options;

		$return_page["title"] = "                   ";
		$return_page["name"] = "                   ";
		$return_page["main_text"] = "                            .<br>                                        .<br>                                           <a href=\"$common_options[site_url]\">$common_options[site_name]</a>";
		//$return_page["error"] = $error;
		$return_page["error"] = "404";
  	
  	return $return_page;
  }
  
  function get_parent_page($element){
  	global $common_options;
    if($element["e_id"] == $common_options["site_root"]){
      $parent_page = $element["id"];
    }elseif($element["te_id"] == 201){
      $parent_page = $element["e_id"];
    }else{
      $parent_page = $element["e_id"];
    }
    
    return $parent_page;
  }
  
  
  function get_main_parameter_page($element, $parameter_page = array()){
  	global $common_options;
  	$parameter_page["id"] = $element["id"];
  	$parameter_page["e_id"] = $element["e_id"];
  	$parameter_page["e_id2"] = get_parent_page($element);
  	$parameter_page["te_id"] = $element["te_id"];
  	$parameter_page["name"] = nvl(get_value_har($element["id"], "h1"), $element["name"]);
  	$parameter_page["main_text"] = get_value_har($element["id"], "main_text");
  	$parameter_page["title"] = nvl(get_value_har($element["id"], "title"), $element["name"]);
  	$parameter_page["keywords"] = get_value_har($element["id"], "keywords");
  	$parameter_page["description"] = get_value_har($element["id"], "description");
  	if($parameter_page["id"] == $common_options["index"]){
  	  $parameter_page["template"] = "index";
  	}elseif ($parameter_page["id"] == $common_options["feedback_page"]){
  	  $parameter_page["template"] = "main";
  	}else{
  	  $parameter_page["template"] = "main";
  	}
  	
  	
  	return $parameter_page;
  }
  
  
  function get_short_desc($id1, $id2 = "", $params=array()) {
    global $common_options; 
    
    $ids[] = $id1;
    $ids[] = $id2;
    
    foreach ($ids as $key => $id) {
      if($id){
      	$element = get_element($id);
      	$parent_element = get_element($element["e_id"]);
      	
        $anons = ($element["te_id"]==10) ? "             ": get_value_har($element["id"], "anons");
        if(!$anons){
          $main_text = strip_tags(get_value_har($element["id"], "main_text"));
          $anons = (strlen($main_text)>100) ? substr($main_text, 0, 19)."..." : $main_text;
        }
        
        $price = (int)get_value_har($element["id"], "price");
      	if($element["te_id"]==10){
            $url = $common_options["collect_url"].$element["id"];
      	} else {
            $url = ($params["url"]) ? $params["url"].$id: get_url_element($element);
      	}
        
        $img = "";
        
      	$small_pic = get_value_har($element["id"], "photo");
        if (!$small_pic) {
            $small_pic = get_value_har($element["id"], "icon");
        }
        if($small_pic){
            $small_pic_info = get_file_info($small_pic);
            
            $img = '
            <div class="cat-pic">
                <a href="' . $url . '">
                  <img title="' . $element["name"] . '" alt="' . $element["name"] . '" src="' . $small_pic_info["link"] . '"></a>
            </div>';
            
            /*"<div style=\"width:$small_pic_info[width]px; height:$small_pic_info[height]px;\" class=\"cat-pic\"><a href=\"$url\"><img src=\"$small_pic_info[link]\" width=\"$small_pic_info[width]\" height=\"$small_pic_info[height]\"  alt='$element[name]' title='$element[name]'></a><i class=\"tl\"></i><i class=\"tr\"></i><i class=\"bl\"></i><i class=\"br\"></i></div>";*/
        }
        
//    						<p class=\"all-presents\"><a href=\"".get_url_element($parent_element)."\">$parent_element[name]</a></p>
          
    		$output .= "
                <div class=\"catalog-item-content\">
                    $img
                    <div class=\"cat-pic_desc\">
                        <a href=\"/catalog/" . $element["id"] . "\"><p class=\"anons\"> " . $anons . " </p>	</a>
                        <p class=\"cat-pic_desc_text\">" . $element["name"] . "</p>
                    ";
                    /*<a href=\"$url\"><p class=\"anons\">$anons</p></a>							
                    <p>$element[name]</p>*/
                    
                    

                    
    		if(!$params["notprice"]){
      		$output .= '
                        <div class="price">
                            <div>' . $price . 
                                '<span class="rur">p</span>
                            </div>
                        </div>
                        <div class="buy more">
                            <a href="' . $url . '">Подробнее</a>
                        </div>
          	';
            /*<a href=\"/order.php\" onclick=\"yaCounter23184481.reachGoal('OPENFORM'); return true;\" class=\"fancy_order_form fb\">Подробнее</a>*/
            /*<div class=\"price\"><div>$price.<span class=\"rur\">p<span>  .</span></span></div></div>*/
    		}
    		$output .= "
                    </div>
                </div>
        	";
        	
      }
    }
        
    if($output){
        $output = "
            <li class=\"catalog-item\">
                $output
            </li>
        ";
    }

  	return $output;
  }
  
  
  
  function get_add_parameter_page($parameter_page){
  	global $common_options;
  	
  	$price_group = get_value_option("price_group");
  	$price_groups = explode("\n", $price_group);
  	foreach ($price_groups as $value) {
  		$price1 = explode("|", $value);
  		$price2 = explode("-", $price1[0]);
  		$common_options["price"][$price2[0]] = $price1[1];
  		$common_options["price_left"][$price2[0]] = $price2[1];
  	}
  	
  	
  	$main_element = get_element($parameter_page["id"]);


  	//                                        
  	$menu = "";$i=0;
  	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_topmenu' and he.value='1' and e.e_id = '$common_options[site_root]' order by e.sort");	
  	while($group = mysql_fetch_array($res)){
  		if(get_value_har($group["id"], "is_access")){
  		  $i++;
      	$submenu = "";
      	$res_sub = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.e_id=$group[id] and e.te_id=103 order by e.sort");  			
      	while ($sub = mysql_fetch_array($res_sub)) {
      		if(get_value_har($sub["id"], "is_access")){
      			$url = get_url_element($sub);
      			$submenu .= "<li><a href=\"$url\" >$sub[name]</a></li>";
      		}
      	}
      	if($submenu){
      	  $menu .=  "<li class=\"more\">";
      	}else{
      	  $menu .=  "<li>";
      	}
  			if($parameter_page["id"]==$group["id"]){
  				//              
  				$menu .= "$group[name]";
  			}else{
    			$url = get_url_element($group);
  				$menu .= "<a href=\"$url\" title=\"$group[name]\">$group[name]</a>";
  			}

      	if($submenu){
      	  $menu .=  "<ul>$submenu<li class=\"corners\"></li></ul>";
      	}
  			
      	$menu .= "</li>";

      	
      	
  		}
  	}
  	$parameter_page["topmenu"] = $menu;
  	
  	//                                      
  	if(!$parameter_page["leftmenu"]){
  	  $text_menu = "";
  	  $quick = "";
    	$menu = "";$i=0;
    	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id = '201' order by e.sort");	
    	while($group = mysql_fetch_array($res)){
  		  $i++;
  		  if($group["id"]==879){
  		    $class=" rose-weight";
  		  }else{
  		    $class = "";
  		  }
  			if($parameter_page["id"]==$group["id"]){
  				//              
  				$menu .= "<li class=\"selected$class\">$group[name]</li>";
  				$quick .= "<li>$group[name]</li>";
  			}else{
    			$url = get_url_element($group);
  				$menu .= "<li class=\"$class\"><a href=\"$url\" title=\"$group[name]\">$group[name]</a></li>";
  				$quick .= "<li><a href=\"$url\" title=\"$group[name]\">$group[name]</a></li>";
  			}
    	}
//$menu .= "<li><a href=\"/corp\">Корпоративные</a></li>";

    	if($menu){
    	  $text_menu .= "<ul class=\"left-menu\">$menu</ul>";
    	}
    	
    	$menu = "";$i=0; $base_url = "/catalog/price?p=";
  		foreach ($common_options["price"] as $k=>$v){
    	  $url = $base_url.$k;    	  
  			if($url == $_SERVER['REQUEST_URI']){
  				//              
  				$menu .= "<li class=\"selected\">$v</li>";
  				$quick .= "<li class=\"selected\">$v</li>";
  			}else{
  				$menu .= "<li><a href=\"$url\">$v</a></li>";
  				$quick .= "<li><a href=\"$url\">$v</a></li>";
  			}
    	}
    	if($menu){
    	  $text_menu .= "<ul class=\"left-menu\">$menu</ul>";
    	}
    	
    	$menu = "";$i=0; $base_url = "/catalog/collect?c=";
    	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_collect' and he.value='1' and e.te_id = '10' order by e.sort");	
    	while($group = mysql_fetch_array($res)){
    		if(get_value_har($group["id"], "is_access")){
    		  $i++;
      	  $url = $base_url.$group["id"];    	  
    			if($url == $_SERVER['REQUEST_URI']){
    				//              
    				$menu .= "<li class=\"selected\">$group[name]</li>";
    			}else{
    				$menu .= "<li><a href=\"$url\">$group[name]</a></li>";
    			}
    		}
    	}
    	if($menu){
    	  $text_menu .= "<p class=\"h2-name\">Мультиподарки</p><ul class=\"left-menu\">$menu</ul>";
    	}
    	
    	
    	
    	if($menu){
    	  $parameter_page["leftmenu"] = "<p class=\"h2-name\">Категории</p>".$text_menu;
    	}
    	if($quick){
    	  $parameter_page["quick"] = "<ul>".$quick."<li class=\"corners\"></li></ul>";
    	}
    	
  	}
  	
  	//if($parameter_page["id"] == $common_options["index"]){
    	//      
    	$pics = array();
    	$res = mysql_query("select * from elements where e_id = 2 and te_id=106");
    	while ($row = mysql_fetch_array($res)) {
    		$pics[]=$row;
    	}
    	
    	if(count($pics)){
      	$pic = $pics[rand(0, count($pics)-1)];
      	$photo = get_value_har($pic["id"], "photo");
      	
      	$photo_info = get_file_info($photo);
      	//print_r(get_value_har($pic["id"], "style"));die();
      	
      	
      	$parameter_page["head"] = "
        	<div class=\"podarok\" id=\"".get_value_har($pic["id"], "style")."\" style=\"background:url(".$photo_info["link"].") 0 0 no-repeat;\">
        		<div id=\"tel\"><a href=\"#\" title=\"         \"></a></div>
        		<div ><a title=\"Лучший подарок\">Лучший подарок</a></div>
        	</div>
      	";
    	}
    	
    	//      
  		$query = "select e.* from elements e, har_elements he where  e.te_id = 202 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_index'  order by e.sort ";
  		$res = mysql_query($query);
  		$i = 0;
  		$list = "";
    	while(($item = mysql_fetch_array($res))){
	  		if(get_value_har($item["id"], "is_access")){
	  				$list .= get_short_desc($item["id"]);
	  		}
    	}
    	$parameter_page["index_goods"] = $list;
    	
    	//                
  		$query = "select e.* from elements e, har_elements he where  e.te_id = 10 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_index'  order by e.sort ";
  		$res = mysql_query($query);
  		$i = 0;
  		$list = "";
    	$menu = "";$i=0; 
    	while(($item = mysql_fetch_array($res))){
  		  $external_link = get_value_har($item["id"], "ext_link");
  		  if($external_link){
  		    $url = $external_link;
  		  }else{
      	  $url = $common_options["collect_url"].$item["id"];    	  
  		  }
    	  if($i%7==0){
    	    $list .= "</ul><ul class=\"q-block\">";
    	  }
				$list .= "<li><a href=\"$url\">$item[name]</a></li>";
  		  $i++;
    	}
    	
    	if($list){
    	  $list = "<div class=\"quadro\"><ul class=\"q-block\">$list</ul></div>";
    	}
    	$parameter_page["index_links"] = $list;
//     	$parameter_page["head"] = "
//       	<div class=\"podarok\" id=\"".get_value_har($pic["id"], "style")."\" style=\"background:url(".$photo_info["link"].") 0 0 no-repeat;\">
//       		<div id=\"tel\"></div>
//       		<div ><a title=\"Лучший подарок\">Лучший подарок</a></div>
//       	</div>
//     	";

		$list_header = [];
      	$res = mysql_query("select value from options where code = 'slogan' or code = 'phone'");
      	while ($row = mysql_fetch_array($res)) {
    		$list_header[]=$row;
    	}
    	$parameter_page["head"] = "
      	<div class=\"podarok\" id=\"".get_value_har($pic["id"], "style")."\" style=\"background:url(".$photo_info["link"].") 0 0 no-repeat;\">
          <div id=\"base-logo\"><a href=\"/\"></a></div>
      		<div id=\"base-info\"><p>".$list_header[1]['value']."</p>
            <div id=\"base-tel\">".$list_header[0]['value']."</div>
          </div>
      	</div>
    	";

    	$banner_link = get_value_har($parameter_page["id"], "banner_link");
    	$banner_text = "";
			$query = "select e.* from elements e, har_elements he where e.id=he.e_id and e.te_id = '602' and he.value='1' and he.pe_code='show-titul'  order by rand()";
			
	  	$res = mysql_query($query);
	  	$i = 0;
	  	$output = "";
	  	while($item = mysql_fetch_array($res)){
	  		$pic = get_value_har($item["id"], "titul-pic");
	  		if($pic){
	  			$i++;
	  			$file_info = get_file_info($pic);
	  			if($i==1){
	  	  		$banner_text .= "<div class=\"h_pic\" style=\"display:block;\"><a href=\"$banner_link\"><img src=\"$file_info[link]\" width=\"$file_info[width]\" height=\"$file_info[height]\"/><span></span></a></div>";
	  			}else{
	  	  		$banner_text .= "<div class=\"h_pic\" style=\"display:none;\"><a href=\"$banner_link\"><img src=\"$file_info[link]\" width=\"$file_info[width]\" height=\"$file_info[height]\"/><span></span></a></div>";
	  			}
	  		}
	  	}
    	if($banner_text){
    	  $parameter_page["banner"] = "<div class=\"ban1\"><div id=\"h_pics\">$banner_text</div></div>";
    	}else{
    	  $parameter_page["banner"] = "";
    	}
    if($parameter_page["id"] == $common_options["index"]){	
  	  $parameter_page["banner"] = "<div class=\"ban1\">    	
      <div id=\"search\">
        <form action=\"/search\" name=\"SearchForm\" method=\"post\"> 
          <div><input id=\"ajaxSearch_input\" name=\"p_query\" type=\"text\" /></div>
           
        </form> 
      </div>
      <!-- VK Widget -->
      <div id=\"vk_groups\"></div>
      <script type=\"text/javascript\">
      VK.Widgets.Group(\"vk_groups\", {mode: 0, width: \"298\", height: \"241\", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 56838347);
      </script>
      </div>";	  	   	
  	  
  	} //<span class=\"link\" onclick=\"document.SearchForm.submit()\">Найти</span>
  	else{
  	  $parameter_page["banner"] = "
        <div id=\"search\">
          <form action=\"/search\" name=\"SearchForm\" method=\"post\"> 
            <div><input id=\"ajaxSearch_input\" name=\"p_query\" type=\"text\" /></div>
           
          </form> 
        </div>
        <!-- VK Widget -->
        <div id=\"vk_groups\"></div>
        <script type=\"text/javascript\">
        VK.Widgets.Group(\"vk_groups\", {mode: 0, width: \"160\", height: \"400\", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 56838347);
        </script>  	  
        ";
  	} //  <span class=\"link\" onclick=\"document.SearchForm.submit()\">Найти</span> 
  	
  	
  	if($parameter_page["id"] == $common_options["map_page"]){
		  $parameter_page["main_text"] .= "
		    <style>
		      .map li {list-style-type:none; margin-bottom:7px;}
		      .map_in li {margin-top:7px;}
		      .map_in li {list-style-type:none; margin-bottom:7px;}
		    </style>";
		  $parameter_page["main_text"] .= get_level(get_element($common_options["site_root"]));
  	}
  	
  	
//  	if($parameter_page["id"] == $common_options["index"]){
    	//              
	  	$action = "";$i=0;
	  	$res = mysql_query("select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id=401  order by e.date desc limit 2");
	  	while($item = mysql_fetch_array($res)){
	  	  $i++;
				$url = get_url_element($item);
				$action .= "
					<div class=\"block$i\">
					<dt>".(intval($item["day"])." ".$common_options["month_name"][$item["month"]]." ".$item["year"])."</dt>
					<dd><h3><a href=\"$url\">$item[name]</a></h3><p>".get_value_har($item["id"], "anons")."</p></dd>
      		</div>
				";					
	  	}
	  	if($action){
	  		$action = "<p class=\"h3\">Новости</p><dl class=\"news\">$action</dl>
			<p class=\"more\"><a href=\"/news\">Архив</a></p>";
	  	}
		  $parameter_page["firstnews"] = $action;
  	  
  	//}

  	$c = (int)get_var_web("c");
	  $g = (int)get_var_web("g");
    $p = (int)get_var_web("p");
  	
  	if(($parameter_page["te_id"] == 2)||($parameter_page["te_id"] == 201)||(!$g&&($parameter_page["te_id"] == 210))||(!$g&&($parameter_page["te_id"] == 211))){
    	//              
    	$params = array();
    	$coll = array();
    	if ($parameter_page["te_id"] == 211){
    	  $c = (int)get_var_web("c");
    	  $coll = get_element($c);
    	  //echo "!@".get_value_har($coll["id"], "is_collect");
    	  if(!get_value_har($coll["id"], "is_collect")){
    	    $parameter_page["te_id"]=213;
    	    $coll["te_id"]=213;
    	  }
    	}
    	if($parameter_page["te_id"] == 210){
    	  //            
		    if($common_options["price"][$p]){
		      //       
  				$query = "select e.* from elements e, har_elements he where e.price>=$p and e.price<".$common_options["price_left"][$p]."  and (e.te_id = 202 or e.te_id=10) and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'  order by e.te_id, e.sort";
//    			$parameter_page["name"] = "<span><a href=\"/catalog\">                </a> /</span><h1>".$common_options["price"][$p]."</h1>";
    			$parameter_page["name"] = "<h1>Подарки ".$common_options["price"][$p]."</h1>";
    			$parameter_page["title"] = "Подарки ".$common_options["price"][$p];
    			$params["url"] = $common_options["price_url"].$p."&g=";
		    }else{
  				$query = "select e.* from elements e, har_elements he where 1=2  and e.te_id = 202 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'  order by name";
    			$parameter_page["name"] = "<span><a href=\"/catalog\">Каталог подарков</a> /</span><h1>".$common_options["price"][$p]."</h1>";
		    }
    	}elseif ($parameter_page["te_id"] == 211){
    	  //            
    	  if($coll["id"]){
      		$query = "select e.orig_id id from elements e where e.e_id='$c' order by e.sort ";
//    			$parameter_page["name"] = "<span><a href=\"/catalog\">                </a> /</span><h1>$coll[name]</h1>";
    			$parameter_page["name"] = "<h1>Мультиподарок «$coll[name]»</h1>";
    			$parameter_page["title"] = "Мультиподарок «$coll[name]»";
    			$multi_price = get_value_har($coll["id"], "price");
    			$anons = get_value_har($coll["id"], "anons");    			
          $parameter_page["main_text"] .= "<p>$anons</p><p class=\"blue-italic\">Стоимость мультиподарка $multi_price <span class=\"rur\">p<span>уб.</span></span></p><br>";
    			$params["url"] = $common_options["collect_url"].$coll["id"]."&g=";
    			$params["notprice"] = 1;
		    }else{
      		$query = "select e.* from elements e, har_elements he where  e.te_id = 202 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access' order by e.name ";
    	  }
    	}elseif ($parameter_page["te_id"] == 213){
    	  //          
    	  if($coll["id"]){
      		$query = "select e.orig_id id from elements e where e.e_id='$c' order by e.sort ";
    			$parameter_page["name"] = "<h1>$coll[name]</h1>";
    			$parameter_page["title"] = "$coll[name]";
    			$params["url"] = $common_options["collect_url"].$coll["id"]."&g=";
		    }else{
      		$query = "select e.* from elements e, har_elements he where  e.te_id = 202 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access' order by e.sort, e.name ";
    	  }
    	}elseif ($parameter_page["te_id"] == 201){
    	  //      
    		$query = "select e.orig_id id from elements e where  e.te_id = 203 and e.e_id='$parameter_page[id]'  order by e.sort ";
    	  $parent = get_element($parameter_page["e_id"]);
  			$parameter_page["name"] = "<span><a href=\"".get_url_element($parent)."\">$parent[name]</a> /</span><h1>$parameter_page[name]</h1>";
  			$parameter_page["pic-bottom"] = get_value_har($parameter_page["id"], "pic_bottom");
  			$pic_top = get_value_har($parameter_page["id"], "pic_top");
  			if($pic_top){
        	$pic_top_info = get_file_info($pic_top);
    			$parameter_page["pic-top"] = $pic_top_info["link"];
  			}
    	}else{
    	  //            
    		$query = "select e.* from elements e, har_elements he where  (e.te_id = 202 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access') or (e.te_id = 10 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_collect') order by e.te_id, e.sort ";
    	}
    	
  		$res = mysql_query($query);
  		$i = 0;
  		$list = "";
  		$id1="";
  		$id2="";
    	while(($item = mysql_fetch_array($res))) {
	  		if(get_value_har($item["id"], "is_access")){
	  		  $i++;
	  		  // if (!($i%2)) {
	  		    // $id2 = $item["id"];
	  		    // $list .= get_short_desc($id1, $id2, $params);
        		// $id1="";
        		// $id2="";
	  		  // } else {
	  		    // $id1 = $item["id"];
	  		  // }
              $list .= get_short_desc($item["id"], null, $params);
              if (!($i%4)) {
                $list .= "<div></div>";
              }
	  		}
    	}
    	if($id1){
		    $list .= get_short_desc($id1, null, $params);
		    $i++;
    	}
    	if($list){
            $no = ($i<=8) ? "-no" : "";
          
            $parameter_page["main_text"] .= "
                <div class=\"catalog-inside clearfix\">
						<div id=\"scroller$no\">
							<div class=\"scroll-pane\">
							<ul class=\"main_catalog_list\">
							$list
                </ul>
							</div><!--/scroll-pane-->
						</div><!--/#scroller-->
					</div><!--/catalog-inside-->
					";
    	}
    	$parameter_page["template"] = "catalog";
		  
  	}
  	

  	
  	if(($parameter_page["te_id"] == 202)||($c&&$g&&($parameter_page["te_id"] == 211))||($p&&$g&&($parameter_page["te_id"] == 210))){
  	
    	//                  
    	if($parameter_page["te_id"] == 211){
    	  $coll = get_element($c);
    	  $good = get_element($g); 
    	  $parameter_page["id"] = $good["id"];
    	  if(!get_value_har($coll["id"], "is_collect")){
    	    $parameter_page["te_id"]=213;
    	    $coll["te_id"]=213;
    	  }
    	}elseif ($parameter_page["te_id"] == 210) {
    	  $good = get_element($g); 
    	  $parameter_page["id"] = $good["id"];
    	}
    	$img = "";
    	$photo = get_value_har($parameter_page["id"], "photo");
        $price = (int)get_value_har($parameter_page["id"], "price");
    	if($photo){
      	$photo_info = get_file_info($photo);
      	$img .= "<div class=\"full-img\">
					<div style=\"width:$photo_info[width]px; height:$photo_info[height]px;\" class=\"cat-pic\">
						<img src=\"$photo_info[link]\" alt=\"\"  />
						<i class=\"tl\"></i>
						<i class=\"tr\"></i>
						<i class=\"bl\"></i>
						<i class=\"br\"></i>
					</div>
				</div>";
      	
      	$dop_uslugi = get_value_option("dop_uslugi");
      	if($dop_uslugi){
      	  $img .= "<div class='dop_uslugi'>$dop_uslugi</div>";
      	}
      	$img .= "</div>";
    	}
    	if($parameter_page["te_id"] == 202){
            // $anons = get_value_har($parameter_page["id"], "anons");
            $anons = get_element($parameter_page["id"]);
			$anons = $anons['name'];
            if(!$anons){
              $main_text = strip_tags(get_value_har($parameter_page["id"], "main_text"));
              if(strlen($main_text)>100){
                $anons = substr($main_text, 0, 19)."...";
              }else{
                $anons = $main_text;
              }
            }
    	    $parent = get_element($parameter_page["e_id"]);
  			// $parameter_page["name"] = "<span><a href=\"".get_url_element($parent)."\">$parent[name]</a> /</span><h1>$anons</h1>";
  			$parameter_page["name"] = "<span><a href=\"".get_url_element($parent)."\">$parent[name]</a> /</span><h1>$anons</h1>";
			$parameter_page["product_title"] = $anons;
    	}elseif ($parameter_page["te_id"] == 213){
  			$parameter_page["name"] = "<span><a href=\"".$common_options["collect_url"]."$coll[id]\">$coll[name]</a> /</span><h1>$good[name]</h1>";
  			$parameter_page["title"] = "$coll[name] / $good[name]";
    	}elseif ($parameter_page["te_id"] == 210){
  			$parameter_page["name"] = "<span><a href=\"".$common_options["price_url"]."$p\">Подарки ".$common_options["price"][$p]."</a> /</span><h1>$good[name]</h1>";
  			$parameter_page["title"] = "Подарки ".$common_options["price"][$p]." / $good[name]";
    	}else{
  			$parameter_page["name"] = "<span><a href=\"".$common_options["collect_url"]."$coll[id]\">Мультиподарок «$coll[name]»</a> /</span><h1>$good[name]</h1>";
  			$parameter_page["title"] = "Мультиподарок «$coll[name]» / $good[name]";
    	}
    	
		  $parameter_page["main_text"] = $img;
		  $parameter_page["main_text"].= "<div class=\"left-img\">";
          
          if(($parameter_page["te_id"] == 202)||($parameter_page["te_id"] == 210)||($parameter_page["te_id"] == 213)){
            $parameter_page["main_text"].= "<div class=\"buy\">$price";
          }else {
            $multi_price = get_value_har($coll["id"], "price");
            $parameter_page["main_text"].= "<div class=\"buy\">$multi_price";
          }
          $parameter_page["main_text"].= " <span class=\"rur\">b</span> <a href=\"/order.php\" onclick=\"yaCounter23184481.reachGoal('OPENFORM'); return true;\" class=\"fancy_order_form fb\">Заказать</a></div>";
		  /*$parameter_page["main_text"].= "<a href=\"/contacts\" target=\"_blank\">Где купить</a>
          <a href=\"/service.php\" class=\"fancy_order_form fb\">Дополнительные услуги</a>
          <a href=\"#reviews\" class=\"anchor_link\">Посмотреть отзывы</a></div>";*/
          $parameter_page["main_text"].= "<div class=\"additional-info\">";
		  $parameter_page["main_text"].= "<div class=\"delivery__text\">Бесплатная доставка: 9:00 — 18:00</div>";
		  $parameter_page["main_text"].= "<div class=\"stock__text\">Скидка 3% при заказе на сайте</div>";
		  $parameter_page["main_text"].= "<h3>Варианты оплаты:</h3>";
		  $parameter_page["main_text"].= "<ul>
				<li>— Наличными курьеру
				<li>— Онлайн-перевод
				</ul>";
		  $parameter_page["main_text"].= "<div class=\"acc-to-pay yandex--money-block__text\">41001764076607</div>";
		  $parameter_page["main_text"].= "<div class=\"acc-to-pay sberbank--money-block__text\">4276 6800 1269 4806</div>";
		  $parameter_page["main_text"].= "</div>";
		  $parameter_page["main_text"].= "</div>";
          $parameter_page["main_text"].= "<div class=\"down-img\">";
          $parameter_page["main_text"].= "<div class=\"gray-italic\">".get_value_har($parameter_page["id"], "main_text")."</div>";
		  $parameter_page["main_text"].= "<p class=\"blue-bold\">".nvl(get_value_har($parameter_page["id"], "header_programms"), "Программа")."</p>";
		  $parameter_page["main_text"].= "<div class=\"black-bold\">".get_value_har($parameter_page["id"], "main_text2")."</div>";
		  $parameter_page["main_text"].= "<p class=\"blue-italic\">Активация</p>";
		  $parameter_page["main_text"].= "<div>".get_value_har($parameter_page["id"], "main_text3")."</div>";

		  
		  $albom = get_value_har($parameter_page["id"], "albom");
		  if($albom){
		  	$url = get_url_element(get_element($albom));
		  	$query = "select * from elements where e_id='$albom' order by rand() limit 3";
			  $parameter_page["main_text"].= "<br><br><div class=\"down-img\">";
			  $parameter_page["main_text"].= "<p class=\"blue-italic\"><a href=\"$url\">Фотоальбом</a></p>";
			  $parameter_page["main_text"].= "<div>".show_list_photos($query, "")."</div>";
			  $parameter_page["main_text"].= "</div>";
		  	
		  }
		  
		  
			session_start();		  
		  $cmd = get_var_web("p_cmd");
		  
		  if($cmd=="send_comment"){
		    $name = nvl(htmlspecialchars(stripslashes(trim(get_var_web("p_name")))), "Аноним");
		    $message = htmlspecialchars(stripslashes(trim(get_var_web("p_message"))));
		    if($name&&$message){
		    	if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['p_kode']){
		    	
			      mysql_query("insert into response (date, name, text, e_id) values (now(), \"$name\", \"$message\", $parameter_page[id])");
		    	}else{
		    		$error = "<p>Неверно введен текст с картинки</p>";
		    	}
		    }
		  }

		  $comments = "";
  		$query = "select r.*, date_format(r.date, '%d') day, date_format(r.date, '%m') month, date_format(r.date, '%Y') year from response r where  active=".true." e_id = '$parameter_page[id]' order by date desc ";
  		$res = mysql_query($query);
  		$i=0;
    	while(($item = mysql_fetch_array($res))){
    	  $i++;
    	  if($i%2){
      	  $side = "left";
    	  }else{
      	  $side = "right";
    	  }
    	  $comments .= "
					<div class=\"comment-$side\">
						<p><span>“</span>$item[text]<span>”</span></p>";
    	  if($item["video"]){
      	  $comments .= "<p>".$item["video"]."</p>";
    	  }
    	  $comments .= "
					</div>
					<p class=\"comment-by-left\">".(intval($item["day"]).".".$item["month"].".".$item["year"])." ~ $item[name]</p>
    	  ";
    	}
		
        
        
        
        $query = "select e.* from elements e, har_elements he where (e.te_id = 202 and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access') order by e.te_id, e.sort";
    	
  		$res = mysql_query($query);
        
        $list_same = array();
    	while(($item = mysql_fetch_array($res))) {
	  		$list_same[] = $item;
    	}
        shuffle($list_same);
            
            $parameter_page["main_text"] .= "<div class=\"same_product\">
                    <h2>С этим сертификатом также смотрят:</h2>
                    <div class=\"product\">
                        <ul class=\"additional_list\">";
            $i = 0;
            foreach ($list_same as $product) {
                $photo = get_value_har($product["id"], "photo");
                $anons = get_value_har($product["id"], "anons");
                if($photo){
                    $photo_info = get_file_info($photo);
                }
                
                $parameter_page["main_text"] .= "
                  <li class=\"catalog-item\">
                    <div class=\"catalog-item-content\">
                      <div class=\"cat-pic\">
                        <a href=\"/catalog/" . $product["id"] . "\">
                          <img title=\"" . $product["name"] . "\" alt=\"" . $product["name"] . "\" src=\"$photo_info[link]\"></a>
                      </div>
                      <div class=\"cat-pic_desc\">
                        <a href=\"/catalog/" . $product["id"] . "\"><p class=\"anons\"> " . $anons . " </p>	</a>							
                        <p class=\"cat-pic_desc_text\">" . $product["name"] . "</p>
                        <div class=\"price\"><div>" . $product["price"] . "<span class=\"rur\">
                        p<span></span></span></div></div>
                        <div class=\"buy more\">
                            <a href=\"/catalog/" . $product["id"] . "\">Подробнее</a>
                        </div>	
                      </div>
                    </div>
                  </li>";
                $i++;
                if ($i == 4) break;
            }
            $parameter_page["main_text"] .= "</ul>
                    </div>
                </div>";
            
            
			$parameter_page["main_text"] .= "
        <div class=\"comments\">
        $comments
          <p class=\"make-comment\"><a href=\"#\" onClick=\"JavaScript:document.getElementById('response').style.display=''; return false;\">              </a></p>
          <a name=\"response-form\"></a>
          <div id=\"response\" style='display:none;'>
          <table>

          <form method=\"post\">
              <input type=\"hidden\" name=\"p_cmd\" value=\"send_comment\" />
                <tr>
                    <td align=right class=\"title\">От кого</td>
              </tr>
                <tr>
                    <td ><input class=\"input-short\" name=\"p_name\" size=\"40\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
                </tr>
                <tr>
                    <td  align=right valign=top class=\"title\">Сам отзыв*</td>
                </tr>
              <tr>
                  <td ><textarea class=\"input\" name=\"p_message\" cols=\"35\" rows=\"7\">".htmlspecialchars(stripslashes($p_message))."</textarea></td>
                </tr>
                <tr>
                    <td  align=right valign=top class=\"title\">Введите код*</td>
                </tr>
                <tr class=\"capture\"> 
                    <td><img src=\"/cap.php\" width=160 height=80>
                    <input type=\"text\" name=\"p_kode\" class=\"input\"></td>
              </tr>
              <tr>
                  <td  ><input class=\"button\" type=\"submit\" value=\"Отправить\" /></td>
              </tr>
          </form>
          </table>
          </div>
        </div>";  
			
            $parameter_page["main_text"] .= "<div id=\"vk_comments\"></div>
<script type=\"text/javascript\">
VK.Widgets.Comments(\"vk_comments\", {limit: 5, width: \"665\", attach: \"photo\"});
</script>";



    	$parameter_page["template"] = "catalog";
		  
  	}
  	
  	if($parameter_page["te_id"] == 4){
    	//                
	  	$action = "";$i=0;
	  	$res = mysql_query("select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id=401  order by e.date desc");
	  	while($item = mysql_fetch_array($res)){
	  	  $i++;
				$url = get_url_element($item);
				$action .= "
					<div class=\"news-item\">
      			<p><a href=\"$url\">".(intval($item["day"])." ".$common_options["month_name"][$item["month"]]." ".$item["year"])."</a><br>".get_value_har($item["id"], "anons")."</p>
      		</div>
				";					
	  	}
		  $parameter_page["main_text"] = $action;
  	  
  	}elseif ($parameter_page["te_id"] == 401){
  		//       
	  	$res = mysql_query("select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year from elements e where e.id='$main_element[id]'");
	  	$item = mysql_fetch_array($res);
		  $parameter_page["main_text"] = "<p class='small'>".(intval($item["day"])." ".$common_options["month_name"][$item["month"]]." ".$item["year"])."</p>".$parameter_page["main_text"];
  	}

  	
  	if($parameter_page["id"] == $common_options["search_page"]){
      $query = get_var_web("p_query", "web");
      $parameter_page["main_text"] .= "
    		<div class=\"search\">
    			<form  action=\"/search\" name=\"SearchForm\" method=\"post\"> 
    				
    			</form>
    		</div>
    		
    		
      ";  //<input id=\"ajaxSearch_input\" name=\"p_query\" type=\"text\" /><span class=\"link\" onclick=\"document.SearchForm.submit()\">Найти</span>
      if($query){
        $parameter_page["main_text"] .= search($query);
      }
  	  
  	}
  	
  	if($parameter_page["id"] == $common_options["response_page"]){
  	  //      
  	  
			session_start();	
			
		  $cmd = get_var_web("p_cmd");
		  if($cmd=="send_comment"){
		    $name = nvl(htmlspecialchars(stripslashes(trim(get_var_web("p_name")))), "Аноним");
		    $message = htmlspecialchars(stripslashes(trim(get_var_web("p_message"))));
		    if($name&&$message){
		    	if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['p_kode']){
		    	
			      $response_success = mysql_query("insert into response (date, name, text) values (now(), \"$name\", \"$message\")"); 
						//var_dump($response_success); die();
				      $param = array();
		    	}else{
					//var_dump('Капча не прошла'); die();
		    		$error = "<p>Неверно введен текст с картинки</p>";
		    	}
		      
		      
		    }
		  }

		  $comments = "";
  		$query = "select r.*, date_format(r.date, '%d') day, date_format(r.date, '%m') month, date_format(r.date, '%Y') year from response r where  active=".true." order by date desc ";
  		$res = mysql_query($query);
  		$i=0;
    	while(($item = mysql_fetch_array($res))){
    	  $i++;
    	  if($i%2){
      	  $side = "left";
    	  }else{
      	  $side = "right";
    	  }
    	  $comments .= "
					<div class=\"comment-$side\">
						<p><span>“</span>$item[text]<span>”</span></p>";
    	  if($item["video"]){
      	  $comments .= "<p>".$item["video"]."</p>";
    	  }
    	  $comments .= "
					</div>
					<p class=\"comment-by-left\">".(intval($item["day"]).".".$item["month"].".".$item["year"])." ~ $item[name]</p>
    	  ";
    	}
    	
    	if($i>4){
    	  $comments = "<p class=\"make-comment-top\"><a href=\"#response-form\" onClick=\"responseCaptcha(); \">Добавить отзыв</a></p>".$comments;
    	}
    	$response_massage = '';
		if(isset($_POST['review_captcha']) and $_POST['review_captcha'] != null) {
			if(true === $response_success ) {
				$response_massage = '<span class="success_massage">Ваш отзыв принят и будет опубликован модератором!</span>';
			} elseif (false === $response_success) {
					$response_massage = '<span class="error_massage">Произошла ошибка при добавленни отзыва повторите попытку позже!</span>';
			} else {
				$response_massage  = '<span class="error_massage">'.$error.'</span>';
			}
			$display = "'style='display;'";
		} else {
			$display = "style='display:none'";
		}
		  
			     $parameter_page["main_text"] .= "
        <div class=\"comments\">
        $comments
          <p class=\"make-comment\"><a href=\"#\" onClick=\"JavaScript:document.getElementById('response').style.display=''; return false;\">              </a></p>
          <a name=\"response-form\"></a>
          <div id=\"responseErrors\" $display >$response_massage
          </div>
          <div id=\"response\" style='display:none'>
          <table>

          <form method=\"post\">
              <input type=\"hidden\" name=\"p_cmd\" value=\"send_comment\" />
                <tr>
                    <td align=right class=\"title\">От кого</td>
              </tr>
                <tr>
                    <td ><input class=\"input-short\" name=\"p_name\" size=\"40\" value=\"".htmlspecialchars(stripslashes($p_name))."\"/></td>
                </tr>
                <tr>
                    <td  align=right valign=top class=\"title\">Сам отзыв*</td>
                </tr>
              <tr>
                  <td ><textarea class=\"input\" name=\"p_message\" cols=\"35\" rows=\"7\">".htmlspecialchars(stripslashes($p_message))."</textarea></td>
                </tr>
                <tr>
                    <td  align=right valign=top class=\"title\">Введите код*</td>
                </tr>
                <tr class=\"capture\"> 
                    <td><img src=\"/cap.php\" width=160 height=80>
                    <input type=\"text\" name=\"p_kode\" class=\"input\"></td>
              </tr>
              <tr>
                  <td  ><input class=\"button\" name=\"review_captcha\" type=\"submit\" value=\"Отправить\" /></td>
              </tr>
          </form>
          </table>
          </div>
        </div>";  	
        
  	}

  	$parameter_page["path"] = get_path_elements($main_element);
  	
  	
  	if ($parameter_page["te_id"] == 6) {
		//           
  	  
		$query = "select e.* from elements e where e.e_id = '$parameter_page[id]' order by e.te_id, e.sort";
		
		$parameter_page["main_text"] .= show_list_photos($query, "alb");
		$parameter_page["menu2"] = "";
    	  		
  	}elseif ($parameter_page["te_id"] == 601) {
		//      
		$parent = get_element($main_element["e_id"]);
		$parameter_page["name"] = "<span><a href=\"".get_url_element($parent)."\">$parent[name]</a> /</span><h1>$parameter_page[name]</h1>";
		$query = "select e.* from elements e where e.e_id = '$main_element[id]' order by e.te_id, e.sort";
		
		$parameter_page["main_text"] .= show_list_photos($query, "photo");
  	}
  	
  	
  	if($parameter_page["id"] == $common_options["feedback_page"]){
			$p_cmd = get_var_web("p_cmd");
		  if($p_cmd){
		    $p_name = trim(get_var_web("p_name"));
		    $p_email = trim(get_var_web("p_email"));
		    $p_message = trim(get_var_web("p_message"));
		    if($p_name&&$p_email){
		      if(check_mail($p_email)){
		  		  $message = "<h3>Письмо с сайта</h3>
		  		               <table>
		  		               <tr><td align=\"right\">Имя: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
		  		               <tr><td align=\"right\">E-mail: </td><td><b>".htmlspecialchars(stripslashes($p_email))."</b></td></tr>
		  		               <tr><td align=\"right\">Сообщение: </td><td><b>".htmlspecialchars(stripslashes($p_message))."</b></td></tr>
		  		               </table> 
		  		             ";
		  		  $feedback_email = get_value_option("feedback_email");
		  	    $status = @my_mail($feedback_email, "Письмо с сайта", $message, $common_options["from_email"]);
		  		  if($status){
		  		  	$parameter_page["title"] = "Ваше сообщение отправлено";
		  		  	//$parameter_page["main_text"] .= show_form("", "", "");
		  		  	$parameter_page["main_text"] .= "<br><br><p><b>Ваше сообщение отправлено.</b></p>";
		  		  }else{
		  		  	$parameter_page["title"] = "Ошибка";
		  		  	$parameter_page["main_text"] .= show_form($p_name, $p_email, $p_message);
		  		  	$parameter_page["main_text"] .= "<p>В данный момент операцию завершить невозможно.<br>Попробуйте через некоторое время.<br>Извините за неудобства.</p>";
		  		  }
		      }else{
		  	  	$parameter_page["title"] = "Ошибка";
	  		  	$parameter_page["main_text"] .= show_form($p_name, $p_email, $p_message);
		  	  	$parameter_page["main_text"] .= "<p>Неправильный e-mail!</p>";
		      }
				}else{
			  	$parameter_page["title"] = "Ошибка";
  		  	$parameter_page["main_text"] .= show_form($p_name, $p_email, $p_message);
			  	$parameter_page["main_text"] .= "<p>Пожалуйста, заполните помеченные * поля</p>";
				}
			}else{
		  	$parameter_page["main_text"] .= show_form("", "", "");
			}
  	}
  	
  	if($parameter_page["id"] == $common_options["order_page"]){
			$p_cmd = get_var_web("p_cmd");
		  if($p_cmd){
		    $p_name = trim(get_var_web("p_name"));
		    $p_email = trim(get_var_web("p_email"));
		    $p_message = trim(get_var_web("p_message"));
		    if($p_name&&$p_email){
		      if(check_mail($p_email)){
		  		  $message = "<h3>Письмо с сайта</h3>
		  		               <table>
		  		               <tr><td align=\"right\">Имя: </td><td><b>".htmlspecialchars(stripslashes($p_name))."</b></td></tr>
		  		               <tr><td align=\"right\">E-mail: </td><td><b>".htmlspecialchars(stripslashes($p_email))."</b></td></tr>
		  		               <tr><td align=\"right\">Сообщение: </td><td><b>".htmlspecialchars(stripslashes($p_message))."</b></td></tr>
		  		               </table> 
		  		             ";
		  		  $feedback_email = get_value_option("feedback_email");
		  	    $status = @my_mail($feedback_email, "Письмо с сайта", $message, $common_options["from_email"]);
		  		  if($status){
		  		  	$parameter_page["title"] = "Ваше сообщение отправлено";
		  		  	//$parameter_page["main_text"] .= show_form("", "", "");
		  		  	$parameter_page["main_text"] .= "<br><br><p><b>Ваше сообщение отправлено.</b></p>";
		  		  }else{
		  		  	$parameter_page["title"] = "Ошибка";
		  		  	$parameter_page["main_text"] .= show_form($p_name, $p_email, $p_message);
		  		  	$parameter_page["main_text"] .= "<p>В данный момент операцию завершить невозможно.<br>Попробуйте через некоторое время.<br>Извините за неудобства.</p>";
		  		  }
		      }else{
		  	  	$parameter_page["title"] = "Ошибка";
	  		  	$parameter_page["main_text"] .= show_form($p_name, $p_email, $p_message);
		  	  	$parameter_page["main_text"] .= "<p>Неправильный e-mail!</p>";
		      }
				}else{
			  	$parameter_page["title"] = "Ошибка";
  		  	$parameter_page["main_text"] .= show_form($p_name, $p_email, $p_message);
			  	$parameter_page["main_text"] .= "<p>Пожалуйста, заполните помеченные * поля</p>";
				}
			}else{
		  	$parameter_page["main_text"] .= show_form("", "", "");
			}
  	}

  	if($parameter_page["id"] == $common_options["basket_page"]){
  	  include_once($_SERVER['DOCUMENT_ROOT']."/functional/basket.php");
  	}
  	
  	return $parameter_page;
  }
  
  
  
  function get_page($parameter_page){
  	global $common_options;
  	if($parameter_page["template"]){
  		$template = file_get_contents($common_options["tpl_folder"].$parameter_page["template"].".tpl");
  	}else{
  		$template = file_get_contents($common_options["tpl_folder"]."main.tpl");
  	}
  	$page = $template;
  	
  	if($parameter_page["error"]=="404"){
  		header("HTTP/1.x 404 Not Found");
  	}else{
  		header("HTTP/1.x 200 OK");
  	}
		$page = str_replace("{@TITLE@}", $parameter_page["title"], $page);
		$page = str_replace("{@NAME@}", $parameter_page["name"], $page);
		$page = str_replace("{@KEYWORDS@}", $parameter_page["keywords"], $page);
		$page = str_replace("{@DESCRIPTION@}", $parameter_page["description"], $page);
		$page = str_replace("{@TOPMENU@}", $parameter_page["topmenu"], $page);
		$page = str_replace("{@HEAD@}", $parameter_page["head"], $page);	
		$page = str_replace("{@INDEXGOODS@}", $parameter_page["index_goods"], $page);
		$page = str_replace("{@LINKS@}", $parameter_page["index_links"], $page);
		$page = str_replace("{@FIRSTNEWS@}", $parameter_page["firstnews"], $page);
		$page = str_replace("{@LEFTMENU@}", $parameter_page["leftmenu"], $page);
		$page = str_replace("{@BANNER@}", $parameter_page["banner"], $page);
		$page = str_replace("{@QUICK@}", $parameter_page["quick"], $page);
		$page = str_replace("{@PIC-BOTTOM@}", nvl($parameter_page["pic-bottom"], "pic-none"), $page);
		$page = str_replace("{@PIC-TOP@}", nvl($parameter_page["pic-top"], "/images/inside1.jpg"), $page);
		$page = str_replace("{@ADDRESS@}", get_value_option("address"), $page);
		$page = str_replace("{@TOWN@}", get_value_option("town"), $page);
		$page = str_replace("{@MAINTEXT@}", $parameter_page["main_text"], $page);
		$page = str_replace("{@PRODUCT_TITLE@}", $parameter_page["product_title"], $page);

		return $page;
  	
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
								<th width=\"26%\" colspan=2>Подарок</th>
								<th width=\"14%\">Цена</th>
								<th width=\"16%\">Количество</th>
								<th width=\"18%\">Стоимость</th>
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
  	  	$micro_pic = get_value_har($row["id"], "icon");
  	  	if($micro_pic){
		  		$file_info = get_file_info($micro_pic);	
	  			$img = "<img src=\"$file_info[link]\" width=\"70\" />";
  	  	}
				
				$output .= "
					<tr>
						<td class='nobordr'>$img</td>
						<td class=\"name\"><a href='$url'>$row[name]</a></td>
						<td>".$price." руб.</td>
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
  	
		$count_goods = mysql_result(mysql_query("select count(*) n from string_baskets sb, elements e where sb.b_id = '$_SESSION[b_id]' and sb.e_id = e.id"), 0);
		$output = "";
		
		$output .= "
      <script>
      var calcPrice = function () {
          $( \"#select-delivery option:selected\" ).each(function() {
             $('#delivery_cost').html('Стоимость доставки: '+$( this ).attr(\"add\")+'р.')
           });
          
       };
      
      
      
      $(document).ready(function () {
         calcPrice();
          
         $('#select-delivery').change(function () {
              calcPrice();
           })
           .change();
          
      });
      </script>		
		
		";
		
		if($count_goods){
  		$output .= "
  		<p><br><b>Чтобы завершить оформление заказа, необходимо заполнить следующие поля:</b></p>
  								<div id=\"form\"><form class=\"order-form\" id=\"order-form\" method=\"post\" action=\"/basket\">";
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
  		if(!$param["delivery"]){
  		  $param["delivery"] = 1;
  		}
		  $delivery_cost= "Стоимость доставки: ".$common_options["delivery_price"][$param["delivery"]]."р.";
  		$output .= "
        <tr><td class=\"name\">Ваше имя* </td></tr><tr><td><input title=\"\" type=\"text\" name=\"firstname\" size=\"50\" value=\"".check_var_web($param["firstname"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">Телефон для связи* </td></tr><tr><td><input title=\"\" type=\"text\" name=\"phone\" size=\"50\"  value=\"".check_var_web($param["phone"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">E-mail </td></tr><tr><td><input title=\"\" type=\"text\" name=\"email\" size=\"50\" value=\"".check_var_web($param["email"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">Город* </td></tr><tr><td>".show_array($common_options["city"], nvl(check_var_web($param["city"]), 1), "select", "city", "onChange='if(this.selectedIndex==1){\$(\"#other_city\").show();}else{\$(\"#other_city\").hide();}'")." <span  $other_city id=\"other_city\">Название города: <input type=\"text\" id=\"oc_input\" name=\"other_city\" size=\"30\" value=\"".check_var_web($param["other_city"])."\" class=\"shortinput\"></span></td></tr>
        <tr><td class=\"name\">Адрес доставки (улица, дом, квартира)* </td></tr><tr><td><input title=\"\" type=\"text\" name=\"address\" size=\"50\"  value=\"".check_var_web($param["address"])."\" class=\"input\"></td></tr>
        <tr><td class=\"name\">Способ доставки </td></tr><tr><td>".show_array($common_options["delivery"], nvl(check_var_web($param["delivery"]), 1), "select", "delivery", " id='select-delivery' ", $common_options["delivery_price"])." <span id=\"delivery_cost\">$delivery_cost</span></td></tr>
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
  function show_array($a, $p, $mode = "select", $name = "", $add = "", $array_add = array()){
  	$return = "";
  	foreach ($a as $k=>$v){
  	  if($mode=="select"){
  	    if(count($array_add)){
  	      $text_add = " add=\"".$array_add[$k]."\" ";
  	    }else{
  	      $text_add = "";
  	    }
      	if(($k == $p)){
      		$return .= "<option value=\"$k\" $text_add selected>$v</option>";
      	}else{
      		$return .= "<option value=\"$k\" $text_add>$v</option>";
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
  
  
?>