$(document).ready(function () {
    var limit = 0, offset = 0;
    $('#daterangepicker').daterangepicker({format: 'YYYY-MM-DD'});
    function renderData(jsonData, context) {
        var tableRow = [];
        for (var data in jsonData) {
            var tableCell = [];
            tableCell[0] = jsonData[data].date;
            tableCell[1] = jsonData[data].product;
            tableCell[2] = jsonData[data].product_category;
            tableCell[3] = jsonData[data].hasOwnProperty('stock_in') ? jsonData[data].stock_in : '';
            tableCell[4] = jsonData[data].hasOwnProperty('stock_out') ? jsonData[data].stock_out : '';
            tableCell[5] = ' ';
            tableRow.push('<td>' + tableCell.join('</td><td>') + '</td>');
        }
        context.html('<tr>' + tableRow.join('</tr><tr>') + '</tr>');
    }
    function fetchReportData(url, params, context) {
        context.loadify('enable');
        $.getJSON(url, params).done(function (result) {
            if (result && result.data.length > 0) {
                renderData(result.data, context);
            } else if (result && !result.data.length) {
                context.html('<tr><td colspan="6" class="text-center">No results found.</td></tr>');
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
