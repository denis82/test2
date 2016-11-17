<?

  $title_page = "Управление отзывами";
  require "../include/header.php";



  function edit($p_cmd, $p_id){
    $result=mysql_query("select * from response where id=\"$p_id\"");
    $fetch=mysql_fetch_array($result);
    echo "<form action=\"index.php\" method=\"POST\">";
    echo "<table>";
    echo "<input type=\"hidden\" name=\"p_cmd\" value=\"$p_cmd\">";
    echo "<input type=\"hidden\" name=\"p_id\" value=\"$p_id\">";
    echo "
          <tr><td align=\"right\"  class='name_prop'>От кого</td><td><input  class=\"text\" type=\"text\" name=\"p_name\" value=\"$fetch[name]\"></td></tr>
          <tr><td align=\"right\"  class='name_prop'>Отзыв</td><td>".create_input("wisiwig", "p_text", $fetch["text"])."</td></tr>
          <tr><td align=\"right\"  class='name_prop'>Видео</td><td><textarea  class=\"text\" name=\"p_video\" rows=5 cols=50>".htmlspecialchars($fetch["video"])."</textarea></td></tr>
          <tr><td align=\"right\"  class='name_prop'>Подарок</td><td>".create_input("list_catalog", "p_e_id", $fetch["e_id"])."</td></tr>
          <tr><td align=\"right\" colspan=\"2\"><input type=\"submit\" value=\"Сохранить\"></td></tr>
         ";
    echo "</table>";
    echo "</form>";
  }


  //инициализация переменных

  $p_cmd=get_var_web("p_cmd");
  $p_name=nvl(htmlspecialchars(stripslashes(trim(get_var_web("p_name")))), "Аноним");
  $p_text=addslashes((stripslashes(trim(get_var_web("p_text")))));
  $p_video=addslashes(stripslashes(trim(get_var_web("p_video"))));
  $p_e_id=(int)get_var_web("p_e_id");
  $p_id=get_var_web("p_id");
  
  
  $message="";

  $count_cmd=1;
  //конец иннициализации

  for($number_cmd=1; $number_cmd<=$count_cmd; $number_cmd++){
    if($p_cmd=="" || $p_cmd=="list_inquiry"){
      //echo "<p>[<a href=\"index.php?p_cmd=new_i\">новый опрос</a>]<p>";
      $query="select * from response order by date desc";
      $result=mysql_query($query);
      if(mysql_num_rows($result)>0){
        echo "<p>Отзывы</p>";
        echo "<table class=\"elements\">";
        //echo "<table><tr><td align=\"center\"><b>Вопрос</b></td><td align=\"center\"><b>Активность</b></td></tr>";
        for ($i=0; $i<mysql_num_rows($result); $i++){
          $g = array();
          $row=mysql_fetch_array($result);
          if($row["e_id"]){
            $g = get_element($row["e_id"]);
          }
          
          $style = '';
		if ($row['active'] == 0) {
			$style = 'style="color:red"';
			$activeLink = 'Опубликовать';
		} else {
			$activeLink = 'Скрыть';
		}
		
          echo "<tr>
					<td><a href=\"index.php?p_cmd=edit&p_id=$row[id]\">".nvl($g["name"], "Без подарка")."</a></td>
					<td ".$style.">".$row[3]."</td>
					<td><a href=\"index.php?p_cmd=activeResp$row[active]&p_id=$row[id]\">$activeLink</a></td>
					<td><a href=\"index.php?p_cmd=delete&p_id=$row[id]\">удалить</a></td>
				</tr>";
        
        }
      }else{
        echo "Отзывов нет!";
      }

    }elseif($p_cmd=="edit"){
      echo "<div class='top-path'>Редактирование отзыва</div>";
      edit("update", $p_id);
	
	}elseif($p_cmd=="activeResp0"){
      mysql_query("update response
                       set
                         active = 1
                       where id=\"$p_id\"
                   ");
       $p_cmd="";
      $count_cmd=2;
	}elseif($p_cmd=="activeResp1"){
      mysql_query("update response
                       set
                         active = 0
                       where id=\"$p_id\"
                   ");
       $p_cmd="";
      $count_cmd=2;
	

    }elseif($p_cmd=="update"){
      if($p_text){
        mysql_query("update response
                       set
                         name = \"$p_name\",
                         text = \"$p_text\",
                         video = \"$p_video\",
                         e_id = \"$p_e_id\"
                       where id=\"$p_id\"
                   ");

        $count_cmd=2;
        $p_cmd="";
      }else{
        echo "<hr width=\"100%\"><font color=\"red\"><i>Необходимо заполнить поле \"Текст\"</i></font>
              <br><a href=\"JavaScript:history.go(-1)\">Назад</a><hr width=\"100%\">";
      }
      echo mysql_error();

    }elseif($p_cmd=="delete"){
      //удалить акцию
      mysql_query("delete from response where id=\"$p_id\"");
      $p_cmd="";
      $count_cmd=2;

      echo mysql_error();

    }//echo "<pre>"; print_r($p_cmd);echo "</pre>";
  }

  require "../include/bottom.php";
  
  
  ?>
