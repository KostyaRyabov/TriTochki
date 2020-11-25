var myID = 0;
var myName = "";

var profile_data = {};

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
  if(params["id"] > 0) showInfoBox();

  $('#textbox').autoHeight();

  $("body").on("click","button.input-edit",function(){
    $(this).hide(150, function(){
        let input_div = $(this).parent();
        
        let btns = $("<button class='input-cancel'>✘</button><button class='input-submit'>✔</button>").hide();
        input_div.append(btns);
        btns.show(150);
        
        input_div.find('span').attr('contenteditable','true');
        $(this).remove()
    });
  });

  $("body").on("click","button.input-cancel",function(){
      let input_div = $(this).parent();
      let span = input_div.find('span');
      
      input_div.find('button').hide(150, function(){$(this).remove()});

      setTimeout(function(){
          span.attr('contenteditable','false').html(profile_data[span.attr('id')]);
          
          let btn = $("<button class='input-edit'>🖉</button>").hide();
          input_div.append(btn);
          btn.show(150);
      },150)
  });

  $("body").on("click","button.input-submit",function(){
      let input_div = $(this).parent();
      let span = input_div.find('span');
      let id = span.attr('id');

      span.attr('contenteditable','false');
      input_div.find('button').hide(150, function(){$(this).remove()});
      
      setTimeout(function(){
          let btn = $("<button class='input-edit'>🖉</button>").hide();
          input_div.append(btn);
          btn.show(150);
        
          // Изменение информации о пользователе
          $.ajax({
            method: "POST",
            url: "/resource/action/change_user_info.php",
            data: {
              "field": id,
              "data": span.text()
            },
            success: function(result){ // result возвращает пустое значение в случае успеха или ошибку
              // На всякий случай редирект на логин, если не тот пользователь или истекла кука
              if(result.length > 1) location.href = "/Login.html";
  
              profile_data[id] = span.text();
            }
          });
      },150);
  });

  $("body").on("click","button.error-message",function(){
    $(this).fadeOut(300, function(){ 
      $(this).remove();
    });
  });

  $('#textbox').on("input", function(){
    if ($(this).val()){
      if ($("button#send-message").is(":hidden")){
        $("button#send-message").show(50);
      }
    }else{
      if (!$("button#send-message").is(":hidden")){
        $("button#send-message").hide(100);
      }
    }
  });

  $('body').on('keydown paste', "span[contentEditable=true][maxlength]", function (event) {
    if ($(this).text().length >= $(this).attr('maxlength') && event.keyCode != 8) {
        event.preventDefault();
    }
});
  
  // Проверка на авторизацию
  $.ajax({
    method: "POST",
    url: "/resource/action/check.php",
    success: function(result){ // result возвращает данные о текущем пользователе или 0 соответственно
      if(result == 0) location.href = "/Login.html";
      else{
        result = JSON.parse(result);
        
        myID = result.id
        myName = result.myName;
        
        profile_data["First_Name"] = result.firstName;
        profile_data["Second_Name"] = result.lastName;
        profile_data["Login"] = result.login;
        profile_data["Email"] = result.email;
        profile_data["Sex"] = result.sex;
        
        $("#show-my-profile").attr("onclick", "showProfileContext(" + result.id + ")");

        

        // Если указан параметр id в url, то получаем чат и все сопутствующие данные
        if(params["id"] > 0){
          $.ajax({
            method: "GET",
            url: "/resource/action/get_chat.php",
            data: {
              "id": params["id"]
            },
            success: function(result){ // возвращает объект json
              result = JSON.parse(result);
            
              let first_unread = 0; // id первого попавшегося непрочитанного сообщения
            
              // Вывод информации о чате
              $("#tab-name").text(result.name);
              $("#chat-info-name").text(result.name);
              $("#chat-create-date").text(result.date);
              $("#chat-info-contact-list").html(""); // Сперва очищаем от значений по умолчанию
              $.each(result.users, function(id, value){
                $("#chat-info-contact-list").append("<button class='list-item' onClick='showProfileContext(" + id + ")'>" + value + "</button>");
              });
            
              // Вывод сообщений

              $.each(result.messages, function(id, value){
                $("#main").append(genMessage(id, value["user"], result.users[value["user"]], value["text"], value["date"]));
                if(first_unread == 0 && value["is_read"] == 0) first_unread = id;
              });
            
              // Если есть непрочитанное сообщение, скроллим до него
              if(first_unread){
                $('#wrapper').animate({
                  scrollTop: $('#message' + first_unread).offset().top
                }, 300);
              }
            }
          });
        } else{ // Если нет параметра для чата, выводим "главную" страницу
          hideInfoBox();
          $("#tab-name").css("padding", "10px 0").text("Главная страница");
          $("#textbox").remove();
        }
      }
    }
  });
});

function showInfoBox(){
  if (!$("#btn-chat-about").length){
    let obj = $(`<button id="btn-chat-about" class='btn modal-window-trigger' onclick="showModalWindow('#chat-contacts')">?</button>`).hide();
    $('.tab').append(obj);
    obj.show(200);
  }

  if (!$("modal-window-wrapper").length){
    $('body').append(`
      <div id="chat-contacts" class="modal-window-wrapper">
        <div class="block-screen modal-window-trigger" onclick="hideModalWindow('#chat-contacts')"></div>
        <div id="info-box" class="modal-window">
          <div class="input">
            <span id="chat-info-name" class="chat-info-header" contentEditable="false" placeholder="Chat name" maxlength="64"></span>
            <button class="input-edit">🖉</button>
          </div>
          <hr/>
          <span id="chat-create-date"></span>
          <br/>
          <br/>
          <span class="chat-info-header">контакты:</span>
          <div id="chat-info-contact-list">
          </div>
        </div>
      </div>
    `);

    $('#chat-contacts').hide();
  }
}

