<?php

  $cmd = get_var_web("p_cmd");
	$basket_e_id = get_functional_element("basket");

	$count_cmd = 1;
	$parameter_page["title"] = "Оформить заказ";
	$parameter_page["name"] = "Оформить заказ";
  
  for($number_cmd =0 ; $number_cmd < $count_cmd; $number_cmd++){
  	if($cmd=="upd"){
		  $p_id = get_var_web("p_id");
		  $p_del = get_var_web("p_del");
		  $p_number = get_var_web("p_number");
		  
		  for ($i=0; $i<count($p_id); $i++){
		  	$n = (int)$p_number[$i];
		  	$id = (int)$p_id[$i];
  			mysql_query("update string_baskets set number = $n where id = '$id' and b_id = '$_SESSION[b_id]'");
		  }
  		
		  for ($i=0; $i<count($p_del); $i++){
		  	$id = (int)$p_id[$i];
  			mysql_query("delete from string_baskets where id = '$id' and b_id = '$_SESSION[b_id]'");
		  }
  		$count_cmd=2;
  		$cmd= "";
  		
  	}elseif($cmd == "del"){
		  $p_id = get_var_web("p_id");
	  	$id = (int)$p_id;
			if($id){
				mysql_query("delete from string_baskets where id = '$id' and b_id = '$_SESSION[b_id]'");
			}
			$output .= show_basket("write")."<br><br>";
			
  	}elseif($cmd == "order"){
		  if($old_cmd == "add"){
  			$output .= "<p><br><a href=\"JavaScript: history.back();\">Вернуться к выбору товаров</a><br><br></p>";
		  }
			$output .= show_basket("write")."<br><br>";
			$parameter_page["title"] = "Корзина";
			$parameter_page["name"] = "Корзина";
			
  	}elseif($cmd == "order2"){
			$parameter_page["title"] = "Оформить заказ";
			$parameter_page["name"] = "Оформить заказ";			
			$output .= show_basket("read");
			$output .= show_order(array());
  	
  	}elseif($cmd == "save_order"){
			$parameter_page["title"] = "Оформить заказ";
			$parameter_page["name"] = "Оформить заказ";

  	  	$param["firstname"] = check_var_web($_POST["firstname"]);
  	  	$param["email"] = check_var_web($_POST["email"]);
  	  	$param["phone"] = check_var_web($_POST["phone"]);
  	  	
  	  	$param["address"] = check_var_web($_POST["address"]);
  	  	$param["city"] = check_var_web($_POST["city"]);
  	  	$param["city_name"] = $common_options["city"][$param["city"]];
  	  	$param["oplata"] = (int)check_var_web($_POST["oplata"]);
  	  	$param["oplata_name"] = $common_options["oplata"][$param["oplata"]];
  	  	$param["delivery"] = (int)check_var_web($_POST["delivery"]);
  	  	$param["delivery_name"] = $common_options["delivery"][$param["delivery"]];
  	  	
  	  	$param["comment"] = check_var_web($_POST["comment"]);
  	  	$res = trim(stripslashes($_POST["p_res"]));
  	  	
  	  	
  		  if($param["firstname"]&&$param["city"]&&$param["phone"]&&$param["address"]&&($res=='4')){
    			$message = "";
    			$message .= "Заказ товара\n\n";
    			$message .= "Имя: $param[firstname] \n";
  		    $message .= "Контактный телефон: $param[phone] \n";
  		    $message .= "E-mail: $param[email] \n\n";
  		    $message .= "Город: $param[city_name] \n\n";
  		    $message .= "Адрес: $param[address] \n\n";
    			$message .= "Доставка: $param[delivery_name] \n";
    			$message .= "Оплата: $param[oplata_name] \n";
  		    $message .= "Примечания: $param[comment] \n \n \n";
    			$message .= get_basket_mail();				
  				$email_in = get_value_option("feedback_email");
  				$emails = explode(",", $email_in);
  				foreach ($emails as $e1) {
    				if(@my_mail(trim($e1), "Поступил новый заказ", $message, $common_options["from_email"])){
    				  $status_send = 1;
    				}
  				}
					$sum = get_sum_basket();
  				
  				if($param["oplata"]==2){
  					//банковская карта
  					$parameter_page["title"] = "Оплата заказа";
  					$parameter_page["name"] = "Оплата заказа";
  			  	$message = str_replace("'", "\'", str_replace("\n", "<br>", $message));
  					mysql_query("insert into orders (date, u_id, text, email, fio, address, summa, comment, oplata, delivery, subscribe) 
  					  values (now(), ".nvl($_SESSION["u_id"], 'null').", '$message', '".$param["email"]."', '".$param["firstname"]."', '".$param["index"]." ".$param["region"]." ".$param["city"]." ".$param["address"]."', '".$sum."', '".$param["comment"]."', '".$param["oplata"]."', '".$param["delivery"]."', '".$param["subscribe"]."')");
  					$order_id = mysql_insert_id();
  					if($order_id){
	  			  	$output .= "<p>Благодарим Вас за проявленный интерес к нашей компании!</p>";
  					}
  					
  				}else{
	  				if($status_send){
	  					$parameter_page["title"] = "Ваш заказ отправлен";
	  					$parameter_page["name"] = "Ваш заказ отправлен";
	  			  	$output .= "<p>Уважаемый (-ая) $param[firstname], благодарим Вас за оформление заказа в онлайн-магазине Za-za-zu.com! Вся указанная информация поступила персональному менеджеру, который свяжется с Вами в ближайшее время для уточнения и подтверждения данных.</p>";
	  			  	$message = str_replace("'", "\'", str_replace("\n", "<br>", $message));
	  					mysql_query("insert into orders (date, u_id, text, email, fio, address, summa, comment, oplata, delivery, subscribe) 
	  					  values (now(), ".nvl($_SESSION["u_id"], 'null').", '$message', '".$param["email"]."', '".$param["firstname"]."', '".$param["index"]." ".$param["region"]." ".$param["city"]." ".$param["address"]."', '".$sum."', '".$param["comment"]."', '".$param["oplata"]."', '".$param["delivery"]."', '".$param["subscribe"]."')");
	  			  	mysql_query("delete from string_baskets where b_id = '$_SESSION[b_id]'");
	  			  }else{
	  					$parameter_page["title"] = "Ошибка";
	  					$parameter_page["name"] = "Ошибка";
	  			  	$output .= "<p><b>В данный момент операцию завершить невозможно.<br>Попробуйте через некоторое время.<br>Извините за неудобства.</b></p>";
	  				}
  				}
  		  }else{
    			$output .= show_basket("read")."<br><br>";
    			$output .= show_order($_POST, "*");
  		  }
  	
  	}elseif($cmd == "add"){
  		init_basket();
  		$p_id = (int)$_GET["p_id"];

  		if($p_id){
	  		if(mysql_num_rows(mysql_query("select * from string_baskets where e_id = '$p_id' and b_id = '$_SESSION[b_id]' "))){
	  			mysql_query("update string_baskets set number = number+1 where e_id = '$p_id' and b_id = '$_SESSION[b_id]'");
	  		}else{
  				mysql_query("insert string_baskets (b_id, e_id, number) values ('$_SESSION[b_id]', '$p_id', '1')");
	  		}
  		}
  		
		  $count_cmd = 2;
		  $cmd = "order";
		  $old_cmd = "add";

  	}else{
			$parameter_page["title"] = "Корзина";
			$parameter_page["name"] = "Корзина";		
			$output .= show_basket("write")."<br><br>";
		}
	}
	
	$parameter_page["main_text"] = $output;
		  	
  

?>