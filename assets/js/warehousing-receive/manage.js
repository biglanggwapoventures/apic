$(document).ready(function () {
    $('.datetimepicker').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm tt'
    }).datetimepicker('setDate', (new Date()));
    $('tbody').on('click', '.remove-line', function () {
        var $line = $(this).closest('tr');
        $line.hasClass('no-remove') ? void(0) : $line.remove();
    });
    $('.add-line').click(function () {
        var clone = $('tbody tr:first').clone();
        clone.removeClass('no-remove').find('input,select').val('');
        $('tbody').append(clone);
    });
    $('form').submit(function () {
        $.post($(this).attr('action'), $(this).serialize()).done(function (data) {
            if (!data.error_flag) {
                
                window.location.href = $('input[name=redirect-url]').val();
            }else{
                console.log(data);
            }

        }).fail(function () {
            alert('Internal Server Error: Please contact developer.');
        });
        return false;
    });
    //print
    $('.print-doc').click(function () {
        $.get($('input[name=data-url-for-printing]').val(), {id: $(this).attr('data-pk')}).done(function (data) {
            var printpage = window.open();
            printpage.document.write(data);
            printpage.document.close();
            printpage.focus();
            printpage.print();
            printpage.close();
        }).fail(function () {
            alert('Internal Server Error: Please contact developer.');
        });
    });
});
