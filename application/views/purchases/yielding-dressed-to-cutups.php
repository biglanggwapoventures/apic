<style type="text/css">
	div.t{
        border-bottom:1px solid black;
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
    							<input type="number" step="0.01" class="form-control yield-quantity" name="yield[<?= $row['id']?>][quantity]" value="<?= $checked ? $sources[$row['id']]['quantity'] : ''?>" />
							</th>
							<th>
    							<input type="number" step="0.01" class="form-control yield-pieces" name="yield[<?= $row['id']?>][pieces]" value="<?= $checked ? $sources[$row['id']]['pieces'] : ''?>"  />
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
		    						<?= arr_group_dropdown("yield[{$row['id']}][to][{$index}][product_id]", $product_list, 'id', 'description', isset($item['fk_inventory_product_id']) ? $item['fk_inventory_product_id'] : FALSE, 'category_description', "class=\"form-control\" data-name=\"yield[{$row['id']}][to][idx][product_id]\"")?>
	    						</td>
	    						<td>
	    							<?php $kgs = put_value($item, 'quantity', ''); ?>
	    							<input type="number" class="form-control produce-quantity" step="0.01" name="<?= "yield[{$row['id']}][to][{$index}][quantity]" ?>" value="<?= $kgs ?>" data-name="yield[<?= $row['id']?>][to][idx][quantity]"/>
								</td>
	    						<td>
	    							<?php $pieces = put_value($item, 'pieces', ''); ?>
	    							<input type="number" step="0.01" class="form-control produce-pieces" name="<?= "yield[{$row['id']}][to][{$index}][pieces]"?>" value="<?= $pieces ?>" data-name="yield[<?= $row['id']?>][to][idx][pieces]"/>
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
	    				<?php 
	    					if($checked){
	    						$weight_loss = (100 -  (($total['kgs'] / $sources[$row['id']]['quantity']) * 100));
	    					}
    					?>
						<tr>
							<td colspan="2" style="border:0">
							<td style="border: 0"  class="text-center" style="vertical-align:top">
								<a class="add-line btn btn-default btn-sm btn-flat">
									<i class="fa fa-plus "></i> Add new line
								</a>
							</td>
							<td class="text-center active" colspan="2" style="vertical-align:middle;">
								<b>TOTAL</b>
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
						<tr>
							<td colspan="3" style="border:0;"></td>
							<td class="text-right active produce-total-quantity" style="vertical-align:middle;">
								<?= number_format($total['kgs'], 2)?> kgs
							</td>
							<td class="text-right active produce-total-pieces" style="vertical-align: middle">
								<?= number_format($total['pieces'], 2)?> pieces
							</td>
							<td class="text-right" style="border: 0"></td>
						</tr>
						<tr>
							<td colspan="3" style="border:0;"></td>
							<td colspan="2" class="warning text-center weight-loss">
								<?= isset($weight_loss) ? 'Weight Loss: <b>'.number_format($weight_loss, 2).'%</b>' : ''; ?>
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

		toggleInputs($('.toggle-include'));

		function toggleInputs(el){
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

		doTotalCalculation('.produce-quantity', '.produce-total-quantity', ' kgs');
		doTotalCalculation('.produce-pieces', '.produce-total-pieces', ' pieces');

		function doTotalCalculation(element, elementTotalPlaceholder, append){
			$('#yield-section').on('blur', element, function(){
				var totalQty = 0,
					table = $(this).closest('table');

				table.find('tbody tr').each(function(){
					var quantity = $(this)	.find(element).val() || 0;
					totalQty += parseFloat(quantity);
				});
				table.find(elementTotalPlaceholder).text(numeral(totalQty).format('0,0.00')+append);

				var weightLoss = 100 - (totalQty / parseFloat(table.find('.yield-quantity').val() || 0) * 100);
				table.find('.weight-loss').html('Weight Loss: <b>'+weightLoss.toFixed(2)+'%</b>');
			});

			

		}

		// $('#yield-section').on('blur', '.produce-quantity', function(){
		// 	var that = $(this),
		// 		table = that.closest('table'),
		// 		yieldQty = table.find('.yield-quantity').val() || 0,
		// 		unitPrice = table.find('.yield-unit-price').data('unit-price') || 0,
		// 		yieldCost = parseFloat(yieldQty) * parseFloat(unitPrice),
		// 		produceQty = parseFloat(that.val() || 0);

		// 	if(yieldCost > 0){

				
		// 		var percentage = (produceQty / yieldQty),
		// 			thisCost = yieldCost * percentage;

		// 		that.closest('tr').find('.produce-unit-cost').text(numeral(thisCost/produceQty).format('0,0.00'))
		// 		that.closest('tr').find('.produce-total-cost').text(numeral(thisCost).format('0,0.00'))
		// 	}

			


		// });
		


	});
</script>