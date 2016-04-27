<style type="text/css">
	div.t{
        border-bottom:1px solid black;
    }
</style>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title"><?= $form_title ?></h3>
    </div><!-- /.box-header -->

    <div class="box-body">
    	<form data-action="<?= $form_action?>" method="POST">
    		<div class="form-group">
    			<label>Remarks</label>
    			<textarea class="form-control" name="remarks"></textarea>
    		</div>
    		<hr>
			<?php foreach(array_chunk($data['details'], 2) AS $chunk):?>
				<div class="row" id="yield-section" style="margin-bottom:20px">
					<?php foreach($chunk AS $row):?>
						<div class="col-md-6">
	    				<table class="table table-condensed">
		    				<thead>
		    					<tr class="active">
		    						<th>Item</th><th style="width:10%">Unit</th><th>Quantity</th><th></th>
		    					</tr>
		    					<tr class="info">
		    						<th>
		    							<input type="hidden" name="yield[<?= $row['id']?>][rr_detail_id]" value="<?= $row['id']?>">
		    							<div class="checkbox"><label><input type="checkbox" class="toggle-include"> <?= $row['description']?></label></div>
		    						</th>
		    						<th>
		    							<div class="t"><?= $row['unit_description']?></div>
		    							pcs
	    							</th>
		    						<th>
			    						<div class="t">
			    							<input type="number" step="0.01" class="form-control" name="yield[<?= $row['id']?>][quantity]" />
		    							</div>
		    							<input type="number" step="0.01" class="form-control" name="yield[<?= $row['id']?>][pieces]" />
	    							</th>
	    							<th></th>
		    					</tr>
	    					</thead>
	    					<tfoot>
	    						<tr>
	    							<td colspan="4">
	    								<a class="add-line btn btn-default btn-sm btn-flat">
	    									<i class="fa fa-plus "></i> Add new line
										</a>
									</td>
									</tr>
	    					</tfoot>
	    					<?php $processed = isset($row['processed_to']) ? $row['processed_to'] : [[]] ?>
			    			<tbody data-length="<?= count($processed)?>">
			    				<?php foreach( $processed AS $index => $item ):?>
			    					<tr>
				    					<td>
				    						<?= arr_group_dropdown("yield[{$row['id']}][to][{$index}][product_id]", $product_list, 'id', 'description', FALSE, 'category_description', "class=\"form-control\" data-name=\"yield[{$row['id']}][to][idx][product_id]\"")?>
			    						</td>
			    						<td>
			    							<div class="t"><?= $row['unit_description']?></div>
			    							pcs
										</td>
			    						<td>
			    							<div class="t">
				    							<input type="number" class="form-control" step="0.01" name="<?= "yield[{$row['id']}][to][{$index}][quantity]" ?>" data-name="yield[<?= $row['id']?>][to][idx][quantity]"/>
			    							</div>
			    							<input type="number" step="0.01" class="form-control" name="<?= "yield[{$row['id']}][to][{$index}][pieces]"?>" data-name="yield[<?= $row['id']?>][to][idx][pieces]"/>
										</td>
										<td>
											<a class="btn btn-danger btn-sm btn-flat remove-line">
												<i class="fa fa-times"></i>
											</a>
										</td>
									</tr>
								<?php endforeach;?>
			    			</tbody>
		    			</table>
	    				</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach;?>
    		<pre class="hidden">
    			<?php print_r($data) ?>
    		</pre>
    		<hr>
    		<button type="submit" class="btn btn-success btn-flat">Submit</button>
    		<a class="pull-right" href="<?= base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$data['id']}")?>" >
    			<i class="fa fa-mail-reply"></i>
    			Go to purchase receiving report # <?= $data['id']?>
			</a>
    	</form>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

		var messageBox = $('.callout.callout-danger');

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

		})

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

		$('.toggle-include').change(function(){
			toggleInputs($(this));
		});

		toggleInputs($('.toggle-include'));

		function toggleInputs(el){
			if(el.prop('checked')){
				el.closest('table').find('input:not([type=checkbox]),select').removeAttr('disabled')
				return
			}	
			el.closest('table').find('input:not([type=checkbox]),select').attr('disabled', 'disabled')
		}


	});
</script>