$(document).ready(function(){
    var socket = io('localhost:3000'),
        token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwibG9naW5fdXNlcm5hbWUiOiJoY2IiLCJmdWxsbmFtZSI6IlRoZSBGbGFzaCIsImxvZ2luX3R5cGUiOiJzdSIsImlhdCI6MTQ2NTE4NTExOH0.UJEuRDPbBlnqjbuKDjddsLMByD6uLakSuX9kJoVGpC0",
        user_id,
        message,
        message_counter=0,
        opened_message_counter=0;

    $('#chat-message-counter').attr('title', message_counter+' New Messages').text(message_counter);

    function emit(eventName, data){
        var payload = data || {};
        payload['token'] = token;
        payload['excludeSelf'] = true;
        socket.emit(eventName, payload);
    }

    emit('user.reconnect.attempt');

    socket.on('user.reconnect.success', function(response){
        emit('user.list.request.attempt');
    });

    socket.on('user.list.request.success', function(response){
        var user_dummy = $('#user-dummy').clone();
        for(x=0; x<response.data.userList.length; x++){
            var user_dummy = $('#user-dummy').clone();
            user_dummy.find('.contacts-list-name').text(response.data.userList[x].fullname);
            for(y=0; y<response.data.onlineUsers.length; y++){
                if(parseInt(response.data.onlineUsers[y]) == response.data.userList[x].id){
                    user_dummy.find('img').addClass('online');
                }
            }
            user_dummy.find('img,.chat-message-counter-individual').attr('user-id', response.data.userList[x].id).attr('message-counter', 0);
            user_dummy.removeClass('hidden');
            user_dummy.removeAttr('id');
            $('.contacts-list').append(user_dummy);
        }
    });

    socket.on('user.connected', function(response){
        $('[user-id='+response.data+']').addClass('online');
    });

    socket.on('user.disconnected', function(response){
        $('[user-id='+response.data+']').removeClass('online');
    });

    socket.on('user.message.received', function(response){
        var has_active_log = $('div[active-user-id]'); // check if element exist. will only exist if user has clicked a user to send msg.
        $('#chatAudio')[0].play();
        if(has_active_log.length != 0){
            var active_id = $('.direct-chat-messages').attr('active-user-id');
            if(active_id == response.data.senderId){   // recipient is the owner of the container logs

                if(!parseInt($('#chat-box-toggle').attr('state'))){
                    opened_message_counter++;
                    message_counter += opened_message_counter;
                    $('#chat-message-counter').attr('title', message_counter+' New Messages').text(message_counter);
                }
                var recipient_bubble = $('#chat-sender-bubble').clone();
                recipient_bubble.find('.direct-chat-timestamp').text(response.data.created_at);
                recipient_bubble.find('.direct-chat-text').text(response.data.message);
                recipient_bubble.removeClass('hidden');
                $('.chat-bubbles-container').append(recipient_bubble);
                $('.direct-chat-messages').animate({scrollTop: $('.direct-chat-messages').prop("scrollHeight")}, 500);
            }else{  // recipient does not own the container logs
                message_counter++;
                var sender_counter = parseInt($('[user-id='+response.data.senderId+']').text());
                sender_counter = (!sender_counter) ? 0 : sender_counter;
                sender_counter++;
                $('#chat-message-counter').attr('title', message_counter+' New Messages').text(message_counter);
                $('.chat-message-counter-individual[user-id='+response.data.senderId+']').attr('title', sender_counter+' New Messages').text(sender_counter).removeClass('hidden');
            }
        }else{  // no opened logs
            message_counter++;
            var sender_counter = parseInt($('[user-id='+response.data.senderId+']').text());
            sender_counter = (!sender_counter) ? 0 : sender_counter;
            sender_counter++;
            $('#chat-message-counter').attr('title', message_counter+' New Messages').text(message_counter);
            $('.chat-message-counter-individual[user-id='+response.data.senderId+']').attr('title', sender_counter+' New Messages').text(sender_counter).removeClass('hidden');
        }
    });

    socket.on('user.message.logs.success', function(response){
        for(x=0; x<response.data.length; x++){
            if(response.data[x].recipient_id == user_id){
                var recipient_bubble = $('#chat-recipient-bubble').clone();
                recipient_bubble.find('.direct-chat-timestamp').text(response.data[x].created_at);
                recipient_bubble.find('.direct-chat-text').text(response.data[x].message);
                recipient_bubble.removeClass('hidden');
                $('.chat-bubbles-container').append(recipient_bubble);
            }else{
                var recipient_bubble = $('#chat-sender-bubble').clone();
                recipient_bubble.find('.direct-chat-timestamp').text(response.data[x].created_at);
                recipient_bubble.find('.direct-chat-text').text(response.data[x].message);
                recipient_bubble.removeClass('hidden');
                $('.chat-bubbles-container').append(recipient_bubble);
            }
        }
        $('.direct-chat-messages').animate({scrollTop: $('.direct-chat-messages').prop("scrollHeight")}, 500).attr('active-user-id', user_id);
        $('.box-footer').removeClass('hidden');
    });

    socket.on('user.message.send.success', function(response){
        var recipient_bubble = $('#chat-recipient-bubble').clone();
        recipient_bubble.find('.direct-chat-timestamp').text(new Date().toLocaleString());
        recipient_bubble.find('.direct-chat-text').text(message);
        recipient_bubble.removeClass('hidden');
        $('.chat-bubbles-container').append(recipient_bubble);
        $('.direct-chat-messages').animate({scrollTop: $('.direct-chat-messages').prop("scrollHeight")}, 500);
        $('[name=message]').val('');
    });

    $('ul').on('click', '.user_click', function(e){
        e.preventDefault();
        $('.direct-chat-primary').removeClass('direct-chat-contacts-open');
        $('.chat-bubbles-container').empty();

        user_id = $(this).find('img').attr('user-id');
        var user_name = $(this).find('.contacts-list-name').text();
        var user_message_counter = parseInt($('.chat-message-counter-individual[user-id='+user_id+']').text());
        user_message_counter = (!user_message_counter) ? 0 : user_message_counter;
        message_counter = message_counter - user_message_counter;
        $('.chat-message-counter-individual[user-id='+user_id+']').attr('title', '0 New Messages').text(0).addClass('hidden');
        $('#chat-message-counter').attr('title', message_counter+' New Messages').text(message_counter);
        $('#chat-select-user,#recipient-title').text(user_name);
        emit('user.message.logs', {token: token, data:{recipientId:user_id}});
    });

    $('#send-message').submit(function(e){
        e.preventDefault();
        message = $('[name=message]').val();
        emit('user.message.send', {token: token, message:{recipientId:user_id, content:message}});
    });

    $('#chat-box-toggle').click(function(e){
        e.preventDefault();
        var curr_state = parseInt($(this).attr('state'));
        if(opened_message_counter != 0){
            message_counter = message_counter - opened_message_counter;
            opened_message_counter = 0;
            $('#chat-message-counter').attr('title', message_counter+' New Messages').text(message_counter);
        }
        $(this).attr('state', ((curr_state) ? 0 : 1));
    });
})