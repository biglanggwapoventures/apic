$(document).ready(function () {
    var limit = 0, offset = 0;
    $('#daterangepicker').daterangepicker({format: 'YYYY-MM-DD'});
    function renderData(jsonData, context) {
        var tableRow = [];
        for (var data in jsonData) {
            var tableCell = [];
            tableCell[0] = jsonData[data].date;
            tableCell[1] = jsonData[data].hasOwnProperty('income') ? jsonData[data].income : '';
            tableCell[2] = jsonData[data].hasOwnProperty('cost') ? jsonData[data].cost : '';
            tableCell[3] = jsonData[data].hasOwnProperty('expense') ? jsonData[data].expense : '';
            tableRow.push('<td>' + tableCell.join('</td><td>') + '</td>');
        }
        context.html('<tr>' + tableRow.join('</tr><tr>') + '</tr>');
    }
    function fetchReportData(url, params, context) {
        context.loadify('enable');
        $.getJSON(url, params).done(function (result) {
            console.log(result);
            if (result && result.data['statements'].length > 0) {
                renderData(result.data['statements'], context);
                context.siblings('tfoot').find('tr:first > td:last > b').text(result.data['total_income']);
            } else if (result && !result.data['statements'].length) {
                context.html('<tr><td colspan="4" class="text-center">No results found.</td></tr>');
                context.siblings('tfoot').find('tr:first > td:last > b').text('0.00');
            }
        }).fail(function () {
            alert('Internal Server Error: Please contact developer.');
        }).always(function () {
            context.loadify('disable');
        });
    }
    $('form').submit(function () {
        fetchReportData($(this).attr('action'), $(this).serialize(), $('tbody#report-content'));
        return false;
    });
});
