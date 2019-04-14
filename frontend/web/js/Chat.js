class Chat {
    constructor(elemId) {
        this.elemId = elemId;
        this._init();
    }

    _init() {
        this.task = this._findGetParam('id');

        let socket = new WebSocket('ws://localhost:8080?room=task_' + this.task);
        socket.onmessage = event => {
            this._renderMessage(JSON.parse(event.data));
        };

        this.socket = socket;

        $(window).on("beforeunload", () => {
            this._saveUserHistory();
            this.socket.onclose = function () {}; // disable onclose handler first
            this.socket.close()
        });

        let $container = $(`#${this.elemId}`);

        this._render($container);

        $.ajax({
            url: 'http://project-a:8888/chat/get-data',
            type: 'post',
            data: {
                task: this.task
            },
            dataType : "json",
            success: result => {
                this.user = result.user;
                this._renderHistory(result);
            }
        });
    }

    _saveUserHistory() {
        let $lastMessageId = $('.msg').not('.not-read').last().data('id');
        let data = {
            id_message: $lastMessageId,
            id_user: this.user.id,
            id_task: this.task,
        };

        $.ajax({
            url: 'http://project-a:8888/chat/add-history',
            type: 'post',
            data: data,
            dataType : "json",
        });
    }

    _render($container) {
        let $row = $("<div class='row'></div>");
        let $messagesContainer = $("<div class='message-wrap col-lg-12'></div>");
        let $messageWrap = $("<div class='msg-wrap' id='msg-wrap'></div>");
        $messageWrap.appendTo($messagesContainer);

        let $messageBlock = $("<div class='send-wrap'></div>");
        let $message = $("<textarea class='form-control send-message' id='message' rows='3' placeholder='Write a reply...'></textarea>\n");
        $message.on('click', () => {
            $('.not-read').removeClass('not-read');
        });
        $message.appendTo($messageBlock);
        $messageBlock.appendTo($messagesContainer);

        let $btnBlock = $("<div class='btn-panel'>");
        let $btn = $('<a/>', {
            class: 'col-lg-4 text-right btn send-message-btn pull-right',
            role: 'button',
            text: 'Send Message'
        });

        $btn.on('click', event => {
            this._addMessage();
            event.preventDefault();
        });

        $btn.appendTo($btnBlock);
        $btnBlock.appendTo($messagesContainer);

        $messagesContainer.appendTo($row);
        $row.appendTo($container);
    }

    _renderHistory(data) {
        let lastViewMsg = data.lastViewMsg || data.messages.length;
        console.log(lastViewMsg);
        let $container = $('#msg-wrap');
        data.messages.forEach(message => {
            let $message = new _Message(message.id, message.message, message.users.username, message.time).render();
            if (message.id > lastViewMsg) {
                $message.addClass('not-read');
            }
            $container.append($message);
        });
        let $chat = $('.msg-wrap');
        $chat.scrollTop($chat.prop('scrollHeight'));
    }

    _addMessage() {
        let message = $('#message').val();
        let data = {
            task: this.task,
            room: 'task_' + this.task,
            message: message,
            userId: this.user.id,
            userName: this.user.username,
            time: Date.now() / 1000 | 0,
        };

        $.ajax({
            url: 'http://project-a:8888/chat/add-message',
            type: 'post',
            data: data,
            dataType : "json",
            success: result => {
                if (result.status == 1) {
                    data.id = result.id;
                    this._renderMessage(data);
                    this.socket.send(JSON.stringify(data));
                    $('#message').val('')
                }
            }
        });
    }

    _findGetParam(param) {
        let result = null, tmp = [];
        let items = location.search.substr(1).split("&");
        for (let index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === param) result = decodeURIComponent(tmp[1]);
        }
        return result;
    }

    _renderMessage(data) {
        let $message = new _Message(data.id, data.message, data.userName, data.time).render();
        let $chat = $('.msg-wrap');
        $message.appendTo($chat);
        $chat.scrollTop($chat.prop('scrollHeight'));
    }

}