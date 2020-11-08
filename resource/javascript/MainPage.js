var myName = "";

$(document).ready(function() {
  $("button#btn-chat-about").click(function() {
    $('#chat-info').slideToggle(200)
  });

  $('textarea#textbox').autoResize();

  
});

// скрытие открытого подменю
jQuery(function($){
  $(document).mouseup(function (e){
    if (!$("button#btn-chat-about").is(e.target)) {
      if ($('#chat-info').is(":visible")) $('#chat-info').slideToggle(200);
    }
  });
});

function hideSendButton(oField) {
  if ($.trim(oField.value)){
    if ($("button#send-message").is(":hidden")){
      $("button#send-message").show(50);
    }
  }else{
    if (!$("button#send-message").is(":hidden")){
      $("button#send-message").hide(100);
    }
  }
}

function genMessage(id_message, author, text, date){
  var itsMine = (author == myName);

  var result = 
  `<div class='msg-area' id='message${id_message}'>
    <div class='msg-container ${(itsMine)?"mine":"not-mine"}'>
      ${(itsMine)?"":"<div class='msg-author-name'>"+author+"</div>"}
      <div class='msg-date'>${date}</div>  
      <textarea class='msg-text' readonly>${text}</textarea>
    </div>
  </div>`;

  return result;
}

function sendMessage() {
  
  $('textarea#textbox').prop("disabled", true );
  $("button#send-message").addClass("Verification").removeClass("Idle");

  /*
  $.ajax({
      method: "POST",
      url: "/resource/action/" + action + ".php",
      data: data,
      success: function(result){ // result возвращает ошибку или пустое значение, если все ок
        $("button#send-message").removeClass("Verification");

        if (false){
          $("button#send-message").addClass("Valid");
          setTimeout(function() {
            $("button#send-message").removeClass("Valid").addClass("Idle");
            $("button#send-message").hide(100);
            $('textarea#textbox').val('').prop("disabled", false).css("height", "auto");
          },500)
          
          // добавление сообщения в html
          $('#main').append(genMessage("message-id-in-bd","author", $('textarea#textbox').val(),"date"));

          var obj = $('#main:last').children().last().children().last().children().last();      
          obj.scrollTop(obj.get(0).scrollHeight);
          var scrollHeight = obj.scrollTop() + obj.height();
          obj.scrollTop(0);
          obj.css("height", scrollHeight);
        }else{
          $("button#send-message").addClass("Invalid");
          setTimeout(function() {
            $("button#send-message").removeClass("Invalid").addClass("Idle");
            $('textarea#textbox').prop("disabled", false);
          },500)
        }
      }
  });
  */
  




  // код ниже просто для первичного представления         !!! закоментить при раскоменчивании верхнего !!!

  setTimeout(function() {
    $("button#send-message").removeClass("Verification");

    if (true){
      $("button#send-message").addClass("Valid");
      setTimeout(function() {
        $("button#send-message").removeClass("Valid").addClass("Idle");

        if ($.trim($('textarea#textbox').val())){
          if ($("button#send-message").is(":hidden")){
            $("button#send-message").show(50);
          }
        }else{
          if (!$("button#send-message").is(":hidden")){
            $("button#send-message").hide(100);
          }
        }
      },500)
      
      // добавление сообщения в html
      $('#main').append(genMessage("message-id-in-bd","", $('textarea#textbox').val(),"date"));

      var obj = $('#main:last').children().last().children().last().children().last();      
      obj.scrollTop(obj.get(0).scrollHeight);
      var scrollHeight = obj.scrollTop() + obj.height();
      obj.scrollTop(0);
      obj.css("height", scrollHeight);

      $('textarea#textbox').val('').prop("disabled", false).css("height", "auto");
    }else{
      $("button#send-message").addClass("Invalid");
      setTimeout(function() {
        $("button#send-message").removeClass("Invalid").addClass("Idle");
        $('textarea#textbox').prop("disabled", false);
      },500)
    }
  }, 2000);
}