$(document).ready(function(){
    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
    $('table').stickyTableHeaders({fixedOffset: $('.content-header')});

    $('#print-report').click(function(){
        var $main = $('table tr').clone();
        var total = ($main.length)-5;
        var $first = $main.slice(5, 31);
        var $second = $main.slice(31, total);
        var $third = $main.slice(total);

        var tr_chunks = _.chunk($second, 31);

        $('#table-dummy').append($first);
        $first.wrapAll('<table class="table first-table"></table>');
        $first.find('th').css({'border':0});
        $('.first-table tr.active > th').css({'border':'1px solid #ddd'});

        for(x=0; x<tr_chunks.length; x++){
        	$('#table-dummy').append(tr_chunks[x]);
        	$(tr_chunks[x]).wrapAll('<table class="table chunked" style="width:100%"></table>');
        }

        $('#table-dummy').append($third);
        $third.wrapAll('<table class="table"></table>');
        $third.find('th').css({'border':0});

        $('.chunked').prepend($main.slice(5,6));
        $('.chunked tr.active > th').css({'border':'1px solid #ddd'});
        $('._ > td').css({'border':0});

        $('.first-table .active th, .chunked .active th').each(function(){
            $(this).attr('width', '10%');
        });
        $('.first-table .active th:first-child, .chunked .active th:first-child').attr('width', '10%');
        $('.chunked, .first-table').css({'font-size': '10px'});
        $('.chunked th span, .first-table th span').css({'font-size':'10px'});
        $('.chunked td, .first-table td').css({'padding':'3px'});

        $('#table-dummy').removeClass('hidden').print().addClass('hidden');
    });
})