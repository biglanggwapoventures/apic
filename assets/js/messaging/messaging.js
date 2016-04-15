$(function () {
    $(document).ready(function () {
        var snd = new Audio($('[data-name=notification-src]').attr('data-value')); // buffers automatically when created
        $('#user-list').slimScroll({
            height: $(window).outerHeight() - $('.navbar-static-top').outerHeight() - $('.content-header').outerHeight() - 80
        });
        $('#conversation-wrapper').slimScroll({
            height: $(window).outerHeight() - $('.navbar-static-top').outerHeight() - $('.content-header').outerHeight() - 170 - $('textarea.message-content').outerHeight()
        });
        $('textarea.message-content').css('overflow', 'hidden').autogrow();
        $('#toggle-send-button').change(function () {
            $(this).prop('checked') ? $('#btn-send').addClass('hidden') : $('#btn-send').removeClass('hidden');
        });

        $('#btn-send').click(function () {
            return false;
        });
    });
}(jQuery));