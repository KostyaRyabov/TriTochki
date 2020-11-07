$(document).ready(function() {
    $("button#btn-chat-about").click(function() {
        $('#chat-info').slideToggle(200)
    });
});

function autoGrow (oField) {
    if (oField.scrollHeight > oField.clientHeight) {
      oField.style.height = oField.scrollHeight + "px";
    }
  }