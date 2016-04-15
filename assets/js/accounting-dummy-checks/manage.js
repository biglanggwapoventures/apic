(function($){
    var initializeDatepicker = function(el){
        if(typeof el === 'string'){
            $(el).datepicker({dateFormat:'mm/dd/yy'})
            return;
        }
        el.datepicker({dateFormat:'mm/dd/yy'});
    },

    initializePriceformat = function(el){
        if(typeof el === 'string'){
            $(el).priceFormat({prefix:''})
            return;
        }
        el.priceFormat({prefix:''});
    },

    submit = function(e){

        e.preventDefault();

        var button = $('[type=submit]');
        button.attr('disabled', 'disabled').text('Submitting...');

        var form = $(this),
            action = form.data('action'),
            request = $.post(action, form.serialize());

        request.done(function(response){
            if(response.error_flag){
                button.removeAttr('disabled').text('Submit');
                form.find('.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
            }else{
                window.location.href = $('a.cancel').attr('href'); 
            }
        });

        request.fail(function(){
            $.growl.error({title:'Ooops!', message: 'An internal server error has occured! Please try again later.'});
            button.removeAttr('disabled').text('Submit');
        });
    }

    $(document).ready(function(){
        initializeDatepicker('.datepicker');
        initializePriceformat('.price');
        $('form').submit(submit);
        $('.print-check').printPage();
    })

})(jQuery);