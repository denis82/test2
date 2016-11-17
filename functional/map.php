<?php

  function get_level($p_element, $p_level = 0){
  	global  $common_options;
  	$output = "";
		$query = "select e.* from elements e, har_elements he 
	            where e.e_id = '$p_element[id]' and he.e_id = e.id and he.value = '1' and he.pe_code = 'is_access'
	              and e.te_id in (100, 101, 102, 103, 201, 202)
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



  $root = get_element(get_main_element($common_options["content_te_id"]));
  
  $output .= "
    <style>
      .map li {list-style-type:none; margin-bottom:7px;}
      .map_in li {margin-top:7px;}
      .map_in li {list-style-type:none; margin-bottom:7px;}
    </style>";
  
  
  $output .= get_level($root);
  
  //echo $output;
  
  $parameter_page["main_text"] .= $output;
  
?>