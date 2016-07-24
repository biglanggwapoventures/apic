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
				$.growl.notice({title:'Success', message:'Unit has been successfully deleted.'});
				$('tr[data-pk='+selectedId+']').remove();
			}).fail(function(){
				$.growl.error({title:'Ooops', message:'Cannot perform action due to a server error. Please try again.'});
			}).always(function(){
				$('#confirmation').modal('hide');
			});

		});





















		$('table').stickyTableHeaders({fixedOffset: $('.content-header')});


		var table = $('#entries')
	table.css({width:'100%'});

	/*=====================
	 INITIALIZE STICKY TABLE HEADER
	=====================*/
	table.stickyTableHeaders({fixedOffset: $('.content-header')});

	/*=====================
	 INITIALIZE DATEPICKER ON DATE FIELDS
	=====================*/
 	$('.datepicker').datepicker({dateFormat: 'mm/dd/yy'});

	/*=====================
	 INITIALIZE DATATABLE WITH AJAX
	=====================*/
	var searchParams = {},
		getURL = table.data('get-url'),
		updateURL = table.data('edit-url'),
		deleteURL = table.data('delete-url');

 	var dataTable = table.DataTable({
		paging: true,
		lengthChange: false,
		ordering: false,
		searching: false,
		responsive: true,
		pageLength: 50,
		processing: true,
		serverSide: true,
		ajax: {
			url: getURL,
			type: 'GET',
			dataSrc: 'data',
			data: function ( d ) { // additional data for the search filters
				return $.extend( {}, d, searchParams);
			}
		},
		columns: [
			{ data: 'id' },
			{ data: 'po_number' },
			{ data: 'date', render : function( data, type, row ) { return moment(data).format('MM/DD/YYYY') } },
			{ data: 'supplier' },
			{ data: 'pr_number' },
			{ data: 'total', render:  $.fn.dataTable.render.number( ',', '.', 2, '' ) },
			{ data: null },
			{ data: null }
		],
	  	columnDefs: [ 
	  		{ "className": "text-right", "targets": [ 5 ] }
	  ]	,
	  rowCallback: function(row, data, index){
			// add data-pk of row (for deleting)
			$(row).attr('data-pk', data.id);
			// add link for updating of row
			$('td:eq(0)', row).html('<a href="'+ updateURL + data.id + '">'+ data.id +'</a>');
			// labels for status
			var status = {};
			if(data.approved_by){
				status.className = 'label label-success';
				status.text = 'Approved';
			}else{
				status.className = 'label label-warning';
				status.text = 'Pending';
			}
			$(row).find('td:nth-child(7)').html('<span class="'+ status.className + ' "> ' + status.text + '</span>');
			$(row).find('td:nth-child(8)').html('<a class="btn btn-danger btn-xs btn-flat item-remove"><i class="fa fa-times"></i></a>');
			return row;
		}
	});

	/* ERROR HANDLING OF AJAX DATATABLES */
	$.fn.dataTable.ext.errMode = 'none';

	$('table#entries').on('error.dt', function ( e, settings, techNote, message ) {
		console.log(message)
		$.growl.error({title:'Ooops', message:'An internal server error has occured. Try to refresh page. If error still persists, contact developer.'});
	}).DataTable();

	/* FOR ADVANCED SEARCH */
	$('form#advanced-search').submit(function () {
		searchParams = {
			page: 1
		};
		$.each($(this).serializeArray(), function (i, field) {
			searchParams[field.name] = field.value;
		});
		dataTable.ajax.reload();
		return false;
	});

	$('#reset-btn').click(function(e){
		$('form#advanced-search').trigger('reset');
	});


	/* REMOVING DATA */
	$('table#entries').on('click', '.item-remove', function (e) {
		if(!confirm('Are you sure? This action cannot be undone.')) return;
		var pk = $(this).closest('tr').data('pk');
		$.post(deleteURL, {id: pk})
		.done(function(response){
			if(response.error_flag){
				$.growl.error({title: 'Error', message: response.message[0]});
				return;
			}
			$.growl.notice({title:'Done', message: 'Egg receiving # '+ pk+ ' has been successfully deleted!'});
			$('tr[data-pk="'+pk+'"]').remove();
			dataTable.draw();
		})
		.fail(function(){
			$.growl.error({title: 'Error', message: 'Cannot perform action due to an internal server error!'});
		})
	});
	})
})(jQuery)