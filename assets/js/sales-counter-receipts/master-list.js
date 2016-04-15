$(document).ready(function () {
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