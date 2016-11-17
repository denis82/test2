<?php

  
  function  get_name($name, $te_id){
    if($name){
      $return = $name;
    }else{
      if($te_id==401){
        $return = "Без даты";
      }else{
        $return = "Без названия";
      }
    }
    return $return;
  }

  function load_file($p_file, $p_name = "", $p_description = "", $p_sys = "0"){
  	global $common_options;
  	$p_file_id = (int)$p_file_id;
  	echo 123;
  	//print_r($_FILES);
    if (file_exists($p_file["tmp_name"])){
      if(filesize($p_file["tmp_name"])>0){
      	$ext = strtolower(".".trim(substr($p_file["name"], strrpos($p_file["name"], ".")+1)));
      	$p_name = strtolower(translate($p_name));

      	$res = mysql_query("select max(id)+1 max_id from files");
	      $max_id = mysql_result($res, "max_id");
	      if(!$max_id){
	      	$max_id = 1;
	      }

      	if($p_name){
      		$name = $p_name.$ext;
      	}else{
      		$name = $max_id.$ext;
      	}

      	$n = mysql_result(mysql_query("select count(*) n from files where name=\"$name\""), "n");
      	if($n>0){
      		$max_id = -1;
      	}else{
      		$width =0 ; $height = 0;
      		if(strpos($p_file["type"], "image")!==false){
      			$size = getimagesize($p_file["tmp_name"]);
      			$width = (int)$size[0];
      			$height = (int)$size[1];
      		}
		      mysql_query("insert into files (id, type, name, width, height, description, sys)
		                     values ($max_id, \"".$p_file["type"]."\", \"$name\", $width, $height, \"$p_description\", \"$p_sys\")");
		      copy($p_file["tmp_name"], $common_options["upload_folder"].$name);
		    }
      }
    }

    return $max_id;
  }



  function update_file($p_file, $p_file_id){
  	global $common_options;
  	$p_file_id = (int)$p_file_id;
    if (file_exists($p_file["tmp_name"])){
      if(filesize($p_file["tmp_name"])>0){
      	$old_file = mysql_fetch_array(mysql_query("select * from files where id = $p_file_id"));
      	
		    if (file_exists($common_options["upload_folder"].$old_file["name"])){
        	unlink($common_options["upload_folder"].$old_file["name"]);
        }

        $width =0 ; $height = 0;
    		if(strpos($p_file["type"], "image")!==false){
    			$size = getimagesize($p_file["tmp_name"]);
    			$width = (int)$size[0];
    			$height = (int)$size[1];
	    		mysql_query("update files set width=$width, height=$height where id = $p_file_id");
	    		echo mysql_error();
    		}

        copy($p_file["tmp_name"], $common_options["upload_folder"].$old_file["name"]);
      }
    }
  }



  function delete_file($p_file_id){
  	global $common_options;
  	$p_file_id = (int)$p_file_id;
  	$old_file = mysql_fetch_array(mysql_query("select * from files where id = $p_file_id"));

    if ((file_exists($common_options["upload_folder"].$old_file["name"]))&&($old_file["name"])){
    	unlink($common_options["upload_folder"].$old_file["name"]);
    }

    mysql_query("delete from files where id = $p_file_id");

  }
  
  
	function parse_date($date,$date_format)  {
	  $year=$month=$day=false;
	  for($i=0,$x=0;$i<strlen($date_format);$i++){
	    if($date_format{$i}=='%'){
	      $i++;
	      $typ=$date_format{$i};
	      if($typ=='Y'){
	        if(ctype_alnum($date{$x}) && ctype_alnum($date{$x+1}) &&
	           ctype_alnum($date{$x+2}) && ctype_alnum($date{$x+3}))
	        {
	          $year=substr($date,$x,4);
	          $x+=4;
	        } else return false;
	      }
	      else if($typ=='y'){
	        if(ctype_alnum($date{$x}) && ctype_alnum($date{$x+1})){
	          $year=2000+substr($date,$x,2);
	          $x+=2;
	        } else return false;
	      }
	      else if($typ=='m'){
	        if(ctype_alnum($date{$x}) && ctype_alnum($date{$x+1})){
	          $month=substr($date,$x,2);
	          $x+=2;
	        } else return false;
	      }
	      else if($typ=='d'){
	        if(ctype_alnum($date{$x}) && ctype_alnum($date{$x+1})){
	          $day=substr($date,$x,2);
	          $x+=2;
	        } else return false;
	      }
	    }else {
	      if($date{$x}!=$date_format{$i}) return false;
	      $x++;
	    }
	  }
	  if(!checkdate($month,$day,$year)) return false;
	  return mktime(0,0,0,$month,$day,$year);
	}

  
  
	
  function set_value_har1($e_id, $pe_code, $value, $denormaliz = "", $value_denormaliz = ""){
  	if($value){
			if(exists_har($e_id, $pe_code)){
	  		mysql_query("update har_elements set value = \"$value\" 
	  		               where e_id = '$e_id' and pe_code = \"$pe_code\"");
			}else{
	  		mysql_query("insert into har_elements (e_id, pe_code, value)
	  		               values ($e_id, \"$pe_code\", \"$value\")");
			  echo mysql_error();
			}
			if($denormaliz){
				mysql_query("update elements set $denormaliz = '$value_denormaliz' where id = '$e_id'");
			}
  	}else{
  		mysql_query("delete from har_elements 
  		               where e_id = '$e_id' and pe_code = \"$pe_code\"");
			if($denormaliz){
				mysql_query("update elements set $denormaliz = '' where id = '$e_id'");
			}
  	}
		if(!mysql_errno()){
			$return = 1;
		}else{
			$return = 0;  			
		}
  }
  
  function set_value_har_multi($e_id, $pe_code, $value){
  	if($value){
			if(exists_har($e_id, $pe_code)){
	  		mysql_query("update har_elements set value = \"$value\" 
	  		               where e_id = '$e_id' and pe_code = \"$pe_code\"");
			}else{
	  		mysql_query("insert into har_elements (e_id, pe_code, value)
	  		               values ($e_id, \"$pe_code\", \"$value\")");
			}
  	}else{
  		mysql_query("delete from har_elements 
  		               where e_id = '$e_id' and pe_code = \"$pe_code\"");
  	}
		if(!mysql_errno()){
			$return = 1;
		}else{
			$return = 0;  			
		}
  }
  
  
  function load_ext_files($p_e_id){
    $ext_files = $_FILES["prop_ext_file"];
    if(count($ext_files["tmp_name"])){
      $max_n = mysql_result(mysql_query("select max(n) max_n from ext_files where e_id = $p_e_id"), "max_n");
      if ($max_n) {
      	$max_n ++;
      }else {
      	$max_n = 1;
      }
      for ($i=0; $i<count($ext_files["tmp_name"]); $i++){
        $tmp["name"] = $ext_files["name"][$i];
        $tmp["type"] = $ext_files["type"][$i];
        $tmp["tmp_name"] = $ext_files["tmp_name"][$i];
        $tmp["error"] = $ext_files["error"][$i];
        $tmp["size"] = $ext_files["size"][$i];
        $file_id = load_file($tmp);
        if($file_id){
          mysql_query("insert into ext_files (n, e_id, file_id) values ($max_n, $p_e_id, $file_id)");
          echo mysql_error();
        }
        $max_n++;
      }
    }
    $del = $_POST["prop_ext_file_del"];
    for ($i=0; $i<count($del); $i++){
      $ext_file = mysql_fetch_array(mysql_query("select * from ext_files where id = ".$del[$i]));
      delete_file($ext_file["file_id"]);
      $main_text = get_value_har($p_e_id, "main_text");
      $main_text = str_replace("[FILE".$ext_file["n"]."]", "", $main_text);
      $main_text = str_replace("[/FILE".$ext_file["n"]."]", "", $main_text);
      set_value_har($p_e_id, "main_text", $main_text);
      mysql_query("delete from ext_files where id = $ext_file[id]");
    }
  }
  
  function uc2html($str) {
		$ret = '';
		for( $i=0; $i<strlen($str)/2; $i++ ) {
			$charcode = ord($str[$i*2])+256*ord($str[$i*2+1]);
			$ret .= convert($charcode);
		}
		return $ret;
	}
	
	function convert($c){
		$html = array("1072","1073","1074","1075","1076","1077","1105","1078","1079","1080","1081","1082","1083","1084","1085","1086","1087","1088","1089","1090","1091","1092","1093","1094","1095","1096","1097","1100","1099","1098","1101","1102","1103","1040","1041","1042","1043","1044","1045","1025","1046","1047","1048","1049","1050","1051","1052","1053","1054","1055","1056","1057","1058","1059","1060","1061","1062","1063","1064","1065","1068","1067","1066","1069","1070","1071","45","44","46","40","41", "32","48","49","50","51","52","53","54","55","56","57","8470");
		$rus = array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ь","ы","ъ","э","ю","я","А","Б","В","Г","Д","Е","Ё","Ж","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ь","Ы","Ъ","Э","Ю","Я","-",",",".","(",")"," ","0","1","2","3","4","5","6","7","8","9","№");
		
		$key = array_search($c, $html);
		
		if($key===false){
			return '&#'.$c.";";
		}else{
			return $rus[$key];
		}
	}
	
  

  function set_value_har($e_id, $pe_code, $value, $del_file = 0, $multi = 0){
  	global $common_options;
  	
  	$te_id = get_te_id($e_id);
  	$res = mysql_query("select * from property_elements where code = '$pe_code' and te_id = '$te_id'");
  	//echo $te["id"];
  	$pe = mysql_fetch_array($res);
  	
  	if(($pe["multi"])&&(!$multi)){
  		//обработка множественного поля
  		mysql_query("delete from values_har_elements where e_id = '$e_id' and pe_code = '$pe_code'");
  		$count_values = 0;
  		for ($i=0; $i<count($value); $i++){
  			if($value[$i]){
  				$count_values++;
  				$res_sort = mysql_query("select max(sort) s from values_har_elements where pe_code = '$pe_code' and value = '".check_var_web($value[$i])."'");
  				$max_sort = mysql_fetch_array($res_sort);
  				$sort = $max_sort["s"]+10;  				
  				mysql_query("insert into values_har_elements (e_id, pe_code, value, sort) values ('$e_id', '$pe_code', '".check_var_web($value[$i])."', '$sort')");
  			}
  		}
  		if($count_values){
  			set_value_har1($e_id, $pe_code, "multi");
  		}else {
  			set_value_har1($e_id, $pe_code, "");
  		}
  		
  	}else{
  		
	  	if($pe["save"]=="text"){
	  		$v = check_var_web($value);
	  		$return = set_value_har1($e_id, $pe_code, $v);
	
	  	}elseif ($pe["save"]=="int") {
	  		//echo $value;
	  		$v = (int)$value;
	  		//echo $v."<br>";
	  		$return = set_value_har1($e_id, $pe_code, $v, $pe["denormaliz"], $v);
	
	  	}elseif ($pe["save"]=="wisiwig") {
	  		$v = addslashes(stripslashes($value));
	  		$return = set_value_har1($e_id, $pe_code, $v);
	  		
	  	}elseif ($pe["save"]=="date") {
		    $check_date=parse_date($value, '%d.%m.%Y');
		    if($check_date){
		      $date1=date('d.m.Y', $check_date);
		      $date2=date('Y-m-d', $check_date);
		  		$return = set_value_har1($e_id, $pe_code, $date1, $pe["denormaliz"], $date2);
		    }else{
		    	$return = 0;
		    }
	  		
	  	}elseif ($pe["save"]=="change_group") {
	  		//echo $value;
	  		$v = (int)$value;
	  		if($v){
	  		  mysql_query("update elements set e_id = $value where id = $e_id");
	  		}
	
	  	}elseif ($pe["save"]=="file") {
	  		$f = get_value_har($e_id, $pe_code);
	  		print_r($value);
	  		if($del_file){
	  			delete_file($f);
	  			delete_har_element($e_id, $pe_code);
	  			$f = null;
		  		if(substr($pe_code, 0, 12)=="original_pic"){
		  			$index = substr($pe_code, 12, 1);
			  		$f = get_value_har($e_id, "big-pic".$index);
		  			delete_file($f);
		  			delete_har_element($e_id, "big-pic".$index);
			  		$f = get_value_har($e_id, "small-pic".$index);
		  			delete_file($f);
		  			delete_har_element($e_id, "small-pic".$index);
		  		}
	  		}
	  		if($value["name"]){
		  		if($f){
		  			//обновить файл
		  			update_file($value, $f);
		  			$return =1;
		  		}else {
		  			//создать файл
		  			$h = load_file($value, "", "", "1");
			  		$return = set_value_har1($e_id, $pe_code, $h);
		  		}
	  		}

	  		if(substr($pe_code, 0, 12)=="original_pic"){
	  			$index = substr($pe_code, 12, 1);
	  			
	  			if($value["tmp_name"]){
		  			// Get new dimensions
						list($width, $height) = getimagesize($value["tmp_name"]);
            $ext = strtolower(trim(substr($value["name"], strrpos($value["name"], ".")+1)));
						
						$percent1 = 800/$width;
						$percent2 = 800/$width;
						$percent = min($percent1, $percent2);
						

						if($percent>1){
  						set_value_har($e_id, "big-pic".$index, $value);
						}else {
  						$new_width = round($width * $percent);
  						$new_height = round($height * $percent);  						
  						$image_p = imagecreatetruecolor($new_width, $new_height);
//  						echo $ext;
  						if($ext=="gif"){
    						$image = imagecreatefromgif($value["tmp_name"]);
  						}else{
    						$image = imagecreatefromjpeg($value["tmp_name"]);
  						}
  						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  						
  						$tmppath = $common_options["upload_folder"]."temp_pic.jpg";
  						
  						if($ext=="gif"){
    						imagegif($image_p, $tmppath, 80);
  						}else{
    						imagejpeg($image_p, $tmppath, 80);
  						}
  						$big_pic = $value;
  						$big_pic["tmp_name"] = $tmppath;
  						set_value_har($e_id, "big-pic".$index, $big_pic);
  						unlink($tmppath);
						}
	
						$percent1 = 200/$width;
						$percent2 = 200/$width;
						$percent = min($percent1, $percent2);
						$new_width = round($width * $percent);
						$new_height = round($height * $percent);
						$image_p = imagecreatetruecolor($new_width, $new_height);
						if($ext=="gif"){
  						$image = imagecreatefromgif($value["tmp_name"]);
						}else{
  						$image = imagecreatefromjpeg($value["tmp_name"]);
						}
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						
						$tmppath = $common_options["upload_folder"]."temp_pic.jpg";
						
						if($ext=="gif"){
  						imagegif($image_p, $tmppath, 80);
						}else{
  						imagejpeg($image_p, $tmppath, 80);
						}
						$small_pic = $value;
						$small_pic["tmp_name"] = $tmppath;
						set_value_har($e_id, "small-pic".$index, $small_pic);
						unlink($tmppath);
	
	  			}
	  		}
	  		
	  		
	  		if(substr($pe_code, 0, 16)=="alb_original_pic"){
	  			$index = substr($pe_code, 16, 1);
	  			
	  			if($value["tmp_name"]){
		  			// Get new dimensions
						list($width, $height) = getimagesize($value["tmp_name"]);
            $ext = strtolower(trim(substr($value["name"], strrpos($value["name"], ".")+1)));
						
						$percent1 = 200/$width;
						$percent2 = 200/$width;
						$percent = min($percent1, $percent2);
						$new_width = round($width * $percent);
						$new_height = round($height * $percent);
						$image_p = imagecreatetruecolor($new_width, $new_height);
						if($ext=="gif"){
  						$image = imagecreatefromgif($value["tmp_name"]);
						}else{
  						$image = imagecreatefromjpeg($value["tmp_name"]);
						}
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						
						$tmppath = $common_options["upload_folder"]."temp_pic.jpg";
						
						if($ext=="gif"){
  						imagegif($image_p, $tmppath, 80);
						}else{
  						imagejpeg($image_p, $tmppath, 80);
						}
						$small_pic = $value;
						$small_pic["tmp_name"] = $tmppath;
						set_value_har($e_id, "small-pic".$index, $small_pic);
						unlink($tmppath);
	
	  			}
	  		}
	  		
	  		if(($pe_code=="photo")&&($te_id==202)){
	  			
	  			if($value["tmp_name"]){
		  			// Get new dimensions
						list($width, $height) = getimagesize($value["tmp_name"]);
            $ext = strtolower(trim(substr($value["name"], strrpos($value["name"], ".")+1)));
						
						$percent1 = 98/$height;
						$percent2 = 98/$height;
						$percent = min($percent1, $percent2);
						
						$percent = min($percent1, $percent2);
						$new_width = round($width * $percent);
						$new_height = round($height * $percent);
						$image_p = imagecreatetruecolor($new_width, $new_height);
						if($ext=="gif"){
  						$image = imagecreatefromgif($value["tmp_name"]);
						}else{
  						$image = imagecreatefromjpeg($value["tmp_name"]);
						}
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						
						$tmppath = $common_options["upload_folder"]."temp_pic.jpg";
						
						if($ext=="gif"){
  						imagegif($image_p, $tmppath, 80);
						}else{
  						imagejpeg($image_p, $tmppath, 80);
						}
						$small_pic = $value;
						$small_pic["tmp_name"] = $tmppath;
						set_value_har($e_id, "icon", $small_pic);
						unlink($tmppath);
	
	  			}
	  		}
	  		
	  		
	  		
	  	}elseif ($pe["save"]=="grafik") {
	  	  $table= "";
	  		if($value["tmp_name"]){
	  		  require "../include/excelparser.php";
	  			$exc = new ExcelFileParser();
	  			$error_code = $exc->ParseFromFile($value["tmp_name"]);
	  			if($error_code==0){
	  				$ws = $exc->worksheet['data'][0];
	  				$table .= "<table class=\"center\">";
						for( $i=0; $i<=$ws['max_row']; $i++ ) {
							$table .= "<tr>";
							for( $j=0; $j<=$ws['max_col']; $j++ ) {
								$data = $ws['cell'][$i][$j];
							  switch ($data['type']) {
									// string
									case 0:
										$ind = $data['data'];
										if( $exc->sst['unicode'][$ind] ) {
											$s = uc2html($exc->sst['data'][$ind]);
										} else
											$s = $exc->sst['data'][$ind];
										$s = trim($s);
										break;
									// integer number
									case 1:
										$s =  (int)($data['data']);
										break;
									// float number
									case 2:
										$s =  (float)($data['data']);
										break;
									default:
										$s = "";
										break;
							  }
							  
							  if($j<2){
							    $table .= "<td>$s</td>";
							  }else{
							    if($j%2==0){
							      $status = (int)$s;
							    }else{
						        $class = "";
							      if($i>0){
	  						      if($status==1){
	  						        //по запросу
	  						        $class = "class=\"green\"";
	  						      }elseif ($status==2){
	  						        //Свободных номеров мало
	  						        $class = "class=\"yellow\"";
	  						      }elseif ($status==3){
	  						        //STOP SALE - ПРОДАЖА ОСТАНОВЛЕНА
	  						        $class = "class=\"red\"";
	  						      }
							      }
	  						    $table .= "<td $class>$s</td>";
							    }
							  }
							}
							$table .= "</tr>";
						}
	  				$table .= "</table>";
	  			}
	    		$table = str_replace('"', '\"', $table);
	    		$return = set_value_har1($e_id, $pe_code, $table);
	  		}
	
	  	}elseif ($pe["save"]=="table") {
	  	  $table= "";
	  		if($value["tmp_name"]){
	  		  require "../include/excelparser.php";
	  			$exc = new ExcelFileParser();
	  			$error_code = $exc->ParseFromFile($value["tmp_name"]);
	  			if($error_code==0){
	  				$ws = $exc->worksheet['data'][0];
	  				$table .= "<table class=\"center\">";
						for( $i=0; $i<=$ws['max_row']; $i++ ) {
							$table .= "<tr>";
							for( $j=0; $j<=$ws['max_col']; $j++ ) {
								$data = $ws['cell'][$i][$j];
							  switch ($data['type']) {
									// string
									case 0:
										$ind = $data['data'];
										if( $exc->sst['unicode'][$ind] ) {
											$s = uc2html($exc->sst['data'][$ind]);
										} else
											$s = $exc->sst['data'][$ind];
										$s = trim($s);
										break;
									// integer number
									case 1:
										$s =  (int)($data['data']);
										break;
									// float number
									case 2:
										$s =  (float)($data['data']);
										break;
									default:
										$tmp = ExcelDateUtil::getDateArray($data['data']);
										$s = $tmp["day"].".".$tmp["month"].".".$tmp["year"];
										break;
							  }
							  
						    $table .= "<td>$s</td>";
							}
							$table .= "</tr>";
						}
	  				$table .= "</table>";
	  			}
	    		$table = str_replace('"', '\"', $table);
	    		$return = set_value_har1($e_id, $pe_code, $table);
	  		}
	
	
	
	  	}
  	}
  	
  	return  $return;
  }
  
  function delete_har_element($p_e_id, $p_pe_code = ""){
  	if($p_pe_code){
  		$query = "select pe.code, he.value, pe.type from property_elements pe, har_elements he where pe.code = he.pe_code and pe.code= '$p_pe_code' and he.e_id = $p_e_id";
  	}else{
  		$query = "select pe.code, he.value, pe.type  from property_elements pe, har_elements he where pe.code = he.pe_code and he.e_id = $p_e_id";
  	}
  	$res = mysql_query($query);
  	while($prop = mysql_fetch_array($res)){
  		if($prop["type"]=="file"){
  			delete_file($prop["value"]);
  			mysql_query("delete from har_elements where e_id = $p_e_id and pe_code = '$prop[code]' limit 1");
  		}elseif ($prop["type"]=="ext_file"){
  		  $res = mysql_query("select * from ext_files where e_id = ".$p_e_id);
  		  while ($ext_file = mysql_fetch_array($res)) {
          delete_file($ext_file["file_id"]);
          mysql_query("delete from ext_files where id = $ext_file[id]");
  		  }
  		}else{
  			mysql_query("delete from har_elements where e_id = $p_e_id and pe_code = '$prop[code]' limit 1");
  		}
			mysql_query("delete from values_har_elements where e_id = $p_e_id and pe_code = '$prop[code]'");
  	}
  	
  }

  function create_input($p_type, $p_name, $p_value = "", $p_e_id = ""){
  	global  $admin_options, $common_options;
  	
  	if($p_type == "short_text"){
  		return "<input class=\"text\" type=\"text\" size=\"20\" name=\"$p_name\" value=\"".$p_value."\">";
  	
  	}elseif($p_type == "text"){
  		return "<input class=\"text\" type=\"text\" size=\"40\" name=\"$p_name\" value=\"".$p_value."\">";
  	
  	}elseif($p_type == "date"){
  		return "<input class=\"text\" type=\"text\" size=\"10\" name=\"$p_name\" value=\"".$p_value."\"> <span class=\"note\">(в формате ДД.ММ.ГГГГ)</span>";
  	
  	}elseif($p_type == "hidden"){
  		return "<input type=\"hidden\" name=\"$p_name\" value=\"".$p_value."\">";
  	
  	}elseif($p_type == "textarea"){
  		if($p_name=="prop_anons"){
    		$return = "<textarea cols=\"50\" class=\"textarea\"  maxlength=\"100\" rows=\"5\" name=\"$p_name\" onkeyup=\"JavaScript:document.getElementById('count_symb').innerHTML=(100 - this.value.length);\">".htmlspecialchars($p_value)."</textarea><br>Осталось символов: <span id=\"count_symb\">100</span>";
  		}elseif ($p_name == "prop_text2"){
    		$return = "<textarea cols=\"50\" class=\"textarea\" rows=\"5\" name=\"$p_name\"  onClick=\"JavaScript: active_object = this;\">".htmlspecialchars($p_value)."</textarea>";
  		}else{
    		$return = "<textarea cols=\"50\" class=\"textarea\" rows=\"5\" name=\"$p_name\">".htmlspecialchars($p_value)."</textarea>";
  		}
  		
  		return $return;
  	
  	}elseif($p_type == "long-textarea"){
  	  if($p_name == "prop_text1" || $p_name == "prop_text2"){
    		$return = "<textarea cols=\"60\" class=\"textarea\" rows=\"15\" name=\"$p_name\" onClick=\"JavaScript: active_object = this;\">".$p_value."</textarea>";
  	  }else{
    		$return = "<textarea cols=\"60\" class=\"textarea\" rows=\"15\" name=\"$p_name\">".$p_value."</textarea>";
  	  }
  		return $return;
  	
  	}elseif($p_type == "file"){  		
  		$return .= "<input type=\"file\" class=\"text\" name=\"$p_name\">";
  		if(($p_name!="prop_grafik")&&($p_name!="prop_table")){
    		if($p_value){
    			$file_info = get_file_info($p_value);
    			$return .= "<br><span class=\"note\">Текущий файл: <a href=\"".$file_info["link"]."\" target=\"_blank\">".$file_info["name"]."</a> ".floor($file_info["size"]/1024)." Kb&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"".$p_name."_del\" value=\"$file_info[id]\"> удалить</span>";
    		}
  		}
  		return $return;
  	
  	}elseif($p_type == "ext_file"){  
  	  $current_number = 0;		
  		$return .= "
  		  <fieldset><legend><a href=\"#\" onClick=\"JavaScript:add_ext_file(); return false;\">Добавить</a></legend>
  		  <div id='ext_files'>
  		  ";
  		if($p_e_id){
    	  $res = mysql_query("select * from ext_files where e_id = $p_e_id order by n");
    	  while ($row = mysql_fetch_array($res)) {
    	    $current_number = $row["n"];
    			$file_info = get_file_info($row["file_id"]);
    			$return .= "<div><a href=\"".$file_info["link"]."\" target=\"_blank\">".$file_info["name"]."</a> ".floor($file_info["size"]/1024)." Kb&nbsp;&nbsp;&nbsp;<label><input type=\"checkbox\" name=\"".$p_name."_del[]\" value=\"$row[id]\"> удалить</label></div>";
    	  }
  		}
  		$current_number++;
  		$return .= "
		    <div><input type=\"file\" class=\"text\" name=\"".$p_name."[]\" id=\"ext_file_$current_number\"></div>
		  ";
  		$return .= "</div></fieldset>
        <script language=\"JavaScript\">
    		  current_number = $current_number;
			  </script>  		
  		";

  		return $return;
  	
  	}elseif($p_type == "grafik"){  		
  		$return .= "<input type=\"file\" class=\"text\" name=\"$p_name\">";
  		return $return;
  	
  	}elseif($p_type == "yes_no"){
  		$yes_no = array(1 => "Да", 0 => "Нет");
  		$return = "<select name=\"$p_name\">";
  		$p_v = (int)$p_value;
  		if($p_e_id){
    		$p_v = (int)$p_value;
  		}else{
  		  $p_v = 1;
  		}
  		foreach ($yes_no as $k=>$v){
	    	if($k === $p_v){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "no_yes"){
  		$yes_no = array("0" => "Нет", "1" => "Да");
  		$return = "<select name=\"$p_name\">";
  		foreach ($yes_no as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "menu"){
  		$yes_no = array("0" => "Нет", "1" => "В верхнем", "2" => "В левом");
  		$return = "<select name=\"$p_name\">";
  		foreach ($yes_no as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "status"){
  		$return = "<select name=\"$p_name\">";
  		foreach ($common_options["status"] as $k=>$v){
	    	if(($k == $p_value)&&($p_value!="")){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "order_status"){
  		$return = "<select name=\"$p_name\">";
  		foreach ($common_options["order_status"] as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "komu"){
  		$return = "<select name=\"$p_name\">";
  		$return .= "<option value=\"\">--Не выбрано--</option>";
  		foreach ($common_options["komu"] as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "povod"){
  		$return = "<select name=\"$p_name\">";
  		$return .= "<option value=\"\">--Не выбрано--</option>";
  		foreach ($common_options["povod"] as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "position_pic"){
  		$position = array("left" => "Слева", "top" => "Сверху");
  		$return = "<select name=\"$p_name\">";
  		foreach ($position as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "wisiwig"){
	    $return = "<script language=\"javascript\" src=\"".$admin_options["url"]."fckeditor/fckeditor.js\"></script>
  							 <script type=\"text/javascript\">
								   var oFCKeditor = new FCKeditor('$p_name') ;
								   oFCKeditor.Value	= '".str_replace("'", "\'", str_replace("\r\n", "", $p_value))."' ;
								   oFCKeditor.Create() ;
								 </script>";
	    return $return;

  	}elseif($p_type == "functional"){
  		$f = $common_options["functional"];
  		$return = "<select name=\"$p_name\">";
  		foreach ($f as $k=>$v){
	    	if($k == $p_value){
	    		$return .= "<option value=\"$k\" selected>$v</option>";
	    	}else{
	    		$return .= "<option value=\"$k\">$v</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "list_pages"){
  		$return = "<select name=\"$p_name\">";
  		$root = get_main_element($common_options["content_te_id"]);
  		$return .= "<option value=\"0\">-- Не выбрано --</option>";
  		$res = mysql_query("select * from elements where e_id = '$root' and sys<>1 order by sort");
  		while ($row = mysql_fetch_array($res)) {
	    	if($row["id"] == $p_value){
	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
	    	}else{
	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
	    	}
    		$res_sub = mysql_query("select * from elements where e_id = '$row[id]' and sys<>1 order by sort");
    		while ($row_sub = mysql_fetch_array($res_sub)) {
  	    	if($row_sub["id"] == $p_value){
  	    		$return .= "<option value=\"$row_sub[id]\" selected>&nbsp;&nbsp;&nbsp;$row_sub[name]</option>";
  	    	}else{
  	    		$return .= "<option value=\"$row_sub[id]\">&nbsp;&nbsp;&nbsp;$row_sub[name]</option>";
  	    	}
    		}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "list_catalog"){
  		$return = "<select name=\"$p_name\">";
  		$return .= "<option value=\"0\">-- Не выбрано --</option>";
  		$res = mysql_query("select * from elements e where (e.te_id = 202 or e.te_id=10) order by e.te_id, e.sort, e.e_id, binary(e.name)");
  		while ($row = mysql_fetch_array($res)) {
  		  if($row["te_id"]==10){
  		   if(get_value_har($row["id"], "is_collect")) {
          if($row["id"] == $p_value){
          	$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
          }else{
          	$return .= "<option value=\"$row[id]\">$row[name]</option>";
          }
  		   }
  		  }else{
  	    	if($row["id"] == $p_value){
  	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
  	    	}else{
  	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
  	    	}
  		  }
  		}
	    $return .= "</select>";
	    return $return;
	    
  	}elseif($p_type == "albom"){
  		$return = "<select name=\"$p_name\">";
  		$return .= "<option value=\"0\">-- Не выбрано --</option>";
  		$res = mysql_query("select * from elements e where e.te_id = 601 order by e.sort, e.e_id, binary(e.name)");
  		while ($row = mysql_fetch_array($res)) {
	    	if($row["id"] == $p_value){
	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
	    	}else{
	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "rubricator"){
  		$return = "<select name=\"$p_name\">";
  		$res = mysql_query("select * from rubricator order by name");
  		$return .= "<option value=\"\">--Не выбрано--</option>";
  		while ($row = mysql_fetch_array($res)) {
	    	if($row["id"] == $p_value){
	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
	    	}else{
	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "category"){
  		$return = "<select name=\"$p_name\">";
  		$res = mysql_query("select * from category order by name");
  		$return .= "<option value=\"\">--Не выбрано--</option>";
  		while ($row = mysql_fetch_array($res)) {
	    	if($row["id"] == $p_value){
	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
	    	}else{
	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "vid_menu"){
  		$return = "<select name=\"$p_name\">";
  		$res = mysql_query("select * from menu_rest where mr_id is null order by name");
  		while ($row = mysql_fetch_array($res)) {
	    	if($row["id"] == $p_value){
	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
	    	}else{
	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	}elseif($p_type == "size"){
  		$return = "<select name=\"$p_name\">";
  		$res = mysql_query("select * from sizes order by name");
  		$return .= "<option value=\"\">--Не выбрано--</option>";
  		while ($row = mysql_fetch_array($res)) {
	    	if($row["id"] == $p_value){
	    		$return .= "<option value=\"$row[id]\" selected>$row[name]</option>";
	    	}else{
	    		$return .= "<option value=\"$row[id]\">$row[name]</option>";
	    	}
  		}
	    $return .= "</select>";
	    return $return;

  	
  	}
  }

  function path_elements($p_id, $p_last = 0) {
  	$e = get_element($p_id);
  	if($p_last){
  		$output .= $e["name"];
  	}else{
  		$output .= "[<a href='index.php?p_e_id=$e[id]'>$e[name]</a>] :: ";
  	}
  	if($e["e_id"]){
  		$output = path_elements($e["e_id"]).$output;
  	}
  	return $output;
  }
  
  function get_path($p_id, $p_last = 1, $p_last_text = ""){
  	if($p_id){
	  	$output .= path_elements($p_id, $p_last);
  	}
  	return $output.$p_last_text;
  }

  /*
    p_format: [O] - order, [T] - tasks, [D] - date
  */
  function show_tree_elements ($p_id){
  	global $admin_options;
  	$output = "";
  	
  	if($p_id){

  		$path = get_path($p_id);
  		$output .= "<div class='top-path'>".$path."</div>";

  		$te_id = get_te_id($p_id);
  		
  		$type_element = get_type_element($te_id);

			if($type_element["show_parent"] == "1"){
	      $parent["id"] = $p_id;
	      $parent["te_id"] = get_te_id($p_id);
	      $parent["cmd"] = "edit";
	
	      $output .= edit_element($parent, 0, 1);
			}
	
  		
  		
  		$query = "select * from elements e where e.e_id = \"$p_id\" and sys=0 order by sort, binary(name)";

  		$res = mysql_query("select te.* from type_elements te, hierarchy_types ht 
	  	                      where ht.te_id1 = '$te_id' and ht.te_id2 = te.id and te.new=1");
	  	if(mysql_num_rows($res)){
	  		while($new = mysql_fetch_array($res)){
		  		$output .= "<a href=\"index.php?p_cmd=new&p_e_id=$p_id&p_te_id=$new[id]\" style=\"margin-right: 30px;\">$new[new_text]</a>";
	  		}
	  	}
  	
	  	$res = mysql_query($query);
	  	if(mysql_num_rows($res)){
	  		$output .= "<table class='elements' cellspacing='0' cellpadding='0'>";
	  		//$output .= "<tr><th>&nbsp;</th><th>Наименование</th><th colspan='2'>Задачи</th><th colspan='2'>&nbsp;</th></tr>";
		  	while($e = mysql_fetch_array($res)){
		  		$output .= "<tr>";
		
					$type = mysql_fetch_array(mysql_query("select * from type_elements te where te.id = $e[te_id]"));
					
		  		$res_h = mysql_query("select te.* from type_elements te, hierarchy_types ht 
			  	                      where ht.te_id1 = '$type[id]' and ht.te_id2 = te.id");
			  	if(mysql_num_rows($res_h)){
						$icon = "<td class=\"icon\"><a href=\"index.php?p_e_id=$e[id]\" STYLE=\"text-decoration:none;\">+</a></td>";
			  	}else{
						$icon = "<td>&nbsp;</td>";
			  	}
			  	
			  	if(!$e["name"]){
			  	  $e["name"] = "Без названия";
			  	}
					
					if($type["edit"]){
			  		$name = "<a href=\"index.php?p_cmd=edit&p_id=$e[id]\">$e[name]</a>";
					}else{
						$output .= "<td>&nbsp;</td>";
					}
					$output .= $icon."<td class=\"name r\">$name</td>";
					$output .= "<td class=\"link l\"><a href=\"index.php?p_cmd=up&p_id=$e[id]\"><img src=\"/admin/images/up.gif\" alt=\"вверх\" title=\"вверх\"></a></td>";
					$output .= "<td class=\"link r\"><a href=\"index.php?p_cmd=down&p_id=$e[id]\"><img src=\"/admin/images/down.gif\" alt=\"вниз\" title=\"вниз\"></a></td>";
					$output .= "</tr>";
		  		
		  	}
				$output .= "</table>";
	  	}else {
	  		$output .= "<br><br><p><b>На данном уровне список пуст.</b><br><br><span class=\"note\">Для добавления используйте вышестоящие ссылки.</span>";
	  	}
  		$output .= "<div class='bottom-path'>".$path."</div>";
  	}
		return $output;
  }
  
  function yes_no($p){
    if($p){
      return "Да";
    }else{
      return "Нет";
    }
  }
  
  function show_tree_catalog ($p_id){
  	global $admin_options;
  	$output = "";
  	
  	if($p_id){

  		$path = get_path($p_id);
  		$output .= "<div class='top-path'>".$path."</div>";

  		$te_id = get_te_id($p_id);
  		
  		$type_element = get_type_element($te_id);

			if($type_element["show_parent"] == "1"){
	      $parent["id"] = $p_id;
	      $parent["te_id"] = get_te_id($p_id);
	      $parent["cmd"] = "edit";
	
	      $output .= edit_element($parent, 0, 1);
			}
	
  		
  		
  		$query = "select * from elements e where e.e_id = \"$p_id\" and sys=0 order by sort, binary(name)";

  		$res = mysql_query("select te.* from type_elements te, hierarchy_types ht 
	  	                      where ht.te_id1 = '$te_id' and ht.te_id2 = te.id and te.new=1");
	  	if(mysql_num_rows($res)){
	  		while($new = mysql_fetch_array($res)){
		  		$output .= "<a href=\"index.php?p_cmd=new&p_e_id=$p_id&p_te_id=$new[id]\" style=\"margin-right: 30px;\">$new[new_text]</a>";
	  		}
	  	}
  	
	  	$res = mysql_query($query);
	  	if(mysql_num_rows($res)){
	  		$output .= "<table class='elements' cellspacing='0' cellpadding='0'>";
	  		//$output .= "<tr><th>&nbsp;</th><th>Наименование</th><th colspan='2'>Задачи</th><th colspan='2'>&nbsp;</th></tr>";
		  	while($e = mysql_fetch_array($res)){
		  		$output .= "<tr>";
		
					$type = mysql_fetch_array(mysql_query("select * from type_elements te where te.id = $e[te_id]"));
					
		  		$res_h = mysql_query("select te.* from type_elements te, hierarchy_types ht 
			  	                      where ht.te_id1 = '$type[id]' and ht.te_id2 = te.id");
			  	if(mysql_num_rows($res_h)){
						$icon = "<td class=\"icon\"><a href=\"index.php?p_e_id=$e[id]\" STYLE=\"text-decoration:none;\">+</a></td>";
			  	}else{
						$icon = "<td>&nbsp;</td>";
			  	}
			  	
			  	if(!$e["name"]){
			  	  $e["name"] = "Без названия";
			  	}
					
					if($type["edit"]){
			  		$name = "<a href=\"index.php?p_cmd=edit&p_id=$e[id]\">$e[name]</a>";
					}else{
						$output .= "<td>&nbsp;</td>";
					}
					$output .= $icon."<td class=\"name r\">$name (Активен: ".yes_no(get_value_har($e["id"], "is_access")).", артикул: ".get_value_har($e["id"], "articul").", цена: ".get_value_har($e["id"], "price").")</td>";
					$output .= "<td class=\"link l\"><a href=\"index.php?p_cmd=up&p_id=$e[id]\"><img src=\"/admin/images/up.gif\" alt=\"вверх\" title=\"вверх\"></a></td>";
					$output .= "<td class=\"link r\"><a href=\"index.php?p_cmd=down&p_id=$e[id]\"><img src=\"/admin/images/down.gif\" alt=\"вниз\" title=\"вниз\"></a></td>";
					$output .= "</tr>";
		  		
		  	}
				$output .= "</table>";
	  	}else {
	  		$output .= "<br><br><p><b>На данном уровне список пуст.</b><br><br><span class=\"note\">Для добавления используйте вышестоящие ссылки.</span>";
	  	}
  		$output .= "<div class='bottom-path'>".$path."</div>";
  	}
		return $output;
  }
  
  function show_tree_news ($p_id){
  	global $admin_options;
  	$output = "";
  	
  	if($p_id){

  		$path = get_path($p_id);
  		$output .= "<div class='top-path'>".$path."</div>";

  		$te_id = get_te_id($p_id);
  		
  		$type_element = get_type_element($te_id);

			if($type_element["show_parent"] == "1"){
	      $parent["id"] = $p_id;
	      $parent["te_id"] = get_te_id($p_id);
	      $parent["cmd"] = "edit";
	
	      $output .= edit_element($parent, 0, 1);
			}
	
  		
  		
    	$query = "select date_format(e.date, '%d.%m.%Y') date, e.id, e.name, e.te_id from elements e where e.e_id = \"$p_id\" order by e.date DESC";

  		$res = mysql_query("select te.* from type_elements te, hierarchy_types ht 
	  	                      where ht.te_id1 = '$te_id' and ht.te_id2 = te.id and te.new=1");
	  	if(mysql_num_rows($res)){
	  		while($new = mysql_fetch_array($res)){
		  		$output .= "<a href=\"index.php?p_cmd=new&p_e_id=$p_id&p_te_id=$new[id]\" style=\"margin-right: 30px;\">$new[new_text]</a>";
	  		}
	  	}
  	
	  	$res = mysql_query($query);
	  	if(mysql_num_rows($res)){
	  		$output .= "<table class='elements' cellspacing='0' cellpadding='0'>";
		  	while($e = mysql_fetch_array($res)){
		  		$output .= "<tr>";
		
					$type = mysql_fetch_array(mysql_query("select * from type_elements te where te.id = $e[te_id]"));
					
					if($type["edit"]){
			  		$name = "<a href=\"index.php?p_cmd=edit&p_id=$e[id]\">$e[name]</a>";
					}else{
						$output .= "<td>&nbsp;</td>";
					}
			  	if(!$e["date"]){
			  	  $e["date"] = "Без даты";
			  	}
					
					$output .= "<td class=\"name r\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]\">$e[date]</a></td><td class=\"name r\">$e[name]</td>";
					$output .= "</tr>";
		  		
		  	}
				$output .= "</table>";
	  	}else {
	  		$output .= "<p><b>Список пуст.</b><br><br><span class=\"note\">Для добавления используйте вышестоящие ссылки.</span>";
	  	}
  		$output .= "<div class='bottom-path'>".$path."</div>";
  	}
		return $output;
  }
  
  function show_tree_actions ($p_id){
  	global $admin_options, $common_options;
  	$output = "";
  	
  	if($p_id){

  		$path = get_path($p_id);
  		$output .= "<div class='top-path'>".$path."</div>";

  		$te_id = get_te_id($p_id);
  		
  		$type_element = get_type_element($te_id);

			if($type_element["show_parent"] == "1"){
	      $parent["id"] = $p_id;
	      $parent["te_id"] = get_te_id($p_id);
	      $parent["cmd"] = "edit";
	
	      $output .= edit_element($parent, 0, 1);
			}
	
  		
  		
    	$query = "select date_format(e.date, '%d.%m.%Y') date, e.id, e.name, e.te_id, e.status from elements e where e.e_id = \"$p_id\" order by e.date DESC";

  		$res = mysql_query("select te.* from type_elements te, hierarchy_types ht 
	  	                      where ht.te_id1 = '$te_id' and ht.te_id2 = te.id and te.new=1");
	  	if(mysql_num_rows($res)){
	  		while($new = mysql_fetch_array($res)){
		  		$output .= "<a href=\"index.php?p_cmd=new&p_e_id=$p_id&p_te_id=$new[id]\" style=\"margin-right: 30px;\">$new[new_text]</a>";
	  		}
	  	}
  	
	  	$res = mysql_query($query);
	  	if(mysql_num_rows($res)){
	  		$output .= "<table class='elements' cellspacing='0' cellpadding='0'>";
		  	while($e = mysql_fetch_array($res)){
		  		$output .= "<tr>";
		
					$type = mysql_fetch_array(mysql_query("select * from type_elements te where te.id = $e[te_id]"));
					
					$output .= "
					  <td class=\"name r\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]\">".nvl($e["name"], "без названия")."</a></td>
					  <td class=\"name r\">".$common_options["status"][$e["status"]]."</td>
					";
					$output .= "</tr>";
		  		
		  	}
				$output .= "</table>";
	  	}else {
	  		$output .= "<p><b>Список акций пуст.</b><br><br><span class=\"note\">Для добавления новости используйте вышестоящие ссылки.</span>";
	  	}
  		$output .= "<div class='bottom-path'>".$path."</div>";
  	}
		return $output;
  }
  

  function show_tree_events ($p_id){
  	global $admin_options, $common_options;
  	$output = "";
  	
  	if($p_id){

  		$path = get_path($p_id);
  		$output .= "<div class='top-path'>".$path."</div>";

  		$te_id = get_te_id($p_id);
  		
  		$type_element = get_type_element($te_id);

			if($type_element["show_parent"] == "1"){
	      $parent["id"] = $p_id;
	      $parent["te_id"] = get_te_id($p_id);
	      $parent["cmd"] = "edit";
	
	      $output .= edit_element($parent, 0, 1);
			}
	
  		
  		
    	$query = "select date_format(e.date, '%d.%m.%Y') date, e.id, e.name, e.te_id, e.status from elements e where e.e_id = \"$p_id\" order by e.date DESC";

  		$res = mysql_query("select te.* from type_elements te, hierarchy_types ht 
	  	                      where ht.te_id1 = '$te_id' and ht.te_id2 = te.id and te.new=1");
	  	if(mysql_num_rows($res)){
	  		while($new = mysql_fetch_array($res)){
		  		$output .= "<a href=\"index.php?p_cmd=new&p_e_id=$p_id&p_te_id=$new[id]\" style=\"margin-right: 30px;\">$new[new_text]</a>";
	  		}
	  	}
  	
	  	$res = mysql_query($query);
	  	if(mysql_num_rows($res)){
	  		$output .= "<table class='elements' cellspacing='0' cellpadding='0'>";
		  	while($e = mysql_fetch_array($res)){
		  		$output .= "<tr>";
		
					$type = mysql_fetch_array(mysql_query("select * from type_elements te where te.id = $e[te_id]"));
					
					$output .= "
					  <td class=\"name r\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]\">".nvl($e["name"], "без названия")."</a></td>
					  <td class=\"name r\">".$common_options["status"][$e["status"]]."</td>
					";
					$output .= "</tr>";
		  		
		  	}
				$output .= "</table>";
	  	}else {
	  		$output .= "<p><b>Список пуст.</b><br><br><span class=\"note\">Для добавления используйте вышестоящие ссылки.</span>";
	  	}
  		$output .= "<div class='bottom-path'>".$path."</div>";
  	}
		return $output;
  }
  

  function edit_element($edit = array(), $p_show_name_eng = 1, $p_show_parent = 0) {
    global $common_options;
  	$output = "";
  	
  	
  	$te = get_type_element($edit["te_id"]);

  	if(!$p_show_parent){
	  	if($edit["cmd"]=="new"){
		  	$path = get_path($edit["e_id"], 0, $te["new_text"]);
	  	}else {
		  	$path .= get_path($edit["id"], 1, " :: ".$te["edit_text"]);
	  	}
	  	
			$output .= "<div class='top-path'>".$path."</div>";
  	}
		
  	$output .= "<script language=\"JavaScript\">
  	  var show = false;
  	  var active_object = '';
      function getPos(el,sProp) {
          var iPos = 0;
          while (el!=null) {
              iPos+=el[\"offset\" + sProp]
              el = el.offsetParent
          }
          return iPos
      }
  	  
  function d(p, a){
    if(show){
      document.getElementById(p).style.display='none';
      show = false;
      a.innerHTML = 'Показать дополнительные параметры';
    }else{
      document.getElementById(p).style.display='';
      show = true;
      a.innerHTML = 'Скрыть дополнительные параметры';
    }
    return false;
  }

		
			function add_ext_file(){
			  current_number++;
			  var div = document.createElement('div');
			  div.innerHTML = '<input type=\"file\" class=\"text\" name=\"prop_ext_file[]\"  id=\"ext_file_'+current_number+'\">';
        document.getElementById('ext_files').appendChild(div);
			}
	  
			  
			  </script>";
  	$output .= "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" name=\"f\">";
  	$output .= "<table>";
  	if($edit["cmd"]=="new"){
  		$e = array();
	  	$output .= create_input("hidden", "p_e_id", $edit["e_id"]);
	  	$output .= create_input("hidden", "p_te_id", $edit["te_id"]);
	  	$output .= create_input("hidden", "p_cmd", "insert");
	  	$output .= create_input("hidden", "p_save_parent", $p_show_parent);
			$buttons = "<input type=\"button\" value=\"Отмена\" class=\"button\" onClick=\"JavaScript:location.href='".$_SERVER["self"]."?p_cmd=tree&p_e_id=$edit[e_id]';\"> <input type=\"submit\" value=\"Сохранить\" class=\"button\">";
  	}else{
	  	$e = get_element($edit["id"]);
	  	$output .= create_input("hidden", "p_id", $edit["id"]);
	  	$output .= create_input("hidden", "p_cmd", "update");
	  	$output .= create_input("hidden", "p_save_parent", $p_show_parent);
	  	if(!$p_show_parent){
				$buttons = "<input type=\"button\" value=\"Отмена\" class=\"button\" onClick=\"JavaScript:location.href='".$_SERVER["self"]."?p_cmd=tree&p_e_id=$e[e_id]';\"> <input type=\"submit\" value=\"Сохранить\" class=\"button\"> <input type=\"button\" value=\"Удалить\" class=\"button\" style=\"margin-left: 100px;\" onClick=\"JavaScript:del('index.php?p_cmd=delete&p_id=$e[id]');\">";
	  	}else{
				$buttons = "<input type=\"submit\" value=\"Сохранить\" class=\"button\">";
	  	}
  	}
  	if(!$p_show_parent){
	  	$output .= "<tr><td class='name_prop'>Название</td><td>".create_input("text", "name", str_replace("\"", "&quot;", $e["name"]))."</td>";
  	}else{
	  	$output .= create_input("hidden", "name", str_replace("\"", "&quot;", $e["name"]));
  	}
  	if($te["eng"]){
	  	$output .= "<tr><td class='name_prop'>Название (англ)</td><td>".create_input("text", "name_eng", $e["name_eng"])." <span class='note'>(не обязательное поле)</span></td>";
  	}else{
	  	$output .= create_input("hidden", "name_eng", $e["name_eng"]);
  	}
  	
  	if(!$p_show_parent){
	  	$res1 = mysql_query("select distinct pg.id, pg.name from property_elements pe, property_groups pg where pe.te_id = '$te[id]' and pe.pg_id = pg.id and pe.show=1 order by pg.sort");
  	}else{
	  	$res1 = mysql_query("select distinct pg.id, pg.name from property_elements pe, property_groups pg where pe.te_id = '$te[id]' and pe.pg_id = pg.id and pe.show=1 and pe.show_parent=1 order by pg.sort");
  	}

  	
  	
  	if(mysql_num_rows($res1)){
  		while($pg = mysql_fetch_array($res1)){
	  		if($pg["id"]==6){
	  		  if($edit["show_add"]){
	  		    $style = " id='add_param' ";
	  		  }else{
	  		    $style = " style='display:none;' id='add_param' ";
	  		  }
	  		  $output .= "<tr height=40><td colspan='2'><br><a href='#' onClick=\"JavaScript:d('add_param', this);return false;\">Показать дополнительные параметры</td></tr>";
	  		}else{
	  		  $style = "";
	  		}
	  		$output .= "<tr $style><td colspan='2'><table><tr><td>";
	  		$output .= "<fieldset><legend>&nbsp;$pg[name]&nbsp;</legend>";
	  		$output .= "<table>";
		  	if(!$p_show_parent){
			  	$res = mysql_query("select * from property_elements where te_id = '$te[id]' and pg_id = $pg[id] and `show`=1 order by sort");
		  	}else{
			  	$res = mysql_query("select * from property_elements where te_id = '$te[id]' and pg_id = $pg[id] and `show`=1 and show_parent=1 order by sort");
		  	}
		  	while($prop = mysql_fetch_array($res)){
		  		if($prop["type"]=="wisiwig"){
		  			if($prop["name"]){
		  				$output .= "<tr><td class='name_prop' colspan='2' style='text-align:left'>$prop[name]</td></tr>";
		  			}
			  		$output .= "<tr><td colspan='2'>";
		  		}else{
			  		$output .= "<tr><td class='name_prop'>$prop[name]</td><td>";
		  		}
		  		if($prop["multi"]){
		  			$res_multi = mysql_query("select * from values_har_elements vhe where vhe.pe_code = '$prop[code]' and vhe.e_id = '$e[id]' order by vhe.value");
			  		$output .= "<fieldset><legend>&nbsp;<a href=\"/#\" onClick=\"add_field('$prop[code]', '$prop[type]');return false;\">Добавить</a>&nbsp;</legend>";
			  		$output .= "<div id=\"".$prop["code"]."\">";
	  				$output  .= create_input($prop["type"], "prop_".$prop["code"]."[]", "", $edit["id"])."<br>";
		  			while ($row_multi = mysql_fetch_array($res_multi)) {
		  				$output  .= "<div>".create_input($prop["type"], "prop_".$prop["code"]."[]", $row_multi["value"], $edit["id"])."</div>";
		  			}
			  		$output .= "</div>";
			  		$output .= "</fieldset>";
		  		}else{
			  		$output .= create_input($prop["type"], "prop_$prop[code]", get_value_har($e["id"], $prop["code"]), $edit["id"]);
		  		}
		  		$output .= "</td></tr>";
		  	}
		  	$output .= "</table>";
		  	$output .= "</fieldset>";
		  	$output .= "</td></tr></table>";
		  	
	  		if(($pg["id"]==6)&&(get_value_har($e["id"], "functional")=="photos")){
	  		  //выведем фотки, прикрепленные к титулу галереи
          if($edit["cmd2"]=="up_photo2"){
          	$res_photo = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show-titul' and he.value='1' and e.id = '$edit[photo_id]' order by e.sort2");
          	$current_photo = mysql_fetch_array($res_photo);
          	$res_photo = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show-titul' and he.value='1' and sort2<'$current_photo[sort2]' order by sort2 desc limit 1");
          	$up_photo = mysql_fetch_array($res_photo);
          	if($up_photo["id"]){
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$up_photo[sort2]' 
          	    where id = '$current_photo[id]'
          	  ");
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$current_photo[sort2]' 
          	    where id = '$up_photo[id]'
          	  ");
          	}
      
          }elseif($edit["cmd2"]=="down_photo2"){
          	$res_photo = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show-titul' and he.value='1' and e.id = '$edit[photo_id]' order by e.sort2");
          	$current_photo = mysql_fetch_array($res_photo);
          	$res_photo = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show-titul' and he.value='1' and sort2>'$current_photo[sort2]' order by sort2 asc limit 1");
          	$down_photo = mysql_fetch_array($res_photo);
          	
          	if($down_photo["id"]){
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$down_photo[sort2]' 
          	    where id = '$current_photo[id]'
          	  ");
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$current_photo[sort2]' 
          	    where id = '$down_photo[id]'
          	  ");
          	}
          }
          
        	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'show-titul' and he.value='1' order by e.sort2");
        	if(mysql_num_rows($res)){
        	  $output .= "<table><tr><td id=\"ext_pics\"><fieldset><legend>&nbsp;Фотографии, отображаемые на титуле галереи&nbsp;</legend><table> ";
        	  $number_photo = 0;
          	while ($row = mysql_fetch_array($res)) {
          	  $number_photo ++ ;
          	  $photo = $row;
          	  $alb = get_element($photo["e_id"]);
          	  $output .= "<tr><td><a href=\"/admin/photos/index.php?p_cmd=edit&p_id=$photo[id]\" target=\"_blank\">".get_name($alb["name"], $alb["te_id"])." / ".get_name($photo["name"], $photo["te_id"])."</a></td>";
          	  if($number_photo==1){
      					$output .= "<td class=\"link l\"></td>";
          	  }else{
      					$output .= "<td class=\"link l\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]&p_cmd2=up_photo2&p_photo_id=$photo[id]&p_show_add=1\"><img src=\"/admin/images/up.gif\" alt=\"вверх\" title=\"вверх\"></a></td>";
          	  }
          	  if($number_photo==mysql_num_rows($res)){
      					$output .= "<td class=\"link r\"></td>";
          	  }else{
      					$output .= "<td class=\"link r\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]&p_cmd2=down_photo2&p_photo_id=$photo[id]&p_show_add=1\"><img src=\"/admin/images/down.gif\" alt=\"вниз\" title=\"вниз\"></a></td>";
          	  }
    					$output .= "</tr>";
          	}
          	$output .= "</table></fieldset></td></tr></table>";
        	}
	  		  
	  		}
		  	
	  		if($pg["id"]==6){
	  		  //проверим фотки у страниц
          if($edit["cmd2"]=="up_photo"){
          	$res_photo = mysql_query("select * from values_har_elements where pe_code='show_pages' and e_id = '$edit[photo_id]' and value = '$e[id]' limit 1");
          	$current_photo = mysql_fetch_array($res_photo);
          	$res_photo = mysql_query("select * from values_har_elements where pe_code='show_pages' and value = '$e[id]' and sort<'$current_photo[sort]' order by sort desc limit 1");
          	$up_photo = mysql_fetch_array($res_photo);
          	if($up_photo["e_id"]){
          	  mysql_query("
          	    update values_har_elements 
          	      set sort = '$up_photo[sort]' 
          	    where pe_code='show_pages' 
          	      and value = '$current_photo[value]' 
          	      and e_id = '$current_photo[e_id]'
          	  ");
          	  mysql_query("
          	    update values_har_elements 
          	      set sort = '$current_photo[sort]' 
          	    where pe_code='show_pages' 
          	      and value = '$up_photo[value]' 
          	      and e_id = '$up_photo[e_id]'
          	  ");
          	}
      
          }elseif($edit["cmd2"]=="down_photo"){
          	$res_photo = mysql_query("select * from values_har_elements where pe_code='show_pages' and e_id = '$edit[photo_id]' and value = '$e[id]' limit 1");
          	$current_photo = mysql_fetch_array($res_photo);
          	$res_photo = mysql_query("select * from values_har_elements where pe_code='show_pages' and value = '$e[id]' and sort>'$current_photo[sort]' order by sort asc limit 1");
          	$down_photo = mysql_fetch_array($res_photo);

          	if($down_photo["e_id"]){
          	  mysql_query("
          	    update values_har_elements 
          	      set sort = '$down_photo[sort]' 
          	    where pe_code='show_pages' 
          	      and value = '$current_photo[value]' 
          	      and e_id = '$current_photo[e_id]'
          	  ");
          	  mysql_query("
          	    update values_har_elements 
          	      set sort = '$current_photo[sort]' 
          	    where pe_code='show_pages' 
          	      and value = '$down_photo[value]' 
          	      and e_id = '$down_photo[e_id]'
          	  ");
          	}
          }
          
        	$res = mysql_query("select * from values_har_elements where pe_code='show_pages' and value = '$e[id]' order by sort");
        	if(mysql_num_rows($res)){
        	  $output .= "<table><tr><td id=\"ext_pics\"><fieldset><legend>&nbsp;Прикрепленные фотографии&nbsp;</legend><table> ";
        	  $number_photo = 0;
          	while ($row = mysql_fetch_array($res)) {
          	  $number_photo ++ ;
          	  $photo = get_element($row["e_id"]);
          	  $alb = get_element($photo["e_id"]);
          	  $output .= "<tr><td><a href=\"/admin/photos/index.php?p_cmd=edit&p_id=$photo[id]\" target=\"_blank\">".get_name($alb["name"], $alb["te_id"])." / ".get_name($photo["name"], $photo["te_id"])."</a></td>";
          	  if($number_photo==1){
      					$output .= "<td class=\"link l\"></td>";
          	  }else{
      					$output .= "<td class=\"link l\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]&p_cmd2=up_photo&p_photo_id=$photo[id]&p_show_add=1\"><img src=\"/admin/images/up.gif\" alt=\"вверх\" title=\"вверх\"></a></td>";
          	  }
          	  if($number_photo==mysql_num_rows($res)){
      					$output .= "<td class=\"link r\"></td>";
          	  }else{
      					$output .= "<td class=\"link r\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]&p_cmd2=down_photo&p_photo_id=$photo[id]&p_show_add=1\"><img src=\"/admin/images/down.gif\" alt=\"вниз\" title=\"вниз\"></a></td>";
          	  }
    					$output .= "</tr>";
          	}
          	$output .= "</table></fieldset></td></tr></table>";
        	}
	  		  
	  		}
	  		
	  		if((($pg["id"]==6)&&($e["id"]==$common_options["index"]))||(($pg["id"]==6)&&($e["te_id"]==201))){
  		    $where = "";
	  		  if($e["id"]==$common_options["index"]){
	  		    $pe_code = "show_titul";
	  		  }else{
	  		    $pe_code = "show_grouptitul";
            $res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = 'is_access' and he.value='1' and e.te_id = 202 and e.e_id='$e[id]'");
          	while($group = mysql_fetch_array($res)){
          	  $list[] = $group["id"];
          	}
        
          	if(count($list)){
  	  		    $where .= " and e.e_id in (".implode(",", $list).") ";
          	}else{
          	  $where .= " and 1=0 ";
          	}
	  		    
	  		  }
	  		     
	  		  //проверим товары прикрепленные к группе
          if($edit["cmd2"]=="up_sort2"){
          	$res_sort = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = '$pe_code' and he.value='1' and e.id = '$edit[sort_id]' $where order by e.sort2");
          	$current_sort = mysql_fetch_array($res_sort);
          	$res_sort = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = '$pe_code' and he.value='1' and sort2<'$current_sort[sort2]' $where order by sort2 desc limit 1");
          	$up_sort = mysql_fetch_array($res_sort);
          	if($up_sort["id"]){
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$up_sort[sort2]' 
          	    where id = '$current_sort[id]'
          	  ");
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$current_sort[sort2]' 
          	    where id = '$up_sort[id]'
          	  ");
          	}
      
          }elseif($edit["cmd2"]=="down_sort2"){
          	$res_sort = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = '$pe_code' and he.value='1' and e.id = '$edit[sort_id]' $where order by e.sort2");
          	$current_sort = mysql_fetch_array($res_sort);
          	$res_sort = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = '$pe_code' and he.value='1' and sort2>'$current_sort[sort2]' $where order by sort2 asc limit 1");
          	$down_sort = mysql_fetch_array($res_sort);
          	
          	if($down_sort["id"]){
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$down_sort[sort2]' 
          	    where id = '$current_sort[id]'
          	  ");
          	  mysql_query("
          	    update elements 
          	      set sort2 = '$current_sort[sort2]' 
          	    where id = '$down_sort[id]'
          	  ");
          	}
          }
          
        	$res = mysql_query("select e.* from elements e, har_elements he where e.id = he.e_id and he.pe_code = '$pe_code' and he.value='1' $where order by e.sort2");
        	if(mysql_num_rows($res)){
        	  $output .= "<table><tr><td id=\"ext_pics\"><fieldset><legend>&nbsp;Товары, прикрепленные к этой странице&nbsp;</legend><table> ";
        	  $number_sort = 0;
          	while ($row = mysql_fetch_array($res)) {
          	  $number_sort ++ ;
          	  $sort = $row;
          	  $alb = get_element($sort["e_id"]);
          	  $output .= "<tr><td><a href=\"/admin/catalog/index.php?p_cmd=edit&p_id=$sort[id]\" target=\"_blank\">".get_name($alb["name"], $alb["te_id"])." / ".get_name($sort["name"], $sort["te_id"])."</a></td>";
          	  if($number_sort==1){
      					$output .= "<td class=\"link l\"></td>";
          	  }else{
      					$output .= "<td class=\"link l\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]&p_cmd2=up_sort2&sort_id=$sort[id]&p_show_add=1\"><img src=\"/admin/images/up.gif\" alt=\"вверх\" title=\"вверх\"></a></td>";
          	  }
          	  if($number_sort==mysql_num_rows($res)){
      					$output .= "<td class=\"link r\"></td>";
          	  }else{
      					$output .= "<td class=\"link r\"><a href=\"index.php?p_cmd=edit&p_id=$e[id]&p_cmd2=down_sort2&sort_id=$sort[id]&p_show_add=1\"><img src=\"/admin/images/down.gif\" alt=\"вниз\" title=\"вниз\"></a></td>";
          	  }
    					$output .= "</tr>";
          	}
          	$output .= "</table></fieldset></td></tr></table>";
        	}
	  		  
	  		}
	  		
	  		
	  		
		  	$output .= "</td></tr>";
		  }
  		
  	}
  	
  	$output .= "<tr height='20'><td colspan=2></td></tr>";
  	$output .= "<tr><td colspan=2>$buttons</td></tr>";
  	$output .= "</table>";
  	$output .= "</form>";

  	if(!$p_show_parent){
			$output .= "<div class='bottom-path'>".$path."</div>";
  	}else{
  		$output .= "<br/>";
  	}
  	
  	return $output;
  }
  
  function save_element($save){
  	$exists_name_eng = 1;
  	$name = check_var_web($save["name"]);
  	$name_eng = check_var_web(translate($save["name_eng"]));
  	if(!$name_eng){
  		$name_eng = translate($name);
	  	$exists_name_eng = 0;
  	}
  	if($save["type_save"]=="insert"){
  		$sort = mysql_fetch_array(mysql_query("select max(sort) n from elements where e_id='$save[e_id]'"));
  		if($sort["n"]){
  			$next_sort = $sort["n"]+1;
  		}else{ 
  			$next_sort = 1;
  		}
  		$query = "insert into elements (name, name_eng, e_id, te_id, sort) values ('$name', '$name_eng', ".nvl($save["e_id"], "null").", $save[te_id], $next_sort)";
  	}else{
  		$query = "update elements set name = '$name', name_eng = '$name_eng' where id = '$save[id]'";
  	}
  	//echo $query;
  	mysql_query($query);
  	if(!mysql_errno()){
  		//уникальная страница с таким имененм
	  	if($save["type_save"]=="insert"){
	  		$save["id"] = mysql_insert_id();
      	mysql_query("update elements set sort2 = '$save[id]' where id = '$save[id]'");
	  	}  		
  		//mysql_query("delete from har_elements where e_id = '$save[id]'");
  		if($save["p_save_parent"]=="1"){
		  	$res = mysql_query("select * from property_elements where te_id = '$save[te_id]' and `show` = 1 and show_parent=1");
  		}else{
		  	$res = mysql_query("select * from property_elements where te_id = '$save[te_id]' and `show` = 1");
  		}
	  	while($prop = mysql_fetch_array($res)){
	  		if($prop["type"]=="file"){
	  		  print_r($_FILES);
	  			set_value_har($save["id"], $prop["code"], $save["files"]["prop_".$prop["code"]], $save["prop_".$prop["code"]."_del"]);
	  		}elseif ($prop["type"]=="ext_file"){
	  			load_ext_files($save["id"]);
	  		}else{
	  			set_value_har($save["id"], $prop["code"], $save["prop_".$prop["code"]]);
	  		}
	  	}
	  	if(!$exists_name_eng){
	  		mysql_query("update elements set name_eng = '$save[id]' where id = '$save[id]'");
	  	}
	  	$return = 1;
  	}else{
  		$return = -1;
  	}
  	return $return;
  }


  
  
  
?>