$(document).ready(function () {
    var limit = 0, offset = 0;
    $('#daterangepicker').daterangepicker({format: 'YYYY-MM-DD'});
    function renderData(jsonData, context) {
        var tableRow = [];
        for (var data in jsonData) {
            console.log(data);
            var tableCell = [];
            tableCell[0] = jsonData[data].company_name + ' ( ' + jsonData[data].customer_code + ' )';
            tableCell[1] = '<a target="_blank" href="' + $('input[name=data-so-link]').val() + "/" + jsonData[data].id + '">' + jsonData[data].id + '</a>';
            tableCell[2] = jsonData[data].invoice_number;
            tableCell[3] = jsonData[data].date;
            tableCell[4] = jsonData[data].description + '(' + jsonData[data].code+')';
            tableCell[5] = jsonData[data].this_delivery;
            tableCell[6] = jsonData[data].unit_price;
            tableCell[7] = jsonData[data].delivery_amount;
            tableCell[8] = jsonData[data].receipt_amount;
            tableCell[9] = jsonData[data].balance;
            tableRow.push('<td>' + tableCell.join('</td><td>') + '</td>');
        }
        context.html('<tr>' + tableRow.join('</tr><tr>') + '</tr>');
    }
    function fetchReportData(url, params, context) {
        context.loadify('enable');
        $.getJSON(url, params).done(function (result) {
            if (result && result.data.length > 0) {
                console.log(result.data);
                renderData(result.data, context);
            } else if (result && !result.data.length) {
                context.html('<tr><td colspan="9" class="text-center">No results found.</td></tr>');
            } else {
                alert('Internal Server Error: Please contact developer.');
            }
        }).fail(function () {
            return;
        }).always(function () {
            context.loadify('disable');
        });
    }
    $('form').submit(function () {
        fetchReportData($(this).attr('action'), $(this).serialize(), $('tbody#report-content'));
        return false;
    });
});
