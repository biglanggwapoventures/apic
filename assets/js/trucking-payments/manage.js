(function ($, n) {

    var getUnsettled_URL = $('.box').data('get-customer-unsettled-url'),
        pl_URL = $('.box').data('pl-url'),
        tbody = $('tbody#transactions'),

        gotoPL = function(id){
            return pl_URL+id;
        },

        priceFormat = function(el){
            if(typeof el === 'string'){
                $(el).priceFormat({'prefix': ''});
                return;
            }
            el.priceFormat({'prefix': ''});
        },

        dateFormat = function(el){
            if(typeof el === 'string'){
                $(el).datepicker({'dateFormat': 'mm/dd/yy'});
                return;
            }
            el.priceFormat({'prefix': ''});
        },

        getUnsettledPL = function(){
            var customer_id = $(this).val(),
                request = $.getJSON(getUnsettled_URL, {'customer_id':customer_id});
            request.done(function(response){
                if(typeof response !== 'object'){
                    tbody.html('<tr><td colspan="5" class="text-center text-danger">An unknown error has occured. Please try again later.</td></tr>');
                    return;
                }
                populateTransactions(response);
            });
        },

        populateTransactions = function(data){
            if(!data.length){
                tbody.html('<tr><td colspan="6" class="text-center">Selected customer has no outstanding balance</td></tr>');
                return;
            }
            var tr = [];
            $.each(data, function(i, v){
                var td = [];
                td[0] = '<input type="hidden" value="'+v.fk_tracking_packing_list_id+'" name="details['+v.fk_tracking_packing_list_id+'][pl_id]"/><a target="_blank" href="'+gotoPL(v.fk_tracking_packing_list_id)+'">PL# '+v.fk_tracking_packing_list_id+'</a>';
                td[1] = v.date;
                td[2] = '<span class="full-pay">'+n(v.total_amount).format('0,0.00')+'</span>';
                td[3] = n(v.total_paid).format('0,0.00');
                td[4] = '<span class="full-pay">'+n(v.total_amount-v.total_paid).format('0,0.00')+'</span>';
                td[5] = '<input type="text" class="form-control price payment" name="details['+v.fk_tracking_packing_list_id+'][this_payment]"/>';
                tr.push('<td>'+td.join('</td><td>')+'</td>');
            });
            tbody.html('<tr>'+tr.join('</tr><tr>')+'</tr>');
            priceFormat(tbody.find('tr > td > input.price'));
        },

        fullPay = function(){
            var amount = $(this).text();
            if(n().unformat(amount) > 0){
                 $(this).closest('tr').find('.payment').val(amount);
                getTotal();
            }
           
        },

        getTotal = function(){
            var total = 0;
            $.each(tbody.find('tr'), function(i, v){
                var data = $(v).find('td input.price').val(),
                    price = n().unformat(data);
                if(price > 0){
                    total+=price;
                }
            });
            $('#total').text(n(total).format('0,0.00'));    
        },

        cashPayment = function(){
            if($(this).val() === 'cash'){
                $('.pay-opt-cash-disabled').attr('disabled', 'disabled');
                return;
            }
            $('.pay-opt-cash-disabled').removeAttr('disabled');
        },

        submit = function(e){
            e.preventDefault();

            var request = $.post($(this).data('action'), $(this).serialize());
            $('[type=submit]').attr('disabled', 'disabled');

            request.done(function(response){
                if(typeof response !== 'object'){
                    $.growl.error({'title': 'Ooops!', 'message': 'An unexpected error has occured. Please try again later.'});
                    $('[type=submit]').removeClass('disabled')
                    return;
                }
                if(response.error_flag){
                    $('.callout.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
                    $('[type=submit]').removeClass('disabled')
                }else{
                    window.location.href= $('a.cancel').attr('href');
                }
            })
        };

    $(document).ready(function () {
        $('select[name=customer]').change(getUnsettledPL);
        dateFormat('.datepicker');
        priceFormat('.has-amount, .payment');
        tbody.on('click', '.full-pay', fullPay);
        tbody.on('keyup input', '.payment', getTotal);
        $('form').submit(submit);
        $('#pay-opt').change(cashPayment);
        $('#total').click(function(){
            $('#total-payment').val($(this).text());
        });
    });
}(jQuery, numeral));