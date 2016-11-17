<?php


  function  search($query) {
  
    
    if(strlen($query)>2){
    	$i = 0;
    	$output .= "<p>Искали: $query</p><br>";
    	$query_sql = "
    	  select distinct e.id, e.name 
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
  	  		$output .= "<p><a href=\"$url\">".str_replace($query, "<b>".$query."</b>", $item["name"])."</a><br>";
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
          $text = str_replace($query, "<b>".$query."</b>", $text);
          if($text){
            $output .= $text."<br><br></p>";
          }else{
            $output .= "</p>";
            
          }
  	  	
  	  	}
  	  }
  	  if($i == 0){
  	  	$output .= "<p>Ничего не найдено!</p>";
  	  }
    }else{
    	$output .= "<p>Длина поискового запроса должна превышать 2 символа!</p>";
    }
    
    return $output;
    	
  }

  
  $query = get_var_web("p_query", "web");
  $parameter_page["main_text"] .= "
		<div class=\"search-box\">
			<form  action=\"/search\" name=\"SearchForm\" method=\"post\"> 
			<div class=\"frame\"><input class=\"text\" name=\"p_query\" type=\"text\" value=\"Поиск\"  onblur=\"if(this.value=='') this.value='Поиск';\" onfocus=\"if(this.value=='Поиск') this.value='';\" /></div>
			<input type=\"submit\" value=\"\" class=\"submit\" />
			</form>
		</div><br>
		
		
		
  ";
  if($query){
    $parameter_page["main_text"] .= search($query);
  }
  
  
		
?>