function hideInfoBox(){
  if ($("#btn-chat-about").length){
    $("#btn-chat-about").hide(200,function(){
      $(this).remove();
    });
  }

  if ($("#chat-contacts").length){
    $("#chat-contacts").hide(200,function(){
      $(this).remove();
    });
  }
}

function hideProfileContext(){
  $.when(hideModalWindow('#profile-form')).done(function(){
    $('#profile-form').remove();
  });
}

function showProfileContext(id){
  $.ajax({ // Берем нужного пользователя
    method: "POST",
    url: "/resource/action/get_user.php",
    data: {
      "id": id
    },
    success: function(result){ // result возвращает данные о пользователе или 0 соответственно
      if(result == 0) return false;
      
      result = JSON.parse(result);    // todo: убрать из result атрибут thisName (лишний)
      
      let itsMe = false; // Флаг текущего пользователя
      if(myID === id) itsMe = true;
  
      //todo подумать, безопасна ли такая реализация, если в переменной сначала был текущий пользователь, а теперь там тот, кого получили
      profile_data["First_Name"] = result.firstName;
      profile_data["Second_Name"] = result.lastName;
      profile_data["Login"] = result.login;
      profile_data["Email"] = result.email;
      profile_data["Sex"] = result.sex;

      let form = `
      <div id="profile-form" class="modal-window-wrapper">
        <div class="block-screen modal-window-trigger" onclick="hideProfileContext()"></div>
        <div class="modal-window">
          <div>
            <div id="profile-box" class="input">
              <span contentEditable="false" placeholder="First Name" id="First_Name" maxlength="32">${profile_data["First_Name"]}</span>`
              if (itsMe) form += `<button class="input-edit">🖉</button>`
            form += `</div>
            <div class="input">
              <span contentEditable="false" placeholder="Second Name" id="Second_Name" maxlength="32">${profile_data["Second_Name"]}</span>`
              if (itsMe) form += `<button class="input-edit">🖉</button>`
            form += `</div>
          </div>
          <div class="input">
            <span contentEditable="false" placeholder="Login" id="Login" maxlength="32">${profile_data["Login"]}</span>`
            if (itsMe) form += `<button class="input-edit">🖉</button>`
          form += `</div>
          <div class="input">
            <span contentEditable="false" placeholder="Email" id="Email" maxlength="32">${profile_data["Email"]}</span>`
            if (itsMe) form += `<button class="input-edit">🖉</button>`
          form += `</div>
          <div class="input">
            <span contentEditable="false" placeholder="Description" id="Description" maxlength="255"></span>`
            if (itsMe) form += `<button class="input-edit">🖉</button>`
          form += `</div>`
          if (itsMe) form += `
          <button onclick="changePassword()" class="input">change password</button>
          <select id="Sex" class="input">
            <option value="m">М</option>
            <option value="w">W</option>
          </select>`
          else form += `<div class="input"><span id="Sex">пол: ${profile_data["Sex"]}</span></div>`;
        form += `</div>
      </div>`;
    
      $('body').append(form);
      $("#profile-form").hide();
      showModalWindow('#profile-form');
    }
  });
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

function genMessage(id_message, author_id, author_name, text, date){
  let itsMine = (author_id == myID);

  return `
  <div class='msg-area' id='message${id_message}'>
    <div class='msg-container ${((itsMine)?"mine":"not-mine")}'>
      ${((itsMine)?"":"<div class='msg-author-name'>"+author_name+"</div>")}
      <div class='msg-date'>${date}</div>
      <span class='msg-text'>${text}</span>
    </div>
  </div>`;
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

            if ($.trim($('#textbox').val())){
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

          msg = $('#main').append(genMessage(result.message_id, myID, myName, $('#textbox').val(), result.date)).children(':last').hide().slideDown(500);
          $("div#wrapper").animate({scrollTop:$("div#wrapper")[0].scrollHeight+$("div#wrapper")[0].scrollHeight},500);
          
          $('#textbox').val('').prop("disabled", false).animate({height:'38px'},200);
        }else{
          $("button#send-message").addClass("Invalid");
          setTimeout(function() {
            $("button#send-message").removeClass("Invalid").addClass("Idle");
            $('#textbox').prop("disabled", false);
          },500)

          $('#wrapper').append(`<button class='btn error-message'>${result.error}</button>`).hide().fadeIn(300);
          setTimeout(function() {
            $('#wrapper > .error-message').fadeOut(1000, function(){ 
              $(this).remove();
            });
          },5000)
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

        if ($.trim($('#textbox').val())){
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

      msg = $('#main').append(genMessage("message-id-in-bd","", $('#textbox').html(),"date")).children(':last').hide().slideDown(500);
      $("div#wrapper").animate({scrollTop:$("div#wrapper")[0].scrollHeight+$("div#wrapper")[0].scrollHeight},500);
      
      $('#textbox').val('').prop("disabled", false).animate({height:'38px'},200);
    }else{
      $("button#send-message").addClass("Invalid");
      setTimeout(function() {
        $("button#send-message").removeClass("Invalid").addClass("Idle");
        $('#textbox').prop("disabled", false);
      },500)
    }
  }, 2000);*/
}