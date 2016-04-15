(function ($) {
    $(document).ready(function () {
        var defaultValues = $.parseJSON($('[data-name=values]').attr('data-value'));
        /*===============================
         VARIABLE FOR DETAILS TABLE
         ===============================*/
        var seqNum = '<span class="seq-num text-center"></span>';
        var finishedProductList = '<p class="form-control-static">Fetching list of formulation...</p>';
        var formulationCode = '<span class="formulation-code"></span>';
        var mixNum = '<input name="details[mix_num][]" type="number" class="mix-num form-control input-sm" required="required"/>';
        var customerList = '<p class="form-control-static">Fetching list of customers...</p>';
        var status = '<input type="hidden" class="pk" name="details[id][]"/><select name="details[status][]" class="form-control input-sm status"><option value="0">Pending</option><option value="1">Done</option></select>';
        var removeLine = "<a class='remove-line-details btn btn-danger'><i class='fa fa-times'></i></a>";
        /*===============================
         VARIABLE FOR MISC TABLE
         ===============================*/
        var miscDesc = '<input type="text" name="misc_fees[description][]" class="form-control input-sm misc-desc">';
        var miscAmount = '<input type="text" name="misc_fees[amount][]" class="form-control input-sm misc-amount has-amount">';
        var miscRemoveLine = "<a class='remove-line-misc btn btn-danger'><i class='fa fa-times'></i></a>";
        /*===============================
         FUNCTIONS
         ===============================*/
        function addLineDetails(seqNumDefault, finishedProductListDefault, formulationCodeDefault, mixNumDefault, customerDefault, statusDefault, id) {
            if ($('#details-table tbody tr').length === 1) {
                $(".add-line-notif-details").addClass('hidden');
            }
            var cell = [seqNum, finishedProductListDefault, formulationCodeDefault, customerList, status, removeLine];
            var tr = $('<tr><td class="text-center">' + cell.join('</td><td>') + '</td></tr>');
            id !== 0 ? tr.find('.pk').val(id) : tr.find('.pk').remove();
            tr.find('.seq-num').text(seqNumDefault ? seqNumDefault : $('#details-table tbody tr').length);
            tr.find('.finished-product').val(finishedProductListDefault);
            tr.find('.formulation-code').text(formulationCodeDefault);
            tr.find('.mix-num').val(mixNumDefault);
            tr.find('.customers').val(customerDefault);
            tr.find('.status').val(statusDefault);
            tr.appendTo('#details-table tbody');
        }
        function addLineMisc(descDefault, amountDefault) {
            if ($('#misc-table tbody tr').length === 1) {
                $(".add-line-notif-misc").addClass('hidden');
            }
            var cell = [miscDesc, miscAmount, miscRemoveLine];
            var tr = $('<tr><td class="text-center">' + cell.join('</td><td>') + '</td></tr>');

            tr.find('.misc-desc').val(descDefault);
            tr.find('.has-amount').val(amountDefault).priceFormat({prefix: ''});

            // tr.find('.status').val(statusDefault);
            tr.appendTo('#misc-table tbody');
        }
        /*===============================
         SET DATETIME PICKER IN DATE FIELDS
         ===============================*/
        $('.datetimepicker').datetimepicker({
            timeFormat: 'hh:mm:ss tt'
        });
        $('.has-default').datetimepicker('setDate', (new Date()));
        /*===============================
         GET LIST OF FORMULATION CODES
         ===============================*/
        var formulationListJSON = $.parseJSON($('[data-name=formulations]').attr('data-value'));
        formulationList = $("<select class='finished-product form-control input-sm' name='details[fk_production_formulation_id][]' required='required' />");
        $("<option />", {value: '', text: 'Please select a formulation', disabled: 'disabled', selected: 'selected'}).appendTo(formulationList);
        for (var x in formulationListJSON) {
            $("<option />", {value: formulationListJSON[x].id, text: function(){
                return formulationListJSON[x].description + ' ('+formulationListJSON[x].code+')';
            }}).appendTo(formulationList);
        }
        formulationList = formulationList.prop('outerHTML');
        /*===============================
         GET LIST OF CUSTOMERS
         ===============================*/
        var customerListJSON = $.parseJSON($('[data-name=customers]').attr('data-value'));
        customerList = $("<select class='customers form-control input-sm' name='details[fk_sales_customer_id][]' />");
        $("<option />", {value: '', text: ' '}).appendTo(customerList);
        for (var x in customerListJSON) {
            $("<option />", {value: customerListJSON[x].id, text: customerListJSON[x].name}).appendTo(customerList);
        }
        customerList = customerList.prop('outerHTML');
        /*==============================
         ADD LINE TO DETAILS
         ==============================*/
        $('.add-line-details').click(function () {
            addLineDetails(0, '', '', '', '', 0, 0);
        });
        /*==============================
         ADD LINE TO Miscellaneous
         ==============================*/
        $('.add-line-misc').click(function () {
            addLineMisc();
        });
        /*==============================
         REMOVE LINE FROM DETAILS   
         ==============================*/
        $('#details-table tbody').on('click', '.remove-line-details', function () {
            $(this).closest('tr').remove();
            if ($('#details-table tbody tr').length === 1) {
                $(".add-line-notif-details").removeClass('hidden');
                return;
            }
            $('#details-table tbody tr').each(function (i) {
                $(this).find('.seq-num').text(i);
            });
        });
        /*==============================
         REMOVE LINE FROM MISC   
         ==============================*/
        $('#misc-table tbody').on('click', '.remove-line-misc', function () {
            $(this).closest('tr').remove();
            if ($('#misc-table tbody tr').length === 1) {
                $(".add-line-notif-misc").removeClass('hidden');
            }
        });
        /*==============================
         SUBMITTING DATA
         ==============================*/
        $("form").submit(function () {
            $('button[type=submit]').attr('disabled', 'disabled');
            $.post($(this).attr('action'), $(this).serialize()).done(function (response) {
                if (!response.error_flag) {
                    console.log(response);
                    window.location.href = $('#btn-cancel').attr('href');
                } else {
                    var err = [];
                    $.each(response.data, function (field, i) {
                        err.push(response.data[field]);
                    });
                    $("#job-order-validation-errors").html('<li><strong class="text-danger">' + err.join('</strong></li><li><strong class="text-danger">') + '</strong><li>');
                    $('#validation-errors').modal('show');
                }
            }).fail(function () {

            }).always(function () {
                $('button[type=submit]').removeAttr('disabled', 'disabled');
            });
            return false;
        });
        /*==============================
         POPULATING DATA
         ==============================*/
        function populateData() {
            if (defaultValues.length < 1) {
                return;
            }
            $('#datetime-start').val(defaultValues.date_started);
            $('#datetime-end').val(defaultValues.date_finished);
            $('#production-code').val(defaultValues.production_code);
            $('#remarks').val(defaultValues.remarks);
            $('[name=is_approved]').prop('checked', parseInt(defaultValues.is_approved) ? true : false);
            $.each(defaultValues.details, function (i) {
                addLineDetails(defaultValues.details[i].sequence_number, defaultValues.details[i].fk_production_formulation_id, defaultValues.details[i].mix_number, defaultValues.details[i].fk_sales_customer_id, defaultValues.details[i].status, defaultValues.details[i].id);
            });
            if (defaultValues.hasOwnProperty('misc_fees') && defaultValues.misc_fees.length) {
                $.each(defaultValues.misc_fees, function (i) {
                    console.log(defaultValues.misc_fees[i]);
                    addLineMisc(defaultValues.misc_fees[i].description, numeral(defaultValues.misc_fees[i].amount).format('0,0.00'), defaultValues.misc_fees[i].id);
                });
            }
        }
        populateData();
    });
})(jQuery);
