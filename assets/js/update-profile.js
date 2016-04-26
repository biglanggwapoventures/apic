$(document).ready(function(){

	$('#update-profile-btn').click(function(){
		$.post($(this).data('url'), function(data){
			var new_data = JSON.parse(data);
			$('#fname').text(new_data.FirstName);
			$('#lname').text(new_data.LastName);
			$('#email').text(new_data.Email);
            $('[name=firstname]').val(new_data.FirstName);
            $('[name=lastname]').val(new_data.LastName);
            $('[name=email]').val(new_data.Email)
		})
	});

	var doModuleAccess = function(){
        if($(this).val() == 1){
            $('.disabled-type-admin').attr('disabled', 'disabled');
            return;
        }
        $('.disabled-type-admin').removeAttr('disabled');
    }

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
            window.location.href=window.location.href;
        }
    });

    $('input[name=type]').change(doModuleAccess);

});