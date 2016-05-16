$(document).ready(function(){
    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
    $('table').stickyTableHeaders({fixedOffset: $('.content-header')});

    // var sample = ['sample', 'lol'];

    $('#print-report').click(function(){

        // make virtual div with table inside
        var div = $('<div />', { html: $('<table />', { css:{'width':'100%', 'page-break-after' : 'always'},  html: $('<tbody />') }) } );

        // clone priamry table
        var clone = $('table').clone();

        // cut cloned table thead to table of virtual div
        var header = clone.find('thead');
        // var repeatedHeader = header.find('.repeated').clone();
        div.find('table').prepend(header);

        var entries = clone.find('tbody tr');
        var firstPageEntries = entries.splice(0, 17);
        div.find('tbody').append(firstPageEntries);

        var remaining = _.chunk(entries.splice(17, entries.length-17), 23);

        for(var x in remaining){
            var table = $('<table />', {css:{'width':'100%', 'page-break-after' : 'always'}});
            table.append($('.repeated:first').clone().find('th').css({'border':'1px solid black', 'padding': '3px'})).wrap('<thead></thead>')
            table.append($(remaining[x]).wrap('<tbody>'));
            table.appendTo(div);
        }

        div.print();
        
    });
})