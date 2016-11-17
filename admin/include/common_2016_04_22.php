<?php

  function get_tree_elements($p_e_id, &$p_parent_elements){
  	global $common_options;
  	$element = get_element($p_e_id);
  	if($element["te_id"] == $common_options["content_te_id"]){
  		//return $p_parent_elements;
  		null;
  	}else{
  		if($element["e_id"]){
				$p_parent_elements[] = $element;
  			get_tree_elements($element["e_id"], $p_parent_elements);
  		}else{
  			$type = get_type_element($element["te_id"]);
  			if($type["functional"]){
  				$func_elem = get_functional_element($type["functional"]);
  			//echo "<!-- ".$func_elem["id"]."-->";
  				get_tree_elements($func_elem["id"], $p_parent_elements);
  			}
  		}
  	}
  }
  

  function get_url_element($p_element, $p_ext = 0){
  	global $common_options;
  	$ext_link = get_value_har($p_element["id"], "ext_link");
  	if($ext_link){
  	  $url = $ext_link;
  	}else{
    	$tree_elements = array();
    	get_tree_elements($p_element["id"], $tree_elements);
    	$url = "";
    	for($i=0; $i<count($tree_elements); $i++){
    		if($i==0){
    			$url = $tree_elements[$i]["name_eng"].$url;
    		}else{
    			$url = $tree_elements[$i]["name_eng"]."/".$url;
    		}
    	}
  
    	
    	$url = $common_options["site_url"].$url;
    	
  	 	if($p_ext){
  	 		$url .= ".html";  	
  	 	}
  	}
	 	
	 	if($url == $common_options["site_url"]."index"){
	 	  $url = $common_options["site_url"];
	 	}
	 	
	 	return $url;
  }

  function get_url_element_old($p_element, $p_ext = 1){
  	global $common_options, $about_tovar, $razdely;
  	$tree_elements = array();
  	get_tree_elements($p_element["id"], $tree_elements);
  	$url = "";
  	for($i=0; $i<count($tree_elements); $i++){
  		if($i==0){
  			$url = $tree_elements[$i]["name_eng"].$url;
  		}else{
  			$url = $tree_elements[$i]["name_eng"]."/".$url;
  		}
  	}
  	
  	$url = $common_options["site_url"].$url;
  	
	 	if($p_ext){
//	 		return $url.".htm";  	
	 		return $url;  	
	 	}else{
	 		return $url;  	
	 	}
  }

  function get_functional_element($p_functional){
  	$res = mysql_query("select * from har_elements where pe_code='functional' and value = '$p_functional' limit 1");
  	$h = mysql_fetch_array($res);
  	return get_element($h["e_id"]);
  }

  
  function get_file_info($p_file_id){
  	global $common_options;
  	$p_file_id = (int)$p_file_id;
  	$file = mysql_fetch_array(mysql_query("select * from files where id = $p_file_id"));
  	$return = array();
  	$return = $file;
  	$return["size"] = @filesize($common_options["upload_folder"].$file["name"]);
  	$return["sizeK"] = (int)($return["size"]/1024)."K";
  	$return["link"] = $common_options["site_files_url"].$file["name"];
  	$return["path"] = $common_options["upload_folder"].$file["name"];
  	
  	return $return;
  	
  }



  function get_main_element($p_te_id){
  	$e = mysql_fetch_array(mysql_query("select * from elements where te_id='$p_te_id'"));
  	return $e["id"];
  }
  
  function get_var_web($p_name, $p_type = null){
    if(isset($_GET["$p_name"])){
      $return = $_GET["$p_name"];
    }elseif(isset($_POST["$p_name"])){
      $return = $_POST["$p_name"];
    }elseif(isset($_FILES["$p_name"])){
      $return = $_FILES["$p_name"];
    }
    
    switch ($p_type) {
    	case "int":
    		$return = (int)$return;
    		break;
    	case "translate":
    		$return = translate($return);
    		break;
    	case "web":
    		$return = htmlspecialchars(trim($return));
    		break;
    }

    return $return;
  }


  function check_var_web($p_var){
  	$var = $p_var;
  	$var = trim($var);
  	$var = stripslashes($p_var);
  	$var = htmlspecialchars($var);
  	return $var;
  }
  
  function nvl($p1, $p2){
  	if($p1){
  		return $p1;
  	}else{
  		return $p2;
  	}
  }
  
  function translate($p_text) {
  	/*выполнить транслитерацию текста*/
  	$output = $p_text;
	  $search = array ("'А'","'Б'","'В'","'Г'","'Д'","'Е'","'Ё'","'Ж'","'З'",

	          "'И'","'Й'","'К'","'Л'","'М'","'Н'","'О'","'П'","'Р'",

	          "'С'","'Т'","'У'","'Ф'","'Х'","'Ц'","'Ч'","'Ш'","'Щ'",

	          "'Ъ'","'Ы'","'Ь'","'Э'","'Ю'","'Я'","'а'","'б'","'в'",

	          "'г'","'д'","'е'","'ё'","'ж'","'з'","'и'","'й'","'к'",

	          "'л'","'м'","'н'","'о'","'п'","'р'","'с'","'т'","'у'",

	          "'ф'","'х'","'ц'","'ч'","'ш'","'щ'","'ъ'","'ы'","'ь'",

	          "'э'","'ю'","'я'","' '","','");
	  $replace = array ("a","b","v","g","d","e","e","zh","z",

	          "i","j","k","l","m","n","o","p","r",

	          "s","t","u","f","h","c","ch","sh","sc",

	          "","y","","e","u","ya","a","b","v",

	          "g","d","e","e","j","z","i","i","k",

	          "l","m","n","o","p","r","s","t","u",

	          "f","h","c","ch","sh","sc","","y","",

	          "e","u","ya","-","_");
	  $output = preg_replace($search, $replace, $output);
	  $output = eregi_replace("[^a-z0-9-]","",strtolower($output));
	  return $output;
  }

  function get_value_har_ext($e_id, $pe_code){
  	$res = mysql_query("select * from har_elements where e_id = '$e_id' and pe_code = '$pe_code'");
  	$h = mysql_fetch_array($res);
  		$h["value"] = str_replace("\n", "<br>", $h["value"]);
  	return $h["value"];
  }

  function get_value_har($e_id, $pe_code){
  	$res = mysql_query("select * from har_elements where e_id = '$e_id' and pe_code = '$pe_code'");
  	$h = mysql_fetch_array($res);
  	return $h["value"];
  }
  
  function get_values_har($e_id, $pe_code){
  	$res = mysql_query("select * from values_har_elements where e_id = '$e_id' and pe_code = '$pe_code'");
  	while ($row = mysql_fetch_array($res)) {
  		$h[] = $row;
  	}
  	return $h;
  }
  
