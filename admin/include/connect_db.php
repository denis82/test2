<?
  setlocale (LC_ALL, "ru_RU.CP1251");

  if(!mysql_connect($common_options["db_server"], $common_options["db_user"], $common_options["db_pass"])){
    echo "<h1>Ошибка сервера MySQL. MySQL server error.</h1>";
    exit;
  }
  mysql_select_db($common_options["db_name"]);  
  mysql_query("set names 'cp1251'");
?>
