<?

  
  require "inc.php";

  echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" />
<title>$title_page</title>
<link href=\"/admin/styles.css\" rel=\"stylesheet\" type=\"text/css\" />
<script language=\"JavaScript\">
  function del(url){
    if(confirm('Вы действительно хотите удалить эту запись?\\nВосстановление будет не возможно.')){
      location.href=url;
    }
  }
  var add_code = '';
  function process_add_field(){
    if ((req.readyState == 4)&&(req.status == 200)) {
      var response = req.responseText;
      //document.getElementById(add_code).innerHTML += response;
		  var div = document.createElement('div');
		  div.innerHTML = response;
      document.getElementById(add_code).appendChild(div);
    }
  }
			

	function add_field(p_code, p_type){
		var url = '/admin/add_field.php?p_code='+p_code+'&p_type='+p_type;
    if (window.XMLHttpRequest) {
        req = null;
        req = new XMLHttpRequest();
        req.open(\"GET\", url, true);
    // IE
    } else if (window.ActiveXObject) {
        req = new ActiveXObject(\"Microsoft.XMLHTTP\");
        req.open(\"GET\", url, true);
    }
    req.onreadystatechange = process_add_field;
    add_code = p_code;
    req.send(null);
  	
  }
</script>
</head>
<body>
<div id=\"layer\">
<div id=\"header\">
<div id=\"header_menu\"><div id=\"header_menu-br\">
<ul>
<li><a href=\"/admin/content/\" title=\"Страницы\">Страницы</a></li>
<li><a href=\"/admin/response/\" title=\"Отзывы\">Отзывы</a></li>
<li><a href=\"/admin/options/\" title=\"Настройки системы\">Настройки системы</a></li>
</ul>
</div></div></div>

<table id=\"main-block\">
<tr>
<td id=\"center\">
<table width=\"100%\">
<tr>
<td id=\"content\">";

			
?>
