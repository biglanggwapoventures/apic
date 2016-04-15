(function ($) {

    var doModuleAccess = function(){
        if($(this).val() == 1){
            $('.disabled-type-admin').attr('disabled', 'disabled');
            return;
        }
        $('.disabled-type-admin').removeAttr('disabled');
    }

    $(document).ready(function () {

        $('form#personal-information').ajaxForm({
            beforeSubmit: function(){
                $('[type=submit]').attr('disabled', 'disabled');
            },
            success: function(response){
                if(typeof response !== 'object'){
                    $.growl.error({'message':'An internal server error has occured. Please try again later.', 'title': 'Ooops!'})
                    $('[type=submit]').removeAttr('disabled');
                    return;
                }
                if(response.error_flag){
                    $('form#personal-information .callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
                    $('[type=submit]').removeAttr('disabled');
                    return;
                }
                window.location.href=$('a.cancel').attr('href');
            }
        });

        $('input[name=type]').change(doModuleAccess)

    });
})(jQuery); 