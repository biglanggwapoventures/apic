$(document).ready(function(){

    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
    $('table').stickyTableHeaders({fixedOffset: $('.content-header')});

    // var sample = ['sample', 'lol'];

    $('#print-report').click(function(){

        // make virtual div with table inside
        var div = $('<div />');

        // clone priamry table
        var tableClone = $('table').clone();
        var entries = tableClone.find('tbody tr');
        tableClone.find('tbody').empty();

        var firsTable = tableClone.clone();
        firsTable.find('tfoot').remove();

        
        firsTable.find('tbody').empty();

        var firstTableEntries = entries.splice(0, 17);

        firsTable.find('tbody').append(firstTableEntries);

        firsTable.appendTo(div);

        var remaining = _.chunk(entries.splice(17, entries.length-17), 23);
        for(var x in remaining){
            var table = tableClone.clone();
            table.find('tfoot').remove();
            table.find('thead tr:not(.repeated)').addClass('noPrint');
            table.find('tbody').html(remaining[x])
            table.appendTo(div);
        }

        div.print();
        
    });
})