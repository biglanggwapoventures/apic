(function($){
	$(document).ready(function(){	

		var selectedId ;

		$('._delete').click(function(){
			var that = $(this);
			selectedId = that.closest('tr').data('pk');
			$('#confirmation').modal('show');
		});

		$('#delete-confirmed').click(function(){
			var that = $(this);

			$.getJSON(that.data('delete-url')+selectedId)
			.done(function(response){
				if(response.error_flag){
					$.growl.error({title:'Ooops', message:response.message[0]});
					return;
				}
				$.growl.notice({title:'Success', message:'Customer has been successfully deleted.'});
				$('tr[data-pk='+selectedId+']').remove();
			}).fail(function(){
				$.growl.error({title:'Ooops', message:'Cannot perform action due to a server error. Please try again.'});
			}).always(function(){
				$('#confirmation').modal('hide');
			});

		});

		$('table').stickyTableHeaders({fixedOffset: $('.content-header')});

	})
})(jQuery)