<?
  $title_page = "Управление каталогом товаров";
  require "../include/header.php";

  $global_te_id = 2;
  
  
  $count_cmd = 1;
  $p_cmd = get_var_web("p_cmd");
  if(!$p_cmd){
  	$p_cmd = "tree";
  }

  for($number_cmd = 0; $number_cmd < $count_cmd; $number_cmd++){
    if($p_cmd=="new"){
      $new["te_id"] = (int)$_GET["p_te_id"];
      $new["e_id"] = (int)$_GET["p_e_id"];
      $new["cmd"] = "new";
      
    	echo edit_element($new);

    }elseif ($p_cmd=="edit"){
      $new["id"] = (int)$_GET["p_id"];
      $new["te_id"] = get_te_id($new["id"]);
      $new["cmd"] = "edit";
      $new["cmd2"] = $_GET["p_cmd2"];;
      $new["photo_id"] = (int)$_GET["p_photo_id"];;
      $new["sort_id"] = (int)$_GET["sort_id"];;
      $new["show_add"] = (int)$_GET["p_show_add"];;

      echo edit_element($new);

    }elseif($p_cmd=="insert"){
    	$save = $_POST;
    	$save["files"] = $_FILES;
    	$save["type_save"] = "insert";
    	$save["te_id"] = (int)$_POST["p_te_id"];
    	$save["e_id"] = (int)$_POST["p_e_id"];
    	switch (save_element($save)){
    		case -1:
		    	echo "<div class=\"error\">Имя на латинском должно быть уникально в пределах группы!</div>
		    	      <div>[<a href=\"Javascript:history.back();\" title=\"Обратно к вводу данных\">Назад</a>]</div>";
    	  break;
    	  case 1:
    	  	$count_cmd = 2;
    	  	$p_cmd = "tree";
	      	$e_id = $save["e_id"];
    	  break;
    	}

    }elseif($p_cmd=="update"){
    	$save = $_POST;
    	$save["files"] = $_FILES;
    	$save["id"] = (int)$_POST["p_id"];
    	$save["type_save"] = "update";
    	$save["te_id"] = get_te_id($save["id"]);
    	switch (save_element($save)){
    		case -1:
		    	echo "<div class=\"error\">Имя на латинском должно быть уникально в пределах группы!</div>
		    	      <div>[<a href=\"Javascript:history.back();\" title=\"Обратно к вводу данных\">Назад</a>]</div>";
    	  break;
    		case 1:
    	  	$count_cmd = 2;
    	  	$p_cmd = "tree";
	      	$parent_el = get_element($save["id"]);
	      	$e_id = $parent_el["e_id"];
    	  break;
    	  	
    	}

    }elseif($p_cmd=="tree"){
    	if(!$e_id){
    		$e_id = get_var_web("p_e_id", "int");
    	}
    	if($e_id){
	      echo show_tree_catalog($e_id);
    	}else {
    		$e_id = get_main_element($common_options["catalog_te_id"]);
	      echo show_tree_catalog($e_id);
    	}


    }elseif($p_cmd=="up"){
      $id = (int)get_var_web("p_id");
      $e = get_element($id);
      if($e["sort"]>1){
      	mysql_query("update elements set sort=sort+1 where sort=$e[sort]-1 and e_id = '$e[e_id]'");
      	mysql_query("update elements set sort=sort-1 where id=$e[id] ");
      }
    	$parent_el = get_element($id);
    	echo show_tree_catalog($parent_el["e_id"]);

    }elseif($p_cmd=="down"){
      $id = (int)get_var_web("p_id");
      $e = get_element($id);
  		$sort = mysql_fetch_array(mysql_query("select max(sort) n from elements where e_id='$e[e_id]'"));
  		if($sort["n"]){
  			$max_sort = $sort["n"];
  		}else{ 
  			$max_sort = 1;
  		}
      if($e["sort"] < $max_sort){
      	mysql_query("update elements set sort=sort-1 where sort=$e[sort]+1 and e_id = '$e[e_id]'");
      	mysql_query("update elements set sort=sort+1 where id=$e[id] ");
      }
    	$parent_el = get_element($id);
    	echo show_tree_catalog($parent_el["e_id"]);
      

    }elseif($p_cmd=="delete"){
      $id = (int)get_var_web("p_id");
      $count1 = mysql_result(mysql_query("select count(*) n from elements where e_id = $id"), "n");
      if(($count1 > 0)){
      	echo "<div class=\"error\">Удаление элемента невозможно! Имеются связанные записи.</div>";
      	$parent_el = get_element($id);
	    	echo show_tree_catalog($parent_el["e_id"]);
      }else{
      	$parent_el = get_element($id);
      	delete_har_element($id);
      	mysql_query("delete from elements where id = '$id'");
	    	echo show_tree_catalog($parent_el["e_id"]);
      }
    }
  }

  require "../include/bottom.php";
?>
