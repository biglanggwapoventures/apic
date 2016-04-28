$('#print-report').click(function(){
    $('#table-dummy').append($('#table-header').clone()).append('<br/>');

    var $main = $('#aorr tr').clone();
    var $first = $main.slice(0, 13);
    var $second = $main.slice(13);
    var tr_chunks = _.chunk($second, 13);
    var header = $main.slice(1,2);

    $('#table-dummy').append($first);
    $first.wrapAll('<table class="table table-bordered first-table" style="width:100%"></table>');
    $('.first-table .borderless').remove();
    $('.first-table').prepend(header);
    $('.first-table #remove-me').remove();

    for(x=0; x<tr_chunks.length; x++){
    	$('#table-dummy').append(tr_chunks[x]);
    	$(tr_chunks[x]).wrapAll('<table class="table table-bordered chunked" style="width:100%"></table>');
    }

    $('.chunked').prepend(header.clone());
    $('.chunked .borderless').remove();
    $('.chunked #remove-me').remove();

    $('.first-table .active td:first-child').attr('width', '30%');
    $('.chunked .active td:first-child').attr('width', '30%');
    $('.first-table .active td:nth-child(6)').attr('width', '20%');
    $('.chunked .active td:nth-child(6)').attr('width', '20%');

    $('.first-table tbody tr:not(:first-child)').removeClass('b').css('font-weight', 'normal');
    $('.chunked tbody tr:not(:first-child)').removeClass('b').css('font-weight', 'normal');

    $('#table-dummy').removeClass('hidden').print().addClass('hidden').empty();
});