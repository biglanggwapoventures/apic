(function($, n){

	var details = '#withdrawal-details',

		priceFormat = function(el){
            if(typeof el === 'string'){
                $(el).priceFormat({'prefix': ''});
                return;
            }
            el.priceFormat({'prefix': ''});
        },

		addNewLine = function(){
			var trs = $(details).find('tr'), 
				clone = $(trs[0]).clone();
			clone.find('input,select').val('');
			clone.find('.packaging').text('');
			clone.find('input[type=hidden]').remove();
			priceFormat(clone.find('.price'));
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
			getTotal();
		},

		showPackaging = function(el){
			$(el).closest('tr').find('td.packaging').text($(el).find('option:selected').data('packaging'));
		},

		getTotal = function(){
			var total = 0;
			$.each($(details).find('tr'), function(i, v){
				var that = $(v),
					unitPrice = parseFloat(n().unformat(that.find('input.price').val())),
					quantity = parseFloat(that.find('input.quantity').val()),
					amount = quantity*unitPrice;;
				that.find('td.amount').text(n(amount).format('0,0.00'));
				total += amount;
			});
			$('#total-amount').text(n(total).format('0,0.00'))
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
		};

	$(document).ready(function(){
		showPackaging($(details).find('tr select.items'));
		priceFormat('.price');
		$('#new-line').click(addNewLine);
		$(details).on('click', '.remove-line', removeLine);
		$(details).on('change', '.items', function(){
			showPackaging(this);
		});
		$('form').submit(submit);
		$(details).on('input keyup', '.price, .quantity', getTotal);	
	});

})(jQuery, numeral);