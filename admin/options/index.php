<?
  $title_page = "Управление настройками системы";
  require "../include/header.php";

  $count=1;
  $p_cmd = get_var_web("p_cmd");
  if(!$p_cmd){
  	$p_cmd = "list_options";
  }

  for($j=0; $j < $count; $j++){
    if($p_cmd=="list_options"){
      $result = mysql_query("select * from options where show_on_web = 1");
      echo "<table class='elements' cellspacing='0' cellpadding='0'><tr><th>Наименование</th><th>Значение</th><th>Задачи</th></tr>";
      for($i=0; $i < mysql_num_rows($result);$i++){
        $row=mysql_fetch_array($result);
        echo "<tr><td>$row[name]</td><td class='name'>".substr(nvl(htmlspecialchars($row["value"]), "&nbsp;"), 0, 30)."</td><td class=\"link\">[<a href=\"index.php?p_cmd=edit&p_id=$row[id]\">изменить</a>]</td></tr>";
      }
      echo "</table>";

    }elseif($p_cmd=="edit"){
      $p_id = get_var_web("p_id", "int");
      $result=mysql_query("select * from options where id=\"$p_id\"");
      $row = mysql_fetch_array($result);
    	echo "<p>Изменить значение для <b>$row[name]</b></p>";
    	echo "<p>$row[description]</p>";
      echo "<form action=\"index.php\" method=\"post\">";
      echo "<input type=\"hidden\" name=\"p_cmd\" value=\"update\">";
      echo "<input type=\"hidden\" name=\"p_id\" value=\"$row[id]\">";
      echo "<table><tr><td>Значение: </td><td>".create_input($row["type"], "p_value", $row["value"])."</td></tr>";
      echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"button\" value=\"Отмена\" class=\"button\" onClick=\"JavaScript:location.href='index.php';\"><input type=\"submit\" value=\"Сохранить\" class=\"button\"></td></tr>";
      echo "</table></form>";

    }elseif($p_cmd=="update"){
      $value = get_var_web("p_value");
      $p_id = get_var_web("p_id");
      $result=mysql_query("select * from options where id=\"$p_id\"");
      $row = mysql_fetch_array($result);
	  	if($row["save"]=="wisiwig"){
	  		$v = $value;
	  	}else{
	  		$v = check_var_web($value);
	  	}
	  	//echo $v;
	    mysql_query("update options set value=\"$v\" where id=$p_id");
	    $p_cmd="list_options";
	    $count = 2;

    }elseif($p_cmd=="delete_value"){
      $p_id = get_var_web("p_id");
      mysql_query("delete from options where id=$p_id");
      $p_cmd="list_options";
      $count = 2;
    }
  }

  require "../include/bottom.php";
?>
