(function($){

	var list = $('tbody#list'),
		deleteUrl = list.data('delete-url'),

		deleteUser = function(){

			if(!confirm('Are you sure?')){
				return;
			}

			var tr = $(this).closest('tr'),
			 	request = $.post(deleteUrl, {id:tr.data('pk')});

			request.done(function(response){
				if(response.error_flag){
					$.growl.error({'title': 'Ooops!', 'message':'Unable to delete the selected user'});
					return;
				}
				$.growl.notice({'title': 'Done', 'message':'User been success fully deleted'});
				tr.remove();
			});

			request.fail(function(){
				$.growl.error({'title': 'Error 500', 'message':'An internal server error has occured. Please try again later.'});
			})
		};

	$(document).ready(function(){
		list.on('click', 'a.delete', deleteUser);
	});

})(jQuery);