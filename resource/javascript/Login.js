$(document).ready(function(){
    $('body').fadeIn(300);

    $('.modal-window-trigger').on("click",function(){
        toggleModalWindow(this, "table")
    });
    
    $("button#spoiler_links").click(function(){
        if ($(this).text() == "создать аккаунт"){
            $(this).html('имеется аккаунт');
            document.getElementById("register_form").disabled = false;

            $("DIV#spoiler_body").slideDown();
        } else {
            $(this).html('создать аккаунт');
            
            $("DIV#spoiler_body").slideUp(function(){
                document.getElementById("register_form").disabled = true;
            });
        }

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
            success: function(result){ // result возвращает ошибки в JSON формате или пустое значение, если все ок
                if(result.length > 1){
                    // Сброс ошибок, чтобы не наслаивались друг на друга
                    $("input.invalid").removeClass("invalid");
                    $(".error-message").hide();
                    
                    result = JSON.parse(result);
                    $.each(result, function(inputname, text){
                        errorMessage(inputname, text);
                    });
                } else location.href = "/";
            }
        });
    });
});

function errorMessage(inputname, text){
    let input = $(`input[name='${inputname}']`);
    let erMsg = input.prev('.error-message');

    input.addClass("invalid");

    if (!erMsg.length){
        erMsg = $(`<span class="error-message">${text}</span>`).hide();
        input.after(erMsg);
        erMsg.slideDown(100);
    }else{
        erMsg.html(text);
    }
}