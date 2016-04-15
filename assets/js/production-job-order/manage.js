(function(){

	var detailsWrapper = '#details-table tbody',

		addNewLine = function(){
			var details = $(detailsWrapper).find('tr'),
				template = details.first(),
				detailsCount = details.length,
				sequenceNumber;

			if(detailsCount === 1 && template.hasClass('hidden')){
				sequenceNumber = 1;
				template.find('td:first').text(sequenceNumber);
				template.removeClass('hidden').find('input,select').removeAttr('disabled');
			}else{
				var clone = template.clone();
				sequenceNumber = detailsCount+1;
				clone.find('input,select').val('');
				clone.find('td:first').text(sequenceNumber);
				clone.find('[type=hidden]').remove();
				clone.appendTo(detailsWrapper);
			}
		},

		removeDetail = function(){
			var tr = $(this).closest('tr'),
				nextTr = tr.next();
			if($(detailsWrapper).find('tr').length===1){
				tr.find('input[type=hidden]').remove();
				tr.addClass('hidden').find('input,select').val('').attr('disabled', 'disabled');
			}else{
				tr.remove();
			}
			updateSequenceNumber();
		},

		updateSequenceNumber = function(reference){
			$.each($(detailsWrapper).find('tr'), function(i, v){
				$(v).find('td:first').text(i+1);
			});
		}, 

		submit = function(event){
			event.preventDefault();
			event.stopPropagation();

			$('[type=submit]').addClass('disabled');

			var request = $.post($(this).attr('action'), $(this).serialize());

			request.done(function(response){
				if(response.error_flag){
					$('.callout.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
				}else{
					window.location.href = $('#btn-cancel').attr('href');
				}
			});

			request.fail(function(){
				$.growl.error({'title': 'Unexpected error', 'message':'Please try again in a few seconds.'});
			});

			request.always(function(){
				$('[type=submit]').removeClass('disabled');
			})

		};


	$(document).ready(function(){
		$('.add-line-details').click(addNewLine);
		$(detailsWrapper).on('click', '.remove-detail', removeDetail);
		$('form').submit(submit)
		$('[name=date_started]').datetimepicker({
            timeFormat: 'hh:mm:ss TT'
        });
	});
})(jQuery);