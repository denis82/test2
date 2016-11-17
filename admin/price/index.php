<?
  $title_page = "Обновить прайс";
  require "../include/header.php";
  
	$output .= "<table>";
	$output .= "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">";
	$output .= create_input("hidden", "p_cmd", "update");
	$buttons = "<input type=\"submit\" value=\"Обновить\" class=\"button\">";

	$output .= "<tr><td class='name_prop'>Выбери файл с ценами</td><td>";
	$output .= create_input("file", "price");
	$output .= "</td></tr>";
	$output .= "<tr height='20'><td colspan=2></td></tr>";
	$output .= "<tr><td colspan=2>$buttons</td></tr>";
	$output .= "</form>";
	$output .= "</table><br><br>";    	

	
	$filename = "price.zip";
	
	if($_FILES["price"]["tmp_name"] ){
	  if(file_exists($common_options["upload_folder"].$filename)){
	    unlink($common_options["upload_folder"].$filename);
	  }
	  copy($_FILES["price"]["tmp_name"], $common_options["upload_folder"].$filename);
	  $output .= "<p>Файл успешно загружен</p>";
	}
	
  if(file_exists($common_options["upload_folder"].$filename)){
    $output .= "<p>Текущий файл: ".ceil(filesize($common_options["upload_folder"].$filename)/1024)." Kb</p>";
  }
	
  
  echo $output;

  require "../include/bottom.php";
  
  
  
?>
