/*!
  \file
  \brief Модальное окно
*/

/*!
    \brief Отображение модального окна
    \param[in] modal Селектор модального окна
*/
function showModalWindow(modal){
    $(modal).fadeIn(150);

    $('body').children().each(function(){
        if (!$(this).hasClass("blured-screen")){
            $(this).addClass("blured-screen");
            return false;
        }
    });
}

/*!
    \brief Отображение модального окна
    \param[in] modal Селектор модального окна
    \param[in] callback Функция обратного вызова
*/
function hideModalWindow(modal, callback){
    let D1 = $(modal).fadeOut(150);
    
    let D2 = [].reverse.call($('body').children()).each(function(){
        if ($(this).hasClass("blured-screen")){
            $(this).removeClass("blured-screen");
            return false;
        }
    });

    if (typeof callback === 'function') $.when(D1,D2).done(callback)
}