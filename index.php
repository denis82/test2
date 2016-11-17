<?

  require "admin/include/session.php";
  require "admin/include/config.php";
  require "admin/include/connect_db.php";
  require "admin/include/common.php";
  require "admin/include/common_site.php";
  
  $common_options["basket_url"] = get_url_element(get_functional_element("basket"));

	function start_functional($p_functional, $p_urls, $p_content_element, $parameter_page = array()){
		//запускает функциональные страницы
		global $common_options;
		
	  require $common_options["func_folder"].$p_functional.".php";

		return $parameter_page;
	}

	
	function process_request($p_urls) {
		global $common_options;
		
		$parameter_page = array();
		$parent_element = array();
		$is_functional = 0;
		$error = "";
		
		for($number_elem_url = 0; ($number_elem_url < count($p_urls))&&(!$is_functional)&&(!$error); $number_elem_url++){
			$elem_url = get_page_name($p_urls[$number_elem_url]);
			if(!$parent_element["id"]){
				$content = get_main_element($common_options["content_te_id"]);
				$query = "select e.* from elements e, har_elements he 
				            where e.e_id = '$content' and e.name_eng = '$elem_url' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'";
			}else{
				$query = "select e.* from elements e, har_elements he 
				            where e.e_id = '$parent_element[id]' and e.name_eng = '$elem_url' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'";
			}
			$res = mysql_query($query);
			if(mysql_num_rows($res)){
				//что то нашли с таким именем
				$parent_element = mysql_fetch_array($res);
//				$functional = get_value_har($parent_element["id"], "functional");
//				if(($functional)&&($functional!="text")){
//					//проверим нет ли в функциональном блоке текстового раздела
//					$elem_url_tmp = get_page_name($p_urls[$number_elem_url+1]);
//					if($elem_url_tmp){
//						if(!mysql_num_rows(mysql_query("select e.* from elements e, har_elements he where e.e_id = '$parent_element[id]' and e.name_eng = '$elem_url_tmp' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'"))){
//							$is_functional = 1;
//						}
//					}else{
//						$is_functional = 1;
//					}
//					if($is_functional){
//						//запрошена функциональная страница, поиск по контенту прекращаем и уходим на новую ветку
//						$parameter_page = get_main_parameter_page($parent_element);
//						$parameter_page = start_functional($functional, array_slice($p_urls, $number_elem_url), $parent_element, $parameter_page);					
//					}
//				}elseif ($number_elem_url==(count($p_urls)-1)){
//					//проверим есть ли для данного элемента, элемент определенный по умолчанию
//					$default = get_value_har($parent_element["id"], "default_page");				
//					if($default){
//						$tmp = get_element($default);
//						if($tmp["id"]){
//							//если есть - добавим элемент в очередь на проверку
//							$p_urls[] = $tmp["name_eng"];
//						}else{
//							//если такого элемента нет - установим ошибку
//							$parameter_page["error"] = "404";
//							//echo $tmp["name_eng"];
//						}
//					}
//				}
			}else{
				//запрашиваемого элемента на этом уровне нет
				$parameter_page["error"] = "404";
			}
		}
		
		if($parameter_page["error"]){
			$parameter_page = process_error($error);
		}else{
			if(!$is_functional){
				//обработка обычной страницы
				$parameter_page = get_main_parameter_page($parent_element);
			}
		}

		$parameter_page = get_add_parameter_page($parameter_page);		
		
		$return_page = get_page($parameter_page);
		
		return $return_page;
	}
	
  	
  	
  $url = trim(strtolower($_GET["url"]));
  if(!$url){
  	$url = "index.htm";
  }
  //echo $url;
  if(substr($url, strlen($url)-1)=="/"){
  	$url = substr($url, 0, strlen($url)-1).".htm";
  }
  
  $about_tovar = "about_tovar";
  $razdely = "razdely";
  
  if(substr($url, 0, strlen($about_tovar)) == $about_tovar){
    //товар
    $url = get_page_name($url);
    $tovar_id = (int)substr($url, strlen($about_tovar)  );
		$query = "select e.* from elements e, har_elements he 
		            where e.te_id = 203 and e.old_id = '$tovar_id' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'";
    $res = mysql_query($query);
    if(mysql_num_rows($res)){
      $tovar = mysql_fetch_array($res);
      $url = get_url_element_old($tovar);
    }
  }

  if(substr($url, 0, strlen($razdely)) == $razdely){
    //групап
    $url = get_page_name($url);
    $t = strpos($url, "-", strlen($razdely)+1);
    if($t){
      $len = $t -strlen($razdely) - 1;
    }else{
      $len = strlen($url) - strlen($razdely) - 1;
    }
    $razdel_id = (int)substr($url, strlen($razdely)+1, $len);
		$query = "select e.* from elements e, har_elements he 
		            where e.te_id in (201, 202) and e.old_id = '$razdel_id' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'";
    $res = mysql_query($query);
    if(mysql_num_rows($res)){
      $razdel = mysql_fetch_array($res);
      $url = get_url_element_old($razdel);
    }
  }
  
  $urls = explode("/", $url);
  
  $page = process_request($urls);

  echo $page;
  //echo 1;
  
?>