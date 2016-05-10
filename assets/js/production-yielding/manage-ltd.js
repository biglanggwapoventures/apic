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
		
		var totalDressWeight = 0,
			totalByproductsWeight = 0,
			table = $(this).closest('table'),
			yieldQuantity = parseFloat(table.find('.yield-quantity').val() || 0);

		table.find('tbody tr').each(function(){
			var that = $(this),
				value = parseFloat(that.find('.produce-quantity').val() || 0)

			if(that.find('select.produce-product').find('option:selected').data('category-id') == 2){
				// cutups
				totalByproductsWeight += value;
				return;
			}
			totalDressWeight += value;

		});

		var recovery = (totalDressWeight/yieldQuantity) * 100,

			dressCost = parseFloat(table.find('.yield-unit-price input').val() || 0) / (recovery / 100);

		table.find('.total-dress-weight').text(numeral(totalDressWeight).format('0,0.00')+' kgs');
		table.find('.total-byproducts-weight').text(numeral(totalByproductsWeight).format('0,0.00')+' kgs');
		table.find('.total-recovery').text(recovery.toFixed(2)+'%');
		table.find('.dress-cost').text(numeral(dressCost).format('0,0.00'));

	});

		
	function calculate_summary(){
		$('.produce-quantity:not(:disabled):last').trigger('blur');
	}


	calculate_summary();
	$('.yield-quantity').trigger('blur');


});
