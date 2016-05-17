$(document).ready(function(){
	$('#print-report').click(function(){
        var $main = $('table tr').clone();
        var total = ($main.length);
        var $first = $main.slice(5, 30);
        var $second = $main.slice(30, total);
        var $third = $main.slice(total);

        var tr_chunks = _.chunk($second, 28);

        $('#table-dummy').append($first);
        $first.wrapAll('<table class="table first-table"></table>');
        $first.find('th').css({'border':0});
        $('.first-table tr.active > th').css({'border':'1px solid #000'});

        for(x=0; x<tr_chunks.length; x++){
        	$('#table-dummy').append(tr_chunks[x]);
        	$(tr_chunks[x]).wrapAll('<table class="table chunked"></table>');
        }

        $('#table-dummy').append($third);
        $third.wrapAll('<table class="table" style="font-size:10px"></table>');
        $third.find('th').css({'border':'1px solid #000'});

        $('.chunked').prepend($main.slice(4,5));
        $('.chunked tr.active > th').css({'border':'1px solid #000'});
        $('._ > td').css({'border':0});

        $('.first-table .active th, .chunked .active th').each(function(){
            $(this).attr('width', '40%');
        });
        $('.first-table .active th:first-child, .chunked .active th:first-child').attr('width', '10%');
        $('.first-table .active th:nth-child(5), .chunked .active th:nth-child(5)').attr('width', '10%');
        $('.first-table .active th:nth-child(4), .chunked .active th:nth-child(4)').attr('width', '10%');
        $('.first-table .active th:nth-child(2), .chunked .active th:nth-child(2)').attr('width', '15%');
        $('.chunked td, .first-table td, .first-table span, .chunked span').css({'padding':'3px 3px 3px 3px', 'font-size':'10px'});

        $('#table-dummy').removeClass('hidden').print().addClass('hidden').empty();
    });
});