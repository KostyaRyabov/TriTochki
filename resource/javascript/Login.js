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
});