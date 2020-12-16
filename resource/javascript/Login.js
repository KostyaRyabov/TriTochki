/*!
  \file
  \brief Страница регистрации и авторизации
*/

/// \cond
$(document).ready(init);
/// \endcond

/// \brief Инициализация
function init(){
    $('body').fadeIn(300);
    $('.modal-window-trigger').on("click",function(){toggleModalWindow(this, "table")});
    $("button#spoiler_links").click(spoiler);
    $("#restore_password").click(spoilerRestore);
    $("#login-form").on("submit", submit);
}

/// \brief Скрытие части контекста регистрации
function spoiler(){
    /*if(document.getElementById("pwd").disabled){
        spoilerRestore();
    }*/
    
    if ($(this).text() == "Создать аккаунт"){
        $(this).html('Имеется аккаунт');
        document.getElementById("register_form").disabled = false;

        $("DIV#spoiler_body").slideDown();
    } else {
        $(this).html('Создать аккаунт');
        
        $("DIV#spoiler_body").slideUp(function(){
            document.getElementById("register_form").disabled = true;
        });
    }

    return false;
}

/// \brief Скрытие части контекста для восстановления пароля
function spoilerRestore(){
    let $register = $("button#spoiler_links");
    
    $("input.invalid").removeClass("invalid");
    $(".error-message").remove();
    
    if($(this).text() == "Забыли пароль?"){
        $(this).text("Вспомнили пароль?");
        
        $("#pwd").slideUp(function(){
            document.getElementById("pwd").disabled = true;
        });
        $("#register_form").slideUp();
        $("[name='username']").focus();
        
        $register.slideUp();
        $(".line").slideUp();
    } else{
        $(this).text("Забыли пароль?");
        
        $("#restored").remove();
        
        document.getElementById("pwd").disabled = false;
        $("#pwd").slideDown();
        $("#register_form").slideDown();
    
        $(".line").slideDown();
        $register.slideDown();
    }
}

/*!
    \brief Отправка формы регистрации или авторизации
    \param[in] e Событие отправки
*/
function submit(e){
    e.preventDefault();
    
    let action, data = $(this).serialize();
    
    // Установка нужного действия
    if($("#pwd").attr("disabled")) action = "restore";
    else if($("#register_form").attr("disabled")) action = "login";
    else action = "register";
    
    $.ajax({
        method: "POST",
        url: "/resource/action/" + action + ".php",
        data: data,
        success: function(result){
            if(result.length > 1){
                $("input.invalid").removeClass("invalid");
                
                result = JSON.parse(result);

                $.each(result, function(inputname, text){
                    errorMessage(inputname, text);
                });
                
                $("input:not(.invalid) + .error-message").slideUp(100, function(){$(this).remove()});
            } else if(action == "restore")
                $("[name='username']").parent().append("<span id='restored'>Письмо с инструкциями выслано на Вашу почту.</span>");
            else location.href = "/";
        }
    });
}

/*!
    \brief Вывод ошибки
    \param[in] inputname Наименование поля ввода, к которому нужно привязать ошибку
    \param[in] text Сообщение ошибки
*/
function errorMessage(inputname, text){
    let input = $(`input[name='${inputname}']`);
    let erMsg = input.next('.error-message');

    input.addClass("invalid");

    if (!erMsg.length){
        erMsg = $(`<span class="error-message">${text}</span>`).hide();
        input.after(erMsg);
        erMsg.slideDown(100);
    }else{
        erMsg.html(text);
    }
}