$(function () {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };
    $.fn.loadify = function (command) {
        if (command === "enable") {
            this.append("<div class='overlay'></div><div class='loading-img'></div>");
        } else {
            this.find("div.overlay").remove();
            this.find("div.loading-img").remove();
        }
        return this;
    };

    $.fn.promixDeleteItem = function (params) {
        $(".popover").popover("destroy");
        $(this).popover({
            placement: typeof (params.dialog_placement) === 'undefined' ? 'auto' : params.dialog_placement,
            html: true,
            title: 'Delete this item?',
            trigger: 'focus',
            content: '<div class="btn-toolbar"><button data-loading-text="Deleting.." class="btn btn-danger btn-confirm">Yes</button><button class="btn btn-default popover-dismiss">No</button></div>'
        }).on('shown.bs.popover', function () {
            var $popup = $(this);
            $(this).next('.popover').find('.popover-dismiss').click(function (e) {
                $popup.popover('destroy');
            });
            $(this).next('.popover').find('.btn-confirm').click(function (e) {
                var btn = $(this);
                btn.addClass("disabled");
                $.post(params.url, {pk: params.pk})
                .done(function (data) {
                    var response = data;
                    if (typeof params.alreadyJSON === 'undefined') {
                        response = $.parseJSON(data);
                    }
                    if (!response.error_flag) {
                        btn.closest("tr").remove();
                    } else {
                        btn.parent().find("span").remove();
                        btn.parent().append("<span class='text-danger'><br/>" + response.message + "</span>");
                        btn.removeClass("disabled");
                    }
                }).fail(function () {
                    btn.parent().find("span").remove();
                    btn.parent().append("<span class='text-danger'><br/>Internal Server Error!</span>");
                    btn.removeClass("disabled");
                });
            });
        }).popover("show");
    };
    $(document).ready(function () {
        /*SHOW NOTIFICATION*/
        $.each($('[data-gritter]'), function () {
            var notif = $.parseJSON($(this).attr('data-gritter'));
            if (!notif || Object.keys(notif).length === 0) {
                return;
            }
            if (notif.error_flag) {
                $.growl.error({title: 'Error!', message: notif.message});
            } else {
                $.growl.notice({title: 'Success', message: notif.message});
            }
        });
    });
}(jQuery));