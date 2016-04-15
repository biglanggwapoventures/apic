jQuery(function ($) {
    $(document).ready(function () {
        var customerList;
        //set editable to inline mode
        $.fn.editable.defaults.mode = 'inline';
        //parse customer json appended to table body
        customerList = $.parseJSON($('table#aorr').attr('data-customer-src'));
        //remove appended json
        $('table#aorr').removeAttr('data-customer-src');
        //add options to customer list
        $('#customer-list-editable').editable({
            source: function () {
                var src = [];
                $.each(customerList, function (field, i) {
                    src.push({value: field, text: customerList[field].name});
                });
                return src;
            }
        });
        $('#customer-list-editable').on('save', function (e, params) {
//            $('#report-modal').modal('show');
//            $('#report-modal .modal-title').text('Loading');
//            $('#report-modal .modal-body p').text('Generating report. Please wait...');
            
            $.getJSON($('#aorr').attr('data-url-fetch'), {customer_id: params.newValue}).done(function (data) {
                console.log(data);
//                $('#oplr tbody tr:not(.static)').remove();
//                $('#oplr tbody').append(data);
//                $('#report-modal').modal('hide');
            }).fail(function () {
//                $('#report-modal .modal-title').text('Error');
//                $('#report-modal .modal-body p').text('An error has occured. Please try again.');
            });
        });
    });

});