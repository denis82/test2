<?php

  $common_options["site_url"]="/";
  $common_options["site_name"]="������ �������";
  $common_options["document_root"]="/var/www/picomsu/data/www/mybestpodarok.picom.su/";
  $common_options["files_folder"]="files/";
  $common_options["upload_folder"]=$common_options["document_root"].$common_options["files_folder"];
  $common_options["func_folder"]=$common_options["document_root"]."functional/";
  $common_options["tpl_folder"]=$common_options["document_root"]."templates/";
  $common_options["include_folder"]=$common_options["document_root"]."admin/include/";
  $common_options["site_files_url"]=$common_options["site_url"].$common_options["files_folder"];
  $common_options["org"] = "�������� ������� �������";
  $common_options["from_email"] = "site@mybestpodarok.ru";

  $common_options["db_server"]="localhost";
  $common_options["db_name"]="picomsu_mybestpodarok";
  $common_options["db_user"]="picomsu";
  $common_options["db_pass"]="NyAIh9kH";

  
  $common_options["content_te_id"] = 1;
  $common_options["events_te_id"] = 5;
  
  $common_options["functional"]["text"] = "��������� ��������";
  $common_options["functional"]["feedback"] = "��������";

  $common_options["index"] = 2;
  $common_options["site_root"] = 1;
  $common_options["map_page"] = -1;
  $common_options["feedback_page"] = 36;
  $common_options["search_page"] = 72;
  $common_options["response_page"] = 35;
  $common_options["order_page"] = 422;
  $common_options["basket_page"] = 1010;

  $common_options["collect_url"] = "/catalog/collect?c=";
  $common_options["price_url"] = "/catalog/price?p=";
  $common_options["basket_url"] = "/basket";
  

  $admin_options["url"] = $common_options["site_url"]."admin/";
  $admin_options["org"] = "�������� ������� �������";

  $admin_options["admin_login"] = "admin";
  $admin_options["admin_password"] = "kjuu;j15F96t3Gv";//"RWufKkp224";

  
	$common_options["month_name"] = array(
	  "01" => "������",
	  "02" => "�������",
	  "03" => "�����",
	  "04" => "������",
	  "05" => "���",
	  "06" => "����",
	  "07" => "����",
	  "08" => "�������",
	  "09" => "��������",
	  "10" => "�������",
	  "11" => "������",
	  "12" => "�������"
	);

  $common_options["order_status"][3] = "��������";
  $common_options["order_status"][2] = "����� � ������";
  $common_options["order_status"][1] = "����� �� ���������";
  
  
  $common_options["oplata"][1] = "�������� ������ ��� ��������� �������";
  $common_options["oplata"][2] = "����������� ������ (������.������)";
  $common_options["oplata"][3] = "���������� ������ �� �����";
  
  $common_options["delivery"][1] = "������ ��� �� �����";
  $common_options["delivery"][2] = "�������� �� ������� (����� ���������� ������)";
  $common_options["delivery"][3] = "�������� �� ������� (��������� �����)";

  $common_options["delivery_price"][1] = "0";
  $common_options["delivery_price"][2] = "50";
  $common_options["delivery_price"][3] = "100";

  $common_options["city"][1] = "������";
  $common_options["city"][2] = "������ �����";

  $common_options["sort"]["no"] = "";
  $common_options["sort"]["asc"] = "����������� ����";
  $common_options["sort"]["desc"] = "���������� ����";

  
  $common_options["sklad"][0] = "��������";
  $common_options["sklad"][1] = "� �������";
	

?>