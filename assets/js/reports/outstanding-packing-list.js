jQuery(function ($) {
    $(document).ready(function () {
        var customerList, fieldCustomerCode = $('#customer-code'), fieldCustomerCreditTerm = $('#customer-credit-term'), fieldCustomerCreditlimit = $('#customer-credit-limit');
        //set editable to inline mode
        $.fn.editable.defaults.mode = 'inline';
        //parse customer json appended to table body
        customerList = $.parseJSON($('table#oplr').attr('data-customer-src'));
        //remove appended json
        $('table#oplr').removeAttr('data-customer-src');
        //add options to customer list
        console.log(customerList)
        $('#customer-list-editable').editable({
            source: function () {
                var src = [];
                $.each(customerList, function (field, i) {
                    src.push({value: field, text: customerList[field].name});
                });
                return src.sort(function(a, b) {
                    return a.text.localeCompare(b.text);
                });
            }
        });
        $('#customer-list-editable').on('save', function (e, params) {
            $('#report-modal .modal-title').text('Loading');
            $('#report-modal .modal-body p').text('Generating report. Please wait...');
            $('#report-modal').modal('show');
            fieldCustomerCode.text(customerList[params.newValue].customer_code);
            fieldCustomerCreditlimit.text(numeral(customerList[params.newValue].credit_limit).format('0,0.00'));
            fieldCustomerCreditTerm.text(parseInt(customerList[params.newValue].credit_term) ? customerList[params.newValue].credit_term + ' Days' : 'Cash on delivery');
            $.get($('#oplr').attr('data-url-fetch'), {customer_id: params.newValue}).done(function (data) {
                $('#oplr tbody tr:not(.static)').remove();
                $('#oplr tbody').append(data);
                $('#report-modal').modal('hide');
            }).fail(function () {
                $('#report-modal .modal-title').text('Error');
                $('#report-modal .modal-body p').text('An error has occured. Please try again.');
            });
        });
    });

});