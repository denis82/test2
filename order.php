<?
$arResult["MESSAGE"] = false;
$arResult["ERRORS"] = array();
$arResult["ERRORS_FIELD"] = array();
$email = "";

if ($_POST["submit"] == 'Y') {
    $email = trim(iconv("UTF-8", "windows-1251", $_POST["email"]));
    $name = trim(iconv("UTF-8", "windows-1251", $_POST["name"]));
    $message = trim(iconv("UTF-8", "windows-1251", $_POST["message"]));
    
    // �������� �����
    
    if (!trim($_POST["name"])) {
        $arResult["ERRORS"]["name"] = "�� ��������� ��� ���������.";
    }
    
    // ��������� ������ �����.
    if (!$email) {
        $arResult["ERRORS"]["email"] = "�� ��������� ���� e-mail";
    } elseif (!preg_match("|^([0-9a-zA-Z]+[-._+&amp;])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}$|", $email)) {
        $arResult["ERRORS"]["email"] = "���� e-mail ��������� �������.";
    }
    
    // if (!trim($_POST["message"])) {
        // $arResult["ERRORS"]["message"] = "�� ��������� ���� ���������.";
    // }

    $mailer = "mybestpodarok@mail.ru";
    
    if (empty($arResult["ERRORS"])) {
        $to      = $mailer;
        $subject = '����� ��������� � mybestpodarok.ru';
        $message = '������������: ' . $email . PHP_EOL . '������� ����� ���������: ' . PHP_EOL . $message;
        $headers = 'From: webmaster@mybestpodarok.ru' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            $arResult["MESSAGE"] = "���� ��������� ������� ����������.";
        } else {
            $arResult["ERRORS"]["send"] = "��������� ������ �������� ���������. ���������� ����� �����.";
        }
    }
} ?>

<div style="margin: 10px;">
    <?if ($arResult["MESSAGE"]) {?>
        <p><?=$arResult["MESSAGE"]?></p>
		<script>			
			yaCounter23184481.reachGoal('ORDER');
			
			ga('send', 'event', 'forms', 'order', 'orderok');			
		</script>
    <?} else {?>
    <?foreach ($arResult["ERRORS"] as $errors) {?>
        <p><?=$errors?></p>
    <?}?>
    <form action="/order.php" method="POST" class="ajaxform ds">
        <input type="hidden" value="send" name="p_cmd">
        <table class="no-border" id="feedback">
            <tbody>
                <tr>
                    <td>
                        <label>���*</label>
                        <input value="<?=($name) ? $name: '';?>" size="40" maxlength="90" name="name" class="input-short">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>E-mail*</label>
                        <input value="<?=($email) ? $email: '';?>" size="40" name="email" class="input-short">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>���������</label>
                        <textarea rows="7" cols="35" name="message" class="input"><?=($message) ? $message : '';?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" value="��������" class="button order"></td>
                </tr>
                
            </tbody>
        </table>
    </form>
    <script>
        jQuery(document).ready(function($){
            jQuery('.ajaxform').submit(function(event){ 
                event.preventDefault()        
                jQuery('.ajaxform input[type=submit]').attr('disabled','disabled');       
                jQuery('.ajaxform button[type=submit]').attr('disabled','disabled'); 
                jQuery('.ajaxform input[type=submit]').val('������������');  
                jQuery('.ajaxform button[type=submit]').text('������������');  
                
                jQuery.ajax({
                    url: '/order.php',
                    dataType: 'html',
                    type: "POST",
                    data: jQuery(this).serialize()+"&submit=Y",
                    success: function(result){						
                        $("#fancybox-inner").html(result);
                        $.fancybox.resize();
                    }
                });  
            })
        })
		
		$('.button.order').click(function(){
			ga('send', 'event', 'buttons', 'order', 'trysend');			
		});			
    </script>
    <?}?>
</div>