(function ($, num) {

    var initDatepicker = function (selector) {
        if (typeof selector === 'string') {
            $(selector).datepicker({dateFormat: 'yy-mm-dd'});
        } else {
            selector.datepicker({dateFormat: 'yy-mm-dd'});
        }
    };
    
    var initPriceFormat = function (selector){
         if (typeof selector === 'string') {
            $(selector).priceFormat({prefix: ''});
        } else {
            selector.priceFormat({prefix: ''});
        }
    };

    var addNewLineOthers = function () {
        var details = $('table#credit-memo-others > tbody > tr');
        var template = $(details[0]);
        if (template.hasClass('hidden')) {
            $(details[0]).removeClass('hidden').find('input').removeAttr('disabled');
        } else {
            var clone = template.clone();
            clone.find('input').val('');
            clone.find('.hasDatepicker').removeClass('hasDatepicker').removeAttr('id');
            initPriceFormat(clone.find('.price'));
            clone.appendTo('table#credit-memo-others > tbody');
        }
    };
    
    var removeLineOthers = function(){
        var details = $('table#credit-memo-others > tbody > tr');
        if(details.length === 1){
            $(details[0]).addClass('hidden').find('input').val('').attr('disabled', 'disabled');
        }else{
            $(this).closest('tr').remove();
        }
        calculateOthersTotalAmount();
    };
    
    var calculateReturnsTotalAmount = function(){
        var total = 0;
        $.each($('table#credit-memo-returns > tbody > tr'), function(i, el){
            var that = $(el),
            unitPrice = parseFloat(that.find('.unit-price').text().replace(/,/g, '') || 0),
            quantity = parseFloat(that.find('.quantity').val() || 0),
            amount = unitPrice*quantity;
            that.find('td:last-child').text(num(amount).format('0,0.00'));
            total+=amount;
        });
        $('table#credit-memo-returns > tfoot > tr > td:last-child').text(num(total).format('0,0.00'));
    };
    
    var enableReturn = function(){
        var that = $(this);
        var tr = that.closest('tr');
        if(that.is(':checked')){
            tr.find('input').removeAttr('disabled');
        }else{
            tr.find('input:not([type=checkbox])').attr('disabled', 'disabled');
            tr.find('input[type=text],[type=number]').val('');
            calculateReturnsTotalAmount();
        }
    };
    
    var calculateOthersTotalAmount = function (){
        var total = 0;
        $.each($('table#credit-memo-others > tbody > tr'), function(i, el){
            var that = $(el),
            amount = parseFloat(that.find('.amount').val().replace(/,/g, '') || 0);
            total+=amount;
        });
        $('table#credit-memo-others > tfoot > tr > td:last-child span.total-amount').text(num(total).format('0,0.00'));
    };
    
    var submit = function(event){
        
        event.preventDefault();

        var origin = $('[type=submit]');

        origin.addClass('disabled');

        var notifications = $('#validation-errors');
        notifications.addClass('hidden');
     
        var that = $(this);
        var request = $.post(that.attr('action'), that.serialize());
        
        request.done(function(response){
            if(response.error_flag){
                notifications.removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
                origin.removeClass('disabled');
            }else if(response.error_flag === false){
                $.growl.notice({title:'Done!', message:'Credit memo has been successfully saved! Returning to packing list...'});
                setTimeout(function(){
                    window.location.href = $('a#pl-link').attr('href');  
                }, 1500);
                
            }
        });

        request.fail(function(){
            origin.removeClass('disabled');
        }); 
        
    };

    $(document).ready(function () {
        initDatepicker('.datepicker');
        initPriceFormat('.price');
        $('button#add-cm-others').click(addNewLineOthers);
        $('table#credit-memo-others > tbody').on('click', '.remove-line-others', removeLineOthers);
        $('table#credit-memo-returns').on('keyup change', '.quantity', calculateReturnsTotalAmount);
        $('table#credit-memo-returns').on('change', '.enable', enableReturn);
        $('table#credit-memo-others').on('keyup', '.amount', calculateOthersTotalAmount);
        $('form').submit(submit);
    });

})(jQuery, numeral);