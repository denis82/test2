<?php
  
  function show_list_news($query){
  	global $common_options;
  	$res = mysql_query($query);
  	while($item = mysql_fetch_array($res)){
			$i++;
			$url = get_url_element($item);
			$output .= "
				<p>".(intval($item["day"]).".".$item["month"].".".$item["year"])."<br><a href=\"$url\">".get_value_har($item["id"], "anons")."</a></p><br>
			";
  	}
		return $output;
  }
  
  	$parameter_page["te_id"] = 5;
  
  if(count($p_urls)==1){
  	
  	//запроен первый уровень
  	if($_GET["archive"]==1){
  		$parameter_page["name"] = "Архив новостей";
    	$query = "select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id=501  order by e.date desc";
  	}else{
  		$parameter_page["name"] = "Новости";
    	$query = "select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id=501  order by e.date desc limit 5";
  	}
//  	$res = mysql_query($query);
//  	$i=0;
//  	$j=0;
//  	while(($item = mysql_fetch_array($res))){
//			$url = get_url_element($item);
//			$j++;
//			if($item["date"]){
//  			$output2 .= (intval($item["day"])." ".$common_options["month_name"][$item["month"]]." ".$item["year"]);
//			}
//			$output2 .= "
//				<p><a href=\"$url\">".$item["name"]."</a></p><br>
//			";
//  	}
		$parameter_page["main_text"] .= show_list_news($query);
  
  }else{
  	$parameter_page["view_news"] = 1;
		$name = get_page_name($p_urls[1]);
		$news = get_main_element($common_options["events_te_id"]);
  	$query = "select e.*, date_format(e.date, '%d') day, date_format(e.date, '%m') month, date_format(e.date, '%Y') year 
  	         from elements e, har_elements he 
  	         where e.e_id = '$news' and e.name_eng = '$name' and e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id=501  order by e.date desc";
		$res = mysql_query($query);
  	$item = mysql_fetch_array($res);
  	$parameter_page["id"] = $item["id"];
//  	$pic = get_value_har($item["id"], "pic");
//  	if($pic){
//  		$file_info = get_file_info($pic);
//  		$pic_html = "<img src=\"".$file_info["link"]."\" align=\"left\" class=\"news_img\">";
//  	}else{
//  		$pic_html = "";
//  	}
		$parameter_page["name"] = $item["name"];
  	$parameter_page["main_text"] .= "<p>";
		if($item["date"]){
    	$parameter_page["main_text"] .= (intval($item["day"])." ".$common_options["month_name"][$item["month"]]." ".$item["year"])."<br><br>";
		}
  	$parameter_page["main_text"] .= get_value_har($item["id"], "main_text")."</p>";

  }



?>