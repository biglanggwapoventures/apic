$(document).ready(function(){
    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
    $('table').stickyTableHeaders({fixedOffset: $('.content-header')});

    $('#print-report').click(function(){
        var $main = $('table tr').clone();
        var total = ($main.length)-5;
        var $first = $main.slice(7, 24);
        var $second = $main.slice(24, total);
        var $third = $main.slice(total);

        var tr_chunks = _.chunk($second, 17);

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
        $third.find('th').css({'birder':0});

        $('.chunked').prepend($main.slice(6,7));
        $('.chunked tr.active > th').css({'border':'1px solid #ddd'});
        $('._ > td').css({'border':0});

        $('#table-dummy').removeClass('hidden').print().addClass('hidden').empty();
    });
})