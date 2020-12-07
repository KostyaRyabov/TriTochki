/*!
  \file
  \brief Основная страница
*/


///	\brief Идентификатор создателя рассматриваемого чата
var idOwner = -1;

///	\brief Идентификатор рассматриваемого чата
var idChat = -1;

///	\brief Идентификатор текущего пользователя
var myID = -1;

///	\brief Имя текущего пользователя
var myName = "";

///	\brief Выбранные элементы
var selected = [];

///	\brief Структура контакта
var profile_data = {};

///	\brief GET-параметры
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


/// \cond
$(document).ready(init);
/// \endcond

$(document).on("click", ".logo", function(){
  location.href = "/";
});

/*!
  \brief Отображение кнопок для редактирования параметра

  \defgroup editParam Редактирование параметров
    @{
*/
function showEditButtons()
{
  $(this).hide(150, function(){
    let input_div = $(this).parent();
    
    let btns = $("<button class='input-cancel icon-cancel'></button><button class='input-submit icon-check'></button>").hide();
    input_div.append(btns);
    btns.show(150);
    
    input_div.find('span').attr('contenteditable','true');
    $(this).remove()
  });
}

/// \brief Отказ от изменений параметра
function inputCancel()
{
  let input_div = $(this).parent();
  let span = input_div.find('span');
  
  input_div.find('button').hide(150, function(){$(this).remove()});

  setTimeout(function(){
      span.attr('contenteditable','false').html(profile_data[span.attr('id')]);
      
      let btn = $("<button class='input-edit icon-pencil-1'></button>").hide();
      input_div.append(btn);
      btn.show(150);
  },150)
}

/// \brief Подтверждение изменений параметра
function inputSubmit()
{
  let input_div = $(this).parent();
  let span = input_div.find('span');
  let id = span.attr('id');
  let $this = $(this);

  span.attr('contenteditable','false');
  input_div.find('button').hide(150, function(){$(this).remove()});
  
  setTimeout(function(){
    let btn = $("<button class='input-edit icon-pencil-1'></button>").hide();
    input_div.append(btn);
    btn.show(150);

    if($this.parent().parent().attr("id") == "profile-box"){
      $.ajax({
        method: "POST",
        url: "/resource/action/change_user_info.php",
        data: {
          "field": id,
          "data": span.text()
        },
        success: function(result){
          if(result.length > 1) location.href = "/Login.html";

          profile_data[id] = span.text();
        }
      });
    }

    if($this.parent().attr("id") == "chat-title"){
      $.ajax({
        method: "POST",
        url: "/resource/action/change_chat_title.php",
        data: {
          "id": params["id"],
          "name": $("#chat-info-name").text()
        },
        success: function(result){
          if(result.length > 1) return false;
          
          $("#tab-name").text($("#chat-info-name").text());
          $("title").text($("#chat-info-name").text());
        }
      });
    }
  },150);
}

///@}

/*!
  \brief Запрос удаления выбранного элемента
  \details (контакта или чата)
  
  \defgroup delContact Удаление элемента
    @{
*/
function deleteSelectedItem()
{
  selected[0] = { name: $(this).prev().text(), id: $(this).parent().attr('id') };

  let modal = `
    <div id="warning-form" class="modal-window-wrapper">
      <div class="block-screen modal-window-trigger" onclick="hideWarningMessage()"></div>
      <div class="modal-window">
        <span>Вы уверены, что хотите убрать из контактов ${selected[0].name}</span>
        <div id="warning-box">
        <button id='w-yes'>yes</button> <button id='w-no'>no</button>
      </div>
    </div>`;

  $('body').append(modal);
  $("#warning-form").hide();
  showModalWindow('#warning-form');
}

/// \brief Подтверждение удаления выбранного элемента
function deleteSubmit()
{
  $(`.myContact#${selected[0].id}`).slideUp(200,function(){
    $(this).remove();

    /// \todo добавить удаление контакта selected[0].id у myID

    if ($('.list2 > tbody').is(':empty') && !$('.list2 + #empty-list-message').length){
      let txt = 'empty'
      $('.list2').after(`<span id='empty-list-message'>${txt}</span>`)
      $('#empty-list-message').hide().show(300)
    }
  })

  $(`.myChat#${selected[0].id}`).slideUp(200,function(){
    $(this).remove();
    
    let $this = $(this);
    
    $.ajax({
      method: "POST",
      url: "/resource/action/user_chat_leave.php",
      data: {
        "chat": $this.attr("id")
      },
      success: function(result){
        if(result.length > 1) return false;
      }
    });

    if ($('.list2 > tbody').is(':empty') && !$('.list2 + #empty-list-message').length){
      let txt = 'empty';
      $('.list2').after(`<span id='empty-list-message'>${txt}</span>`);
      $('#empty-list-message').hide().show(300);
    }
  });

  hideWarningMessage()
}

