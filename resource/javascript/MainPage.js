var myName = "";

var params = window
   .location
   .search
   .replace('?','')
   .split('&')
   .reduce(
      function(p,e){
        var a = e.split('=');
        p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
        return p;
      },
      {}
   );

$(document).ready(function() {
  showInfoBox();

  $('.modal-window-trigger').on("click",function(){
    toggleModalWindow('.modal-window-wrapper', "table")
  });
  
  $('textarea#textbox').autoHeight();
  
  // Проверка на авторизацию
  $.ajax({
    method: "POST",
    url: "/resource/action/check.php",
    success: function(result){ // result возвращает имя пользователя или 0 соответственно
      if(result == 0) location.href = "/Login.html";
      else myName = result;
    }
  });
  
  // Получение чата и всех сопутствующих данных
  $.ajax({
    method: "GET",
    url: "/resource/action/get_chat.php",
    data: {
      "id": params["id"]
    },
    success: function(result){ // возвращает объект json
      result = JSON.parse(result);
      
      // Вывод информации о чате
      $("#tab-name").text(result.name);
      $("#chat-create-date").text(result.date);
      $("#chat-info-contact-list").html(""); // Сперва очищаем от значений по умолчанию
      $.each(result.users, function(id, value){
        $("#chat-info-contact-list").append("<button class='list-item' onClick='openContact(" + id + ")'>" + value + "</button>");
      });
      
      // Вывод сообщений
      $.each(result.messages, function(id, value){
        genMessage(id, result.users[value["user"]], value["text"], value["date"]);
      });
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

function showInfoBox(){
  if (!$("#btn-chat-about").length){
    let obj = $(`<button id="btn-chat-about" class='btn modal-window-trigger'>?</button>`).hide();
    $('.tab').append(obj);
    obj.show(200);
  }

  if (!$("modal-window-wrapper").length){
    $('body').append(`
      <div class="modal-window-wrapper">
        <div class="block-screen modal-window-trigger"></div>
        <div id="info-box" class="modal-window">
            <span class="chat-info-header"></span>
            <hr/>
            <span id="chat-create-date"></span>
            <hr/>
            <br/>
            <span class="chat-info-header"></span>
            <hr/>
            <div id="chat-info-contact-list">
            </div>
            <hr/>
        </div>
    </div>
    `);
  }
}

function hideInfoBox(){
  if ($("#btn-chat-about").length){
    $("#btn-chat-about").hide(200,function(){
      $(this).remove();
    });
  }

  if ($("info-box-wrapper").length){
    $("info-box-wrapper").hide(200,function(){
      $(this).remove();
    });
  }
}

// отображение списка чатов пользователя (их id и названия)
function showChatListContext(){
  hideInfoBox();
  $("#input-area").slideUp(200);
  $("#tab-name").html('мои чаты');

  $('#main').fadeOut(200,function(){
    //todo: запуск анимации загрузки
  });

  //запрос списка чатов пользователя из БД
  $.ajax({
    method: "GET",
    url: "/resource/action/user_chat_list.php",
    success: function(result){ // возвращает объект json
      result = JSON.parse(result);
      
      let context = "";

      $.each(result, function(id, name){
        context += `<button class="list-item" id="${id}" onClick=openChat(${id})>${name}</button>`;
      });
      
      $('#main').html(context);

    // если result = false -> $(this).html('');
    //todo: завершение анимации загрузки
    }
  });
}

// отображение списка контактов пользователя (их id и названия)
function showContactListContext(){
  hideInfoBox();
  $("#input-area").slideUp(200);
  $("#tab-name").html('мои контакты');

  $('#main').fadeOut(200,function(){
    //todo: запуск анимации загрузки
  });

  //запрос списка контактов пользователя из БД
  $.ajax({
    method: "GET",
    url: "/resource/action/user_contact_list.php",   // todo: создать файл
    data: {
      "id": params["id"]
    },
    success: function(result){ // возвращает объект json
      result = JSON.parse(result);

      let context;

      $.each(result, function(index, value){
        context += `<button class="list-item" id="${value.id}" onClick=openContact(this.id)>${value.name}</button>`;
      });

      $('#main').html(context);

      // если result = false -> $(this).html('');
      //todo: завершение анимации загрузки
    }
  });
}

function openChat(id){
  $("#input-area").slideDown(200);
  $("#btn-chat-about").show(200);
  
  // todo: загрузка данных чата в блок информации
  
  $("#tab-name").html('мои контакты');
}

function openContact(id){
  $("#input-area").slideDown(200);
  $("#btn-chat-about").show(200);
  
  // todo: загрузка данных о пользователе
  
  $("#tab-name").html('имя контакта');
}

function genMessage(id_message, author, text, date){
  let itsMine = (author == myName);

  let result =
  `<div class='msg-area' id='message${id_message}'>
    <div class='msg-container ${(itsMine)?"mine":"not-mine"}'>
      ${(itsMine)?"":"<div class='msg-author-name'>"+author+"</div>"}
      <div class='msg-date'>${date}</div>
      <textarea class='msg-text' readonly>${text}</textarea>
    </div>
  </div>`;

  $("#main").append(result);
}

function sendMessage() {
  $('textarea#textbox').prop("disabled", true );
  $("button#send-message").addClass("Verification").removeClass("Idle");
  
  $.ajax({
      method: "POST",
      url: "/resource/action/send_message.php",
      data: {
        "chat": params["id"],
        "text": $("#textbox").val()
      },
      // result возвращает JSON объект. Если с ошибкой, то присутствует result.error, иначе объект с ид сообщения и датой
      success: function(result){
        result = JSON.parse(result);
        
        $("button#send-message").removeClass("Verification");

        if (!result.error){
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
          },500);
          
          // добавление сообщения в html

          var msg = $(genMessage(result.message_id, myName, $('textarea#textbox').val(), result.date)).hide();
          $('#main').append(msg);
          msg.slideDown(100);
          var textBox = msg.children().last().children().last();
          textBox.scrollTop(textBox.scrollHeight);
          var scrollHeight = textBox.scrollTop() + textBox.height();
          textBox.scrollTop(0);
          textBox.animate({height:scrollHeight},500);

          $("div#wrapper").animate({scrollTop:$("div#wrapper")[0].scrollHeight+scrollHeight},500);
        
          $('textarea#textbox').val('').prop("disabled", false).animate({height:'38px'},200);
        }else{
          $("button#send-message").addClass("Invalid");
          setTimeout(function() {
            $("button#send-message").removeClass("Invalid").addClass("Idle");
            $('textarea#textbox').prop("disabled", false);
          },500)
        }
      }
  });
  
  // код ниже просто для первичного представления         !!! закоментить при раскоменчивании верхнего !!!

  /*setTimeout(function() {
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

      let msg = $(genMessage("message-id-in-bd","", $('textarea#textbox').val(),"date")).hide();
      $('#main').append(msg);
      msg.slideDown(100);
      let textBox = msg.children().last().children().last();
      textBox.scrollTop(textBox.scrollHeight);
      let scrollHeight = textBox.scrollTop() + textBox.height();
      textBox.scrollTop(0);
      textBox.animate({height:scrollHeight},500);

      $("div#wrapper").animate({scrollTop:$("div#wrapper")[0].scrollHeight+scrollHeight},500);
      
      $('textarea#textbox').val('').prop("disabled", false).animate({height:'38px'},200);
    }else{
      $("button#send-message").addClass("Invalid");
      setTimeout(function() {
        $("button#send-message").removeClass("Invalid").addClass("Idle");
        $('textarea#textbox').prop("disabled", false);
      },500)
    }
  }, 2000);*/
}