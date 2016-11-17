<?
  $title_page = "Выполненеи sql кода";
  require "../include/header.php";

  $i=4;
  $e_id = 34;
  $res=mysql_query("select * from elements where te_id=\"202\"");
  while ($row = mysql_fetch_array($res)) {
  	$i++;
  	mysql_query("update elements set e_id=$e_id, `sort`=$i where id=$row[id]");
  }

  require "../include/bottom.php";
?>