/// \brief Отказ удаления выбранного элемента
function deleteCancel()
{
  hideWarningMessage()
}

///@}

// brief Отображение профиля выбранного контакта
//function getContact(){
//  showProfileContext($(this).parent().attr('id'))
//}

/// \brief Изменение размера поля ввода в соответствие с введенным текстом сообщения
function autoSize()
{
  $(this).css({ 'height': 'auto'}).height($(this)[0].scrollHeight)

  if ($(this).val()){
    if ($("button#send-message").is(":hidden")){
      $("button#send-message").show(50);
    }
  }else{
    if (!$("button#send-message").is(":hidden")){
      $("button#send-message").hide(100);
    }
  }
}

/// \brief Удаление контакта из чата
function kickContact()
{
  let $this = $(this);
  
  $.ajax({
    method: "POST",
    url: "/resource/action/chat_user_kick.php",
    data: {
      "chat_id": params["id"],
      "user_id": $(this).data("id")
    },
    success: function(result){
      if(!result.length) $this.parent().remove();
    }
  });
}

/// \brief Инициализация основной страницы
function init()
{
  $('body').hide().fadeIn(200);
  
  $("body").on("click","button.input-edit",showEditButtons);
  
  $("body").on("click","button.input-cancel",inputCancel);
  
  $("body").on("click","button.input-submit",inputSubmit);
  
  $("body").on("click",".item-action.icon-cancel",deleteSelectedItem);
  $("body").on("click",".item-action.icon-plus",function(){
    $(this).removeClass('icon-plus').addClass('icon-minus');

    addSelectedContact($(this).parent().attr('id'));
  });
  $("body").on("click",".item-action.icon-minus",function(){
    $(this).removeClass('icon-minus').addClass('icon-plus');

    removeSelectedContact($(this).parent().attr('id'));
  });
  
  $("body").on("click",".myChat-del",deleteSelectedItem);
  
  $("body").on("click","#w-yes",deleteSubmit);
  
  $("body").on("click","#w-no",deleteCancel);
  
  $("body").on("click",".myContact-name",function(){
    showProfileContext($(this).parent().attr('id'))
  });
  
  $("body").on("click",".myChat-name",function(){
    showChatContext($(this).parent().attr('id'));
    history.pushState({}, "", "?id=" + $(this).parent().attr('id'));
  });
  
  $("body").on("click","button.error-message",function(){
    $(this).fadeOut(300, function(){$(this).remove()});
  });

  $('body').on("input",'#textbox', autoSize);

  $('body').on('keydown paste', "span[contentEditable=true][maxlength]", function (event) {
    if ($(this).text().length >= $(this).attr('maxlength') && event.keyCode != 8) {
      event.preventDefault();
    }
  });

  $('body').on("click", "#chat-exit", chatExit);

  $('body').on("click", ".kick-user", kickContact);

  $('body').on("click", "#chat-add-user", addContact);

  $('body').on("click", "#show-my-contacts", function(){showContactListContext(true)});
  $('body').on("click", "#show-my-chats", function(){showChatListContext()});

  $('body').on("click", "#addContacts", submitSelectedContacts);

  authorization()
}


/*!
  \brief Добавление контактов из списка выбранных в чат
  \param[in] id Идентификатор контакта

  \defgroup selectContacts Изменение списка контактов для добавления в чат
    @{
*/
function submitSelectedContacts()
{
  /// \todo Добавить контакты в чат (БД) по списку selected (набор идентификаторов)

  // ...
  // при успешном выполнении выполнить следующее...
  showChatContext(idChat);
}

