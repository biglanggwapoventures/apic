$(document).ready(function () {
    $("a.request-lock-state").click(function () {
        var $this = $(this);
        var $request_state = $this.attr("data-request");
        $.post($("tbody").attr('data-unlock-url'), {request_state: $request_state, pk: $this.closest("tr").attr("data-pk")}).done(function (data) {
            if (!data.error_flag) {
                $this.find('span').toggleClass('bg-orange bg-green');
                $this.find('i').toggleClass('fa-unlock fa-lock');
                $this.attr('data-request', function (index, value) {
                    return value === 'do_unlock' ? 'do_lock' : 'do_unlock';
                });
            } else {
                alert(data.message);
            }
        }).fail(function () {
            alert('Internal server error.');
        });
    });
    
    $('.tbody-item-remove').click(function() {
        $(this).promixDeleteItem({
            url: $("tbody").attr("data-delete-url"),
            pk: $(this).closest("tr").attr("data-pk"),
            dialog_placement: 'left'
        });
    });

    $('.tbody-item-view').click(function () {
        $.get($('input[name=data-url-generate]').val(), {id: $(this).attr('data-pk')}).done(function (data) {
            var printpage = window.open('','','width=800,height=800');
            printpage.document.write(data);
            printpage.document.close();
            printpage.focus();
        }).fail(function () {
            alert('Internal Server Error: Please contact developer.');
        });
    });
    
    $('.tbody-item-approve').click(function () {
        var action = $(this);
        $.get($('input[name=data-url-approve]').val(), {id: action.attr('data-pk')}).done(function (data) {
            action.closest("tr").removeClass('danger');
            action.closest("tr").children().eq(4).html('Approved');
            action.closest("td").children().eq(0).show().children().removeClass('bg-gray').addClass('bg-teal');
            action.closest("td").children().eq(1).hide();
        }).fail(function () {
            alert('Internal Server Error: Please contact developer.');
        });
    });
    
    //print
    $('.tbody-item-print').click(function () {
        $.get($('input[name=data-url-generate]').val(), {id: $(this).attr('data-pk')}).done(function (data) {
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