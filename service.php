<div style="margin: 10px;">
    <div id="additional_services_title"></div>
    <div class="popup_intro">К каждому подарку мы предлагаем следующие дополнительные услуги:</div>
    <div class="popup_content">
        <ul class="additional_services">
            <li>Срочная доставка в день получения заказа. Стоимость 250 руб.;</li>
            <li>Необычная доставка: ваш подарок доставит фокусник, саксофонист или афроамериканец. Стоимость от 500 руб.;</li>
            <li>Индивидуализация подарка (печать фотографии одаряемого или любого другого изображения на упаковке). Бесплатно.</li>
        </ul>
    </div>
    <form action="/order.php" method="POST" class="ajaxform">
        <input type="hidden" value="send" name="p_cmd">
        <table class="no-border" id="feedback">
            <tbody>
                <tr>
                    <td><input type="submit" value="Заказать подарок" class="button b_service"></td>
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
                jQuery('.ajaxform input[type=submit]').val('Отправляется');  
                jQuery('.ajaxform button[type=submit]').text('Отправляется');  
                
                jQuery.ajax({
                    url: '/order.php',
                    dataType: 'html',
                    type: "POST",
                    data: jQuery(this).serialize()+"&submit=N",
                    success: function(result){
                        $("#fancybox-inner").html(result);
                        $.fancybox.resize();
                    }
                });  
            })
        })
    </script>
</div>