//  function get_values_har($e_id, $pe_code){
//  	$res = mysql_query("select * from har_elements_values where e_id = '$e_id' and pe_code = '$pe_code'");
//  	while ($row = mysql_fetch_array($res)) {
//	  	$v = mysql_fetch_array($res);
//  		$values[] = $v["value"];
//  	}
//  	
//  	return $h["value"];
//  }
  
  function exists_har($e_id, $pe_code){
  	if(mysql_num_rows(mysql_query("select e_id from har_elements where e_id='$e_id' and pe_code = '$pe_code'"))){
  		return 1;
  	}else{
  		return 0;
  	}
  }

  function get_te_id($p_e_id){
		$type = mysql_fetch_array(mysql_query("select * from elements e where e.id = '$p_e_id'"));
		return $type["te_id"];
  }
  
  function get_type_element($p_te_id){
		$type = mysql_fetch_array(mysql_query("select * from type_elements te where te.id = '$p_te_id'"));
		return $type;
  }
  
  function get_element($p_e_id){
		$e = mysql_fetch_array(mysql_query("select * from elements e where e.id = '$p_e_id'"));
		return $e;
  }
  
  function get_content_file($p_path){
  	$content = "";
  	if(file_exists($p_path)){
  		$f = fopen($p_path, "r");
  		$content = fread($f, filesize($p_path));
  		fclose($f);
  	}
  	return $content;
  }
  
  function get_page_name($p_name){
  	//убирает расширение и переводит все символы в англицкие
		$name = explode(".", $p_name);
		return translate($name[0]);
  	
  }

  function get_value_option($p_code){
    $result=mysql_query("select * from options where code=\"$p_code\"");
    if(mysql_num_rows($result)>0){
      $fetch=mysql_fetch_array($result);
      return $fetch["value"];
    }else{
      return "";
    }
  }

  
  function add_koi($p_str){
  	return '=?koi8-r?B?'.base64_encode(convert_cyr_string($p_str, "w", "k")).'?=';
  }

	function my_mail($to, $subject, $message, $from){
    $headers .= "From: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html;\r\n charset=\"koi8-r\" \r\nContent-Transfer-Encoding: 8bit";
    $message = "<html><body>$message</body></html>";
    return @mail($to, add_koi($subject), convert_cyr_string($message, "w", "k"), $headers);
  }
  
  
  function check_mail($address){
   	if(ereg( "^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$address) ) {
   	  return 1;
   	}else {
   	  return 0;
   	}   	
  }
 
  
  
  
?>