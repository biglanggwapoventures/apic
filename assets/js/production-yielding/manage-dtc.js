$(document).ready(function(){

	var messageBox = $('.callout.callout-danger');

	// add new line
	$('.add-line').click(function(){
		var table =  $(this).closest('table'),
			tbody = table.find('tbody'),
			tr = tbody.find('tr'),
			idx = tbody.data('length');

		table.find('tbody').data('length', idx+1);

		if(tr.hasClass('hidden')){
			tr.removeClass('hidden')
				.find('select,input')
				.removeAttr('disabled');
		}else{
			var clone = $(tr[0]).clone();
			clone.find('[type=hidden]').remove();
			clone.find('select,input').val('').attr('name', function(){
				return $(this).data('name').replace('idx', table.find('tbody').data('length'));
			});
			clone.appendTo(table);
		}
	});


	// remove line
	$('#yield-section').on('click', '.remove-line', function(){
		var table =  $(this).closest('table'),
			tr = table.find('tbody tr');

		if(tr.length === 1){
			tr.addClass('hidden')
				.find('input,select')
				.val('')
				.attr('disabled', 'disabled');
		}else{
			$(this).closest('tr').remove();
		}
		calculate_summary();
	})


	// submit form
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

	$('.yield-quantity, .yield-unit-price input').blur(function(){
		var that = $(this),
			tr  = that.closest('tr'),
			unitPrice = tr.find('.yield-unit-price input').val() || 0,
			qty = tr.find('.yield-quantity').val() || 0,
			amount = (parseFloat(qty) * parseFloat(unitPrice));

		tr.find('.yield-amount').text(numeral(amount).format('0,0.00'));

	});


	$('#yield-section').on('blur', '.produce-quantity', function(){
		var totalQty = 0,
			table = $(this).closest('table');

		table.find('tbody tr').each(function(){
			var quantity = $(this).find('.produce-quantity').val() || 0;
			totalQty += parseFloat(quantity);
		});

		table.find('.produce-total-quantity').text(numeral(totalQty).format('0,0.00')+' kgs');

		var weightLoss = totalQty ? (100 - (totalQty / parseFloat(table.find('.yield-quantity').val() || 0) * 100)) : 0;
		table.find('.weight-loss').html(weightLoss.toFixed(2)+'%');
		

	});

		
	function calculate_summary()
	{
		$('.produce-quantity:not(:disabled):last').trigger('blur');
	}


	calculate_summary();
	$('.yield-quantity').trigger('blur');


});
