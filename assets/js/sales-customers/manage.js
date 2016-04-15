(function($){
	$(document).ready(function(){	

		var messageBox = $('.callout.callout-danger');

		$('form').submit(function(e){

			e.preventDefault();

			var that = $(this);

			messageBox.addClass('hidden');

			$('[type=submit]').attr('disabled', 'disabled');

			$.post(that.data('action'), that.serialize())

			.done(function(response){
				if(response.error_flag){
					messageBox.removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
					$('html, body').animate({scrollTop: 0}, 'slow');
					return;
				}
				window.location.href = $('#cancel').attr('href');
			})
			.fail(function(){
				alert('An internal error has occured. Please try again in a few moment.');
			})
			.always(function(){
				$('[type=submit]').removeAttr('disabled');
			});
		});

		$('.pformat').priceFormat({prefix:''});

	})
})(jQuery)