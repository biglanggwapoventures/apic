(function($){

	var form = $('#form'),
		availableURL = form.data('available'),
		getAvailable = function(customerId){
			var request = $.getJSON(availableURL+customerId);
			request.done(function(response){
				var pls = [];
				$.each(response, function(i,v){
					pls.push('<tr><td ><div class="checkbox"><label><input type="checkbox" name="pl_id[]" value="'+v+'">Packing List # '+v+'</label></div></td></tr>');
				})
				$('#gp-pl table tbody').html(pls.join(''));
			})
		},
		gpType = function(type){
			if(type === 'pl'){
				$('#gp-pl').removeClass('hidden').find('input').removeAttr('disabled');
				$('#gp-others').addClass('hidden').find('input').attr('disabled', 'disabled');
				$('input[name=issued_to]').attr('disabled','disabled');
				$('#customer').removeAttr('disabled')
			}else{
				$('#gp-pl').addClass('hidden').find('input').attr('disabled', 'disabled');
				$('#gp-others').removeClass('hidden').find('input').removeAttr('disabled');
				$('input[name=issued_to]').removeAttr('disabled');
				$('#customer').attr('disabled','disabled')
			}
		},
		newLine = function(){
			var trs = $('#gp-others table tbody tr:first'); 
			if(trs.hasClass('hidden')){
				trs.removeClass('hidden').find('input').removeAttr('disabled');
			}else{
				var clone = trs.clone();
				clone.find('input').val('');
				clone.appendTo('#gp-others table tbody');
			}
		},
		removeLine = function(){
			if($('#gp-others table tbody tr').length === 1){
				$('#gp-others table tbody tr:first').addClass('hidden').find('input').attr('disabled', 'disabled').val('');
			}else{
				$(this).closest('tr').remove();
			}
		},
		submit = function(e){
			e.preventDefault();
			var request = $.post($(this).data('action'), $(this).serialize());
			request.done(function(response){
				if(response.error_flag){
					$('.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
				}else{
					window.location.href= $('a.cancel').attr('href');
				}
			})
		};

	$(document).ready(function(){
		$('#customer').change(function(){
			getAvailable($(this).val())
		});
		$('#gp-type').change(function(){
			gpType($(this).val());
		});
		$('.new-line').click(newLine);
		$('#gp-others').on('click', '.remove-line', removeLine)
		form.submit(submit)
	});

})(jQuery);