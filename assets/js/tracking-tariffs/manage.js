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

var idx = $('#less tbody tr').length;


    $('#add-line').click(function(){
        var tr = $('#less tbody tr');

        if(tr.hasClass('hidden')){
            tr.removeClass('hidden').find('input,select').removeAttr('disabled');
            return;
        }

        var clone = $(tr[0]).clone();
        clone.find('[type=hidden]').remove();
        clone.find('input,select').val('').attr('name', function(){
            return $(this).data('name').replace('idx', idx);
        });
        clone.find('.rate').val('');
        clone.find('.kms').val('');
        idx++;

        clone.appendTo('#less tbody');
    });

    $('#form').on('click', '.remove-line', function(){
        var rows = $(this).closest('tbody').find('tr');

        if(rows.length === 1){
            rows.find('input,select').val('').attr('disabled', 'disabled')
            rows.find('[type=hidden]').remove();
            rows.addClass('hidden');
        }else{
            $(this).closest('tr').remove();
        }

    });

	
	(function(){
		var tr = $('#less tbody tr');
		// if(tr.length === 1 && !tr.find('.item-less').val()){
		// 	tr.find('.remove-line').trigger('click');
		// 	alert("asd")
		// }else{
		// 	tr.find('.item-less').trigger('change');
		// 	alert("sdsd")
		// }
	})();
	})
})(jQuery)