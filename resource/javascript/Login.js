$(document).ready(function(){
    $('body#main').fadeIn(300);
    
    $("button#spoiler_links").click(function(){
        if ($(this).text() == "создать аккаунт"){
            $(this).html('имеется аккаунт');
            document.getElementById("register_form").disabled = false;
        } else {
            $(this).html('создать аккаунт');
            document.getElementById("register_form").disabled = true;
        }

        $("DIV#spoiler_body").toggle('normal');
        return false;
    });
    
    // Обработка отправки формы для логина и регистрации
    $("#login-form").on("submit", function(e){
        e.preventDefault();
        
        let action, data = $(this).serialize();
        
        // Установка нужного действия
        if($("#register_form").attr("disabled")) action = "login";
        else action = "register";
        
        $.ajax({
            method: "POST",
            url: "/resource/action/" + action + ".php",
            data: data,
            success: function(result){ // result возвращает ошибку или пустое значение, если все ок
                if(result.length > 1) return false; //todo вывод ошибок в форму логина
                
                location.href = "/";
            }
        });
    });
});