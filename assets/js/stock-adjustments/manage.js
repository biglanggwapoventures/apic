(function($){

	var details = '#adjustment-details',

		addNewLine = function(){
			var trs = $(details).find('tr'), 
				clone = $(trs[0]).clone();
			clone.find('input,select').val('');
			clone.find('.packaging').text('');
			clone.find('input[type=hidden]').remove();
			formatPrice(clone.find('.price'));
			clone.appendTo(details);
		},

		removeLine = function(){
			var trs = $(details).find('tr');
			if(trs.length === 1){
				trs.find('input,select').val('');
				trs.find('input[type=hidden]').remove();
			}else{
				$(this).closest('tr').remove();
			}
		},

		showPackaging = function(el){
			$(el).closest('tr').find('td.packaging').text($(el).find('option:selected').data('packaging'));
		}, 

		submit = function(e){
			e.preventDefault();
			var request = $.post($(this).attr('action'), $(this).serialize());
			request.done(function(response){
				if(response.error_flag){
					$('.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
				}else{
					window.location.href= $('a#cancel').attr('href');
				}
			})
		},

		formatPrice = function(el){
			if(typeof el === 'string'){
				$(el).priceFormat({prefix:''});
				return;
			}
			el.priceFormat({prefix:''});
		};

	$(document).ready(function(){
		showPackaging($(details).find('tr select.items'));
		$('#new-line').click(addNewLine);
		$(details).on('click', '.remove-line', removeLine);
		$(details).on('change', '.items', function(){
			showPackaging(this);
		});
		$('form').submit(submit);
		formatPrice($('.price'));
	});

})(jQuery);