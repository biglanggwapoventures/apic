(function($){

	var jo = '#job-orders',
		details = 'tbody#details', 

		getJobOrderDetails = function(url, id){
			var request = $.getJSON(url, {id:id});
			request.done(function(response){
				if(typeof response !== 'object'){
					$(details).html('<tr><td colspan="3" class="text-center">An unknown error has occured. Please try again.</td></tr>');
					return;
				}
				populateDetails(response);
			})
		},

		populateDetails = function(data){
			var row = [];
			$.each(data, function(i, v){
				var td = [];
				td[0] = v.description + ' ['+v.formulation_code+']';
				td[1] = v.mix_number;
				td[2] = '<input type="number" name="quantity[]" step="0.01" min="0" class="form-control">';
				td[3] = v.unit;
				row.push('<td><input type="hidden" name="jo_detail_id[]" value="'+v.id+'" />'+td.join('</td><td>')+'</td>');
			});
			$(details).html('<tr>'+row.join('</tr><tr>')+'</tr>');
		},

		submit = function(e){
			e.preventDefault();
			var submitBtn = $('[type=submit]');
			submitBtn.attr('disabled', 'disabled');
			var request = $.post($(this).attr('action'), $(this).serialize());
			request.done(function(response){
				if(typeof response !== 'object'){
					return;
				}
				if(response.error_flag){
					$('.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
					submitBtn.removeAttr('disabled');
				}else{
					window.location.href = $('a.cancel').attr('href');
				}
			});
			request.fail(function(){
				$.growl.error({title: 'Ooops!', message:'An unknown error has occured. Please try again later!'});
				submitBtn.removeAttr('disabled');
			})
		};

	$(document).ready(function(){
		$(jo).change(function(){
			var val = $(this).val(),
				url = $(this).data('get-details-url'); 
			$(details).html('<tr><td colspan="3" class="text-center">Loading job order. Please wait...</td></tr>');
			getJobOrderDetails(url, val);
		});
		$('form').submit(submit)
	});

})(jQuery);