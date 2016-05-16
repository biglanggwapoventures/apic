$(document).ready(function(){
	$('#print-report').click(function(){
        var $main = $('table tr').clone();
        var total = ($main.length)-4;
        var $first = $main.slice(5, 24);
        var $second = $main.slice(24, total);
        var $third = $main.slice(total);

        var tr_chunks = _.chunk($second, 17);

        $('#table-dummy').append($first);
        $first.wrapAll('<table class="table first-table"></table>');
        $first.find('th').css({'border':0});
        $('.first-table tr.active > th').css({'border':'1px solid #000'});

        for(x=0; x<tr_chunks.length; x++){
        	$('#table-dummy').append(tr_chunks[x]);
        	$(tr_chunks[x]).wrapAll('<table class="table chunked"></table>');
        }

        $('#table-dummy').append($third);
        $third.wrapAll('<table class="table"></table>');
        $third.find('th').css({'border':'1px solid #000'});

        $('.chunked').prepend($main.slice(5,6));
        $('.chunked tr.active > th').css({'border':'1px solid #000'});
        $('._ > td').css({'border':0});

        $('.first-table .active th, .chunked .active th').each(function(){
            $(this).attr('width', '30%');
        });
        $('.first-table .active th:first-child, .chunked .active th:first-child').attr('width', '10%');
        $('.first-table .active th:nth-child(7), .chunked .active th:nth-child(7)').attr('width', '40%');
        $('.first-table .active th:nth-child(4), .chunked .active th:nth-child(4)').attr('width', '10%');
        $('.first-table .active th:nth-child(2), .chunked .active th:nth-child(2)').attr('width', '15%');
        $('.chunked td, .first-table td, .first-table th, .chunked th').css({'padding':'3px', 'font-size':'10px'});

        $('#table-dummy').removeClass('hidden').print().addClass('hidden');
    });
});