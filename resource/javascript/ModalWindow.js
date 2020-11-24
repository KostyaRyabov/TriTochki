function showModalWindow(modal){
    $(modal).fadeIn(150);

    $('body').children().each(function(){
        if (!$(this).hasClass("blured-screen")){
            $(this).addClass("blured-screen");
            return false;
        }
    });
}

function hideModalWindow(modal){
    $(modal).fadeOut(150);
    
    [].reverse.call($('body').children()).each(function(){
        if ($(this).hasClass("blured-screen")){
            $(this).removeClass("blured-screen");
            return false;
        }
    });
}