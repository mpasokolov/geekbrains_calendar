class _Message {
    constructor(id, message, username, time) {
        this.id = id;
        this.message = message;
        this.username = username;
        this.time = time;
    }

    render() {
        let $container = $("<div class='media msg'>\n").attr('data-id', this.id);
        let $avatarBlock = $("<a class='pull-left' href='#'></a>");
        let $avatarImage = $("<img class='media-object' data-src='holder.js/64x64' src='https://via.placeholder.com/32' alt='64x64\' style='width: 32px; height: 32px;'>\n");

        $avatarImage.appendTo($avatarBlock);

        $avatarBlock.appendTo($container);

        let $messageBlock = $("<div class='media-body'></div>");
        let date = new Date(this.time * 1000);
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let seconds = date.getSeconds();
        seconds = seconds < 10 ? '0' + seconds : seconds;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        hours = hours < 10 ? '0' + hours : hours;
        let time = hours + ':' + minutes + ':' + seconds;
        let $time = $(`<small class='pull-right time'><i class='fa fa-clock-o'></i>${time}</small>\n`);
        let $name = $(`<h5 class='media-heading'>${this.username}</h5>`);
        let $text = $(`<small class="col-lg-10 message-text">${this.message}</small>`);

        $time.appendTo($messageBlock);
        $name.appendTo($messageBlock);
        $text.appendTo($messageBlock);

        $messageBlock.appendTo($container);

        return $container;
    }
}