/*!
  \brief Добавление контакта в список выбранных
  \param[in] id Идентификатор контакта
*/
function addSelectedContact(curID)
{
  if (!selected.includes(curID)) {
    selected.push(curID);
  }

  if (selected.length > 0 && !$('#addContacts').length){
    $('#main').append(`<button id="addContacts">добавить</button>`);
    $('button#addContacts').hide().fadeIn(300);
  }
}

/*!
  \brief Удаление контакта из списка выбранных
  \param[in] id Идентификатор контакта
*/
function removeSelectedContact(curID)
{
  let idx = selected.indexOf(curID)
  if (curID > 0){
    selected.splice(idx, 1);
  }

  if($('#addContacts').length && selected.length == 0){
    $('#addContacts').fadeOut(300,function(){$(this).remove()});
  }
}

/// \brief Добавление контакта к текущему чату
function addContact()
{
  hideModalWindow('#chat-contacts')
  showContactListContext(false,function(){
    selected = [];

    /// \todo Написать код для добавления контактов (массив selected структур {id: value}) к текущему чату idChat
  })
}

///@}



/// \brief Выход из чата
function chatExit()
{
  $.ajax({
    method: "POST",
    url: "/resource/action/user_chat_leave.php",
    data: {
      "chat": params["id"]
    },
    success: function(result){
      if(result.length > 1) return false;
    }
  });

  $('.tab').fadeOut(300,function(){
    $('#tab-name').text("Главная страница");
    $('#btn-chat-about').remove();
  }).fadeIn(300);
  
  $('#main').fadeOut(100,function(){$(this).html('')});
  hideModalWindow('#chat-contacts')
}

///  \brief Авторизация
function authorization()
{
  $.ajax({
    method: "POST",
    url: "/resource/action/check.php",
    success: function(result){
      if(result == 0) location.href = "/Login.html";
      else{
        result = JSON.parse(result);
        
        myID = result.id;
        myName = result.myName;
        
        profile_data["First_Name"] = result.firstName;
        profile_data["Second_Name"] = result.lastName;
        profile_data["Login"] = result.login;
        profile_data["Email"] = result.email;
        profile_data["Sex"] = result.sex;
        
        $("#show-my-profile").attr("onclick", "showProfileContext(" + result.id + ")");

        if(params["id"] > 0){
          showChatContext(params["id"]);
        }
      }
    }
  });
}

/*!
  \brief Отображение сведений о чате
  \param[in] id Идентификатор рассматриваемого чата
*/
function showChatContext(id)
{
  $('#main').fadeOut(100,function(){
  
    $.ajax({
      method: "GET",
      url: "/resource/action/get_chat.php",
      data: {
        "id": id
      },
      success: function(result){
        result = JSON.parse(result);
      
        let first_unread = 0;   /// \todo Изменить номер последнего непрочитанного сообщения
        
        idChat = id;
        params["id"] = id;
        idOwner = result.owner;

        $("title").text(result.name + " | TriTochki");
        showChatInfo(result);
        $('.tab').fadeOut(300,function(){$('#tab-name').text(result.name)}).append(`<button id="btn-chat-about" class='btn modal-window-trigger' onclick="showModalWindow('#chat-contacts')">?</button>`).fadeIn(300);
        $('#main').addClass('shiftDown').html('').fadeIn(300);
        showTextBox();
        
        $.each(result.users, function(id, value){
          let el = `<button class='list-item chatContact ${(id == idOwner)?"icon-crown":""}' onClick='showProfileContext(${id})'>${value}</button>`;

          if (myID == idOwner && id != myID){
            el = `<div>${el + `<button class="icon-cancel kick-user" data-id="${id}"></button>`}</div>`;
          }

          $("#chat-info-contact-list").append(el);
        });

        $.each(result.messages, function(id, value){
          $("#main").append(genMessage(id, value["user"], result.users[value["user"]], value["text"].replace(/\n/g, '<br>'), value["date"]));
          if(first_unread == 0 && value["is_read"] == 0) first_unread = id;
        });

        if(first_unread){
          $('#wrapper').animate({
            scrollTop: $('#message' + first_unread).offset().top
          }, 300);
        }
      }
    });
  })
}

/// \brief Скрытие предупреждающего модального окна
function hideWarningMessage()
{
  hideModalWindow('#warning-form', function(){
    $('#warning-form').remove();
  });
}

