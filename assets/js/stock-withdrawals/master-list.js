(function($){

	var remove = function(){
		var confirmed = confirm('Are you sure');
		if(!confirmed){
			return;
		}
		var that = $(this),
			request = $.post($('table#adjustment-master-list').data('delete-url'), {id:that.closest('tr').data('pk')});
		request.done(function(response){
			if(response.error_flag){
				$.growl.error({'title': 'Ooops!', 'message': 'Cannot delete item. Please try again later.'});
			}else{
				that.closest('tr').remove();
			}	
		})
	};

	$(document).ready(function(){
		$('table#adjustment-master-list').on('click', '.remove', remove);
	})

})(jQuery);