<style type="text/css">
	div.t{
        border-bottom:1px solid black;
    }
    table input{
    	text-align: right;
    }
</style>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title"><?= $form_title ?></h3>
    </div><!-- /.box-header -->

    <div class="box-body" id="yield-section">
    	<div class="callout callout-danger hidden">
            <h4>Error!</h4>
            <ul class="list-unstyled"></ul>
        </div>
    	<form data-action="<?= $form_action?>" method="POST">
    		<div class="form-group">
    			<label>Remarks</label>
    			<textarea class="form-control" name="remarks"></textarea>
    		</div>
    		<hr>
    		<?php 
    			if($yielding){
    				$sources = array_column($yielding['source'], NULL, 'fk_purchase_receiving_detail_id');
    			}
    		?>
			<?php foreach($data['details'] AS $row):?>
				<table class="table table-bordered table-condensed" style="margin-top:10px;border-bottom: 0;border-left: 0;border-right: 0">
    				<thead>
    					<tr class="info">
    						<th>ITEM</th>
    						<th>KGS</th>
    						<th>PIECES</th>
    						<th>UNIT PRICE</th>
    						<th colspan="2">AMOUNT</th>
    					</tr>
    					<?php $unit_price = $row['unit_price'] - ($row['discount'] / $row['this_receive'])?>
    					<tr>
    						<th>
    							<?php $checked = isset($sources[$row['id']]) ? 'checked="checked"' : FALSE?>
    							<?php if($checked):?>
	    							<?= form_hidden("yield[{$row['id']}][id]", $sources[$row['id']]['id'])?>
	    						<?php endif;?>
    							<input type="hidden" name="yield[<?= $row['id']?>][rr_detail_id]" value="<?= $row['id']?>">
    							<div class="checkbox">
    								<label>
    									<input type="checkbox" class="toggle-include" <?= $checked?> /> 
    									<?= $row['description']?>
									</label>
								</div>
    						</th>
    						
    						<th>
    							<input type="text" class="form-control yield-quantity" name="yield[<?= $row['id']?>][quantity]" value="<?= $checked ? $sources[$row['id']]['quantity'] : ''?>" />
							</th>
							<th>
    							<input type="text" class="form-control yield-pieces" name="yield[<?= $row['id']?>][pieces]" value="<?= $checked ? $sources[$row['id']]['pieces'] : ''?>"  />
							</th>
							<th class="text-right yield-unit-price" data-unit-price="<?= $unit_price?>"><?= number_format($unit_price, 2)?></th>
							<th class="yield-amount text-right" colspan="2">
								<?= $checked ? number_format($unit_price * $sources[$row['id']]['quantity'], 2) : ''?>
							</th>
    					</tr>
    					<tr  class="success">
    						<th colspan="2" style="background: white;border:0"></th>
    						<th>ITEM</th>
    						<th>KGS</th>
    						<th>PIECES</th>
    						<th></th>
    					</tr>
					</thead>

					<?php $processed = isset($sources[$row['id']]['result']) ? $sources[$row['id']]['result'] : [[]] ?>
	    			<tbody data-length="<?= count($processed)?>">
	    				<?php $total = ['kgs' => 0, 'pieces' => 0]; ?>
	    				<?php foreach( $processed AS $index => $item ):?>
	    					<tr>
	    						<td colspan="2" style="border:0"></td>
		    					<td>
		    						<?php if(isset($item['id'])):?>
		    							<?= form_hidden("yield[{$row['id']}][to][{$index}][id]", $item['id'])?>
		    						<?php endif;?>
		    						<?= arr_group_dropdown("yield[{$row['id']}][to][{$index}][product_id]", $product_list, 'id', ['text' => 'description', 'attr' => ['name' => 'category-id', 'value' => 'fk_category_id']], isset($item['fk_inventory_product_id']) ? $item['fk_inventory_product_id'] : FALSE, 'category_description', "class=\"form-control produce-product\" data-name=\"yield[{$row['id']}][to][idx][product_id]\"")?>
	    						</td>
	    						<td>
	    							<?php $kgs = (float) put_value($item, 'quantity', ''); ?>
	    							<input type="text" class="form-control produce-quantity" step="0.01" name="<?= "yield[{$row['id']}][to][{$index}][quantity]" ?>" value="<?= $kgs ?: '' ?>" data-name="yield[<?= $row['id']?>][to][idx][quantity]"/>
								</td>
	    						<td>
	    							<?php $pieces = (float)put_value($item, 'pieces', ''); ?>
	    							<input type="text" step="0.01" class="form-control produce-pieces" name="<?= "yield[{$row['id']}][to][{$index}][pieces]"?>" value="<?= $pieces ?: '' ?>" data-name="yield[<?= $row['id']?>][to][idx][pieces]"/>
								</td>
								<td>
									<a class="btn btn-danger btn-sm btn-flat remove-line">
										<i class="fa fa-times"></i>
									</a>
								</td>
								<?php 
									$total['kgs'] += $kgs;
									$total['pieces'] += $pieces;
								?>
							</tr>
						<?php endforeach;?>
	    			</tbody>
	    			<tfoot>
						<tr>
							<td colspan="2" style="border:0">
							<td style="border: 0"  class="text-center" style="vertical-align:top">
								<a class="add-line btn btn-default btn-sm btn-flat">
									<i class="fa fa-plus "></i> Add new line
								</a>
							</td>
							<td class="text-center active" colspan="2" style="vertical-align:middle;">
								<b>SUMMARY</b>
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
						<tr>
							<td colspan="3" style="border:0;"></td>
							<td style="vertical-align:middle;">
								<b>DRESS WEIGHT</b>
							</td>
							<td class="text-right total-dress-weight" style="vertical-align: middle">
								
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
                        <tr>
							<td colspan="3" style="border:0;"></td>
							<td style="vertical-align:middle;">
								<b>BYPRODUCTS WEIGHT</b>
							</td>
							<td class="text-right total-byproducts-weight" style="vertical-align: middle">
								
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
						<tr>
							<td colspan="3" style="border:0;"></td>
							<td style="vertical-align:middle;">
								<b>RECOVERY</b>
							</td>
							<td class="text-right total-recovery" style="vertical-align: middle">
								
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
						<tr>
							<td colspan="3" style="border:0;"></td>
							<td style="vertical-align:middle;">
								<b>DRESS COST</b>
							</td>
							<td class="text-right dress-cost" style="vertical-align: middle">
								
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
					</tfoot>
    			</table>
			<?php endforeach;?>
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
				$.growl.notice({'title':'Done','message': 'Further processing of the products have been successfully saved!'});
				// window.location.href = $('#cancel').attr('href');
				// window.location.reload();
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

		

		function toggleInputs(el){
			console.log('lol')
			var table = el.closest('table');
			if(el.prop('checked')){
				table.find('input:not([type=checkbox]),select').removeAttr('disabled')
				return
			}	
			table.find('input:not([type=checkbox]),select').attr('disabled', 'disabled')

		}

		$('.yield-quantity').blur(function(){
			var that = $(this),
				qty = that.val() || 0,
				tr  = that.closest('tr'),
				unitPrice = tr.find('.yield-unit-price').data('unit-price') || 0,
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
				dressCost = parseFloat(table.find('.yield-unit-price').data('unit-price')) / (recovery / 100);

			table.find('.total-dress-weight').text(numeral(totalDressWeight).format('0,0.00')+' kgs');
			table.find('.total-byproducts-weight').text(numeral(totalByproductsWeight).format('0,0.00')+' kgs');
			table.find('.total-recovery').text(recovery.toFixed(2)+'%');
			table.find('.dress-cost').text(numeral(dressCost).format('0,0.00'));

		});

		$('.toggle-include').each(function(){
			toggleInputs($(this));
		})
		
		$('.produce-quantity:not(:disabled):last').trigger('blur');
		


	});
</script>