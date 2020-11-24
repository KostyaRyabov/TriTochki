function showModalWindow(modal){
    $(modal).fadeIn(150);

    let background = $(modal).prev();
    if (!background.hasClass("blured-screen")) background.addClass("blured-screen");
}

function hideModalWindow(modal){
    $(modal).fadeOut(150);

    let background = $(modal).prev();
    if (background.hasClass("blured-screen")) background.removeClass("blured-screen");
}