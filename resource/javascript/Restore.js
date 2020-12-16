/*!
  \file
  \brief Страница восстановления пароля
*/

///	\brief GET-параметры
var params = window
	.location
	.search
	.replace('?', '')
	.split('&')
	.reduce(
		function(p, e){
			var a = e.split('=');
			p[decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
			return p;
		},
		{}
	);

/// \cond
$(document).ready(init);
/// \endcond

/// \brief Инициализация
function init(){
	if(!params["code"]) location.href = "/Login.html"; // Не пускаем без кода
	$('body').fadeIn(300);
	$('.modal-window-trigger').on("click", function(){
		toggleModalWindow(this, "table")
	});
	$("#restore-form").on("submit", submit);
}

function submit(e){
	e.preventDefault();
	
	$.ajax({
		method: "POST",
		url: "/resource/action/user_restore_password.php",
		data: {
			"password": $("[name='password']").val(),
			"repeat_password": $("[name='repeat_password']").val(),
			"code": params["code"]
		},
		success: function(result){
			if(result.length > 1){
				result = JSON.parse(result);
				
				$(".error-message").remove();
				
				$("#restore-form").append("<p class='error-message'>" + result.error + "</p>");
			} else location.href = "/Login.html";
		}
	});
}