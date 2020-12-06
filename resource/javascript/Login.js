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
    $("#login-form").on("submit", submit);
}

/// \brief Скрытие части контекста регистрации
function spoiler(){
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

/*!
    \brief Отправка формы регистрации или авторизации
    \param[in] e Событие
*/
function submit(e){
    e.preventDefault();
    
    let action, data = $(this).serialize();
    
    // Установка нужного действия
    if($("#register_form").attr("disabled")) action = "login";
    else action = "register";
    
    $.ajax({
        method: "POST",
        url: "/resource/action/" + action + ".php",
        data: data,
        success: request(result)
    });
}

/*!
    \brief Обработка результата запроса регистрации или авторизации
    \param[in] data Передаваемые данные
*/
function request(data){
    if(data.length > 1){
        $("input.invalid").removeClass("invalid");
        
        result = JSON.parse(data);
        $.each(data, errorMessage.bind(null,inputname, text));
        
        $("input:not(.invalid) + .error-message").slideUp(100, function(){$(this).remove()});
    } else location.href = "/";
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