/// \brief Отображение сведений о чате
function showChatInfo(data)
{
  if (!$("modal-window-wrapper").length){
    let context = `
      <div id="chat-contacts" class="modal-window-wrapper">
        <div class="block-screen modal-window-trigger" onclick="hideModalWindow('#chat-contacts')"></div>
        <div id="info-box" class="modal-window">
          <div class="input" id="chat-title">
            <span id="chat-info-name" class="chat-info-header" contentEditable="false" placeholder="Chat name" maxlength="64">${data.name}</span>`
            if (idOwner == myID) context += `<button class="input-edit icon-pencil-1"></button>`
          context += `</div>
          <hr/>
          <span id="chat-create-date">${data.date}</span>
          <br/>
          <br/>
          <span class="chat-info-header">контакты:`
          if (idOwner == myID) context += `<button id="chat-add-user" class="icon-user-plus"></button>`
          context += `</span>
          <div id="chat-info-contact-list" class="list"></div>
          <br/>
          <button id="chat-exit">выйти из чата</button>
        </div>
      </div>`
    
    $('body').append(context);

    $('#chat-contacts').hide();
  }
}

/// \brief Скрытие сведений о чате
function hideChatInfo()
{
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

/// \brief Скрытие сведений о контакте
function hideProfileContext()
{
  hideModalWindow('#profile-form', function(){
    $('#profile-form').remove();
  });
}

/*!
  \brief Отображение сведений о контакте
  \param[in] id Идентификатор рассматриваемого контакта
*/
function showProfileContext(id)
{
  $.ajax({
    method: "POST",
    url: "/resource/action/get_user.php",
    data: {
      "id": id
    },
    success: function(result){
      if(result == 0) return false;
      
      result = JSON.parse(result);
      
      let itsMe = (myID == id);
  
      profile_data["First_Name"] = result.firstName;
      profile_data["Second_Name"] = result.lastName;
      profile_data["Login"] = result.login;
      profile_data["Email"] = result.email;
      profile_data["Sex"] = result.sex;

      let form = `
      <div id="profile-form" class="modal-window-wrapper">
        <div class="block-screen modal-window-trigger" onclick="hideProfileContext()"></div>
        <div class="modal-window">
          <div id="profile-box">
            <div class="input">
              <span contentEditable="false" placeholder="First Name" id="First_Name" maxlength="32">${profile_data["First_Name"]}</span>`
              if (itsMe) form += `<button class="input-edit icon-pencil-1"></button>`
            form += `</div>
            <div class="input">
              <span contentEditable="false" placeholder="Second Name" id="Second_Name" maxlength="32">${profile_data["Second_Name"]}</span>`
              if (itsMe) form += `<button class="input-edit icon-pencil-1"></button>`
            form += `</div>
          </div>
          <div class="input">
            <span contentEditable="false" placeholder="Login" id="Login" maxlength="32">${profile_data["Login"]}</span>`
            if (itsMe) form += `<button class="input-edit icon-pencil-1"></button>`
          form += `</div>
          <div class="input">
            <span contentEditable="false" placeholder="Email" id="Email" maxlength="32">${profile_data["Email"]}</span>`
            if (itsMe) form += `<button class="input-edit icon-pencil-1"></button>`
          form += `</div>
          <div class="input">
            <span contentEditable="false" placeholder="Description" id="Description" maxlength="255"></span>`
            if (itsMe) form += `<button class="input-edit icon-pencil-1"></button>`
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

/// \brief Отображение списка чатов пользователя
function showChatListContext()
{
  hideChatInfo();
  hideTextBox();
  $("#input-area").slideUp(200);
  $("#tab-name").html('чаты');
  $('#main').fadeOut(200,function(){
    $(this).removeClass('shiftDown').html('');

    /// \todo запуск анимации загрузки

    $.ajax({
      method: "GET",
      url: "/resource/action/user_chat_list.php",
      success: function(result){
        result = JSON.parse(result);
        
        let context = "";

        $.each(result, function(id, name){
          context += `<tr class="myChat" id=${id}><td class='myChat-name'>${name}</td><td class="item-action icon-cancel"></td></tr>`;
        });
        
        context = `
          <div id="contactSearch" class="search-field">
            <input type="search" placeholder="search..."></input>
            <button>поиск</button>
          </div>
          <table class='list2'>
            <tbody>${context}</tbody>
          </table>`;

        $('#main').html(context).hide().fadeIn(200);

      /// \todo завершение анимации загрузки
      }
    });
  });
}

/// \brief Отображение списка контактов пользователя
function showContactListContext(isDelete,callback)
{
  hideChatInfo();
  hideTextBox();
  $("#input-area").slideUp(200);
  $("#tab-name").html('контакты');
  $('#main').fadeOut(200,function(){
    $(this).removeClass('shiftDown').html('')

    let context = "";

    $('#main').fadeOut(200,function(){
      /// \todo запуск анимации загрузки


      let context = "";

      context += `<tr class="myContact" id=1><td class='myContact-name'>{name}</td><td class="item-action ${(isDelete)?'icon-cancel':'icon-plus'}"></td></tr>`
      
      context = `
        <div id="contactSearch" class="search-field">
          <input type="search" placeholder="search..."></input>
          <button>поиск</button>
        </div>
        <table class='list2'><tbody>${context}</tbody></table>`;

      $('#main').html(context).hide().fadeIn(200, callback);

      /*

      $.ajax({
        method: "GET",
        url: "/resource/action/user_contact_list.php",   /// \todo создать файл
        data: {
          "id": params["id"]
        },
        success: function(result){
          result = JSON.parse(result);

          let context = "";

          $.each(result, function(id, name){
            context += `<tr class="myContact" id=${id}><td class='myContact-name'>{name}</td><td class="item-action ${(isDelete)?icon-cancel:icon-plus}"></td></tr>`
          });

          context = `
            <div id="contactSearch" class="search-field">
              <input type="search" placeholder="search..."></input>
              <button>поиск</button>
            </div>
            <table class='list2'><tbody>${context}</tbody></table>`;

          $('#main').html(context).hide().fadeIn(200, callback);
          
          /// \todo завершение анимации загрузки
        }
      });
      
      */
    });
  });
}

/*!
 \brief Генерация нового сообщения
 \param[in] id_message Идентификатор нового сообщения
 \param[in] author_id Идентификатор автора нового сообщения
 \param[in] author_name Имя автора нового сообщения
 \param[in] text Текст нового сообщения
 \param[in] date Дата создания
 \return HTML-структуру сообщения
*/
function genMessage(id_message, author_id, author_name, text, date)
{
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

/// \brief Отображение поля ввода сообщений
function showTextBox()
{
  $('table > tbody').append(`
    <tr id="footer">
      <td>
        <div id="input-area">
            <textarea id="textbox"></textarea>
            <button id="send-message" class="Idle icon-paper-plane" onclick="sendMessage()"/>
        </div>
      </td>
    </tr>`);
  $('#footer').hide().slideDown(200);
}

/// \brief Скрытие поля ввода сообщений
function hideTextBox()
{
  $('#footer').slideUp(200,function(){$(this).remove()})
}

/// \brief Отправка сообщения и его отображение
function sendMessage()
{
  $('textarea#textbox').prop("disabled", true );
  $("button#send-message").addClass("Verification").removeClass("Idle icon-paper-plane");
  
  $.ajax({
      method: "POST",
      url: "/resource/action/send_message.php",
      data: {
        "chat": idChat,
        "text": $("#textbox").val()
      },
      // result возвращает JSON объект. Если с ошибкой, то присутствует result.error, иначе объект с ид сообщения и датой
      success: function(result){
        result = JSON.parse(result);
        
        $("button#send-message").removeClass("Verification");

        if (!result.error){
          $("button#send-message").addClass("Valid icon-check");
          setTimeout(function() {
            $("button#send-message").removeClass("Valid icon-check").addClass("Idle icon-paper-plane");

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
          
          $('#main').append(genMessage(result.message_id, myID, myName, $('#textbox').val().replace(/\n/g, '<br>'), result.date)).children(':last').hide().slideDown(500);
          $("div#wrapper").animate({scrollTop:$("div#wrapper")[0].scrollHeight+$("div#wrapper")[0].scrollHeight},500);
          
          $('#textbox').val('').prop("disabled", false).animate({height:'38px'},200);
        }else{
          $("button#send-message").addClass("Invalid icon-cancel");
          setTimeout(function() {
            $("button#send-message").removeClass("Invalid icon-cancel").addClass("Idle icon-paper-plane");
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
}