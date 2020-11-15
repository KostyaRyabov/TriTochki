function toggleModalWindow(modal, background){
    $(modal).fadeToggle(150);
    $(background).toggleClass("blured-screen");
}