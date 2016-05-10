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
    <form data-action="<?= $form_action?>" method="POST">
	    <div class="box-body" id="yield-section">
	    	<div class="callout callout-danger hidden">
	            <h4>Error!</h4>
	            <ul class="list-unstyled"></ul>
	        </div>
    		<input type="hidden" name="yield_type" value="<?= $type?>">
    		<div class="form-group">
    			<label>Remarks</label>
    			<textarea class="form-control" name="remarks"><?=isset($data['yielding']) ? $data['yielding']['remarks'] : ''?></textarea>
    		</div>
    		<hr>
    		<?php $source = put_value($data, 'source', []); ?>
			<table class="table table-bordered table-condensed" style="margin-top:10px;border-bottom: 0;border-left: 0;border-right: 0">
				<thead>
					<tr class="info">
						<th>ITEM</th>
						<th>KGS</th>
						<th>PIECES</th>
						<th>COST/KG</th>
						<th colspan="2">TOTAL COST</th>
					</tr>
					<tr>
						<th>
							<?php if(isset($source['id'])):?>
    							<?= form_hidden("yield[id]", $source['id'])?>
    						<?php endif;?>
    						<?= arr_group_dropdown("yield[fk_inventory_product_id]", $source_items, 'id', 'description', put_value($source, 'fk_inventory_product_id', FALSE), FALSE, "class=\"form-control\"")?>
						</th>
						
						<th>
							<input type="text" class="form-control yield-quantity" name="yield[quantity]" value="<?= put_value($source, 'quantity', '')?>" />
						</th>
						<th>
							<input type="text" step="0.01" class="form-control yield-pieces" name="yield[pieces]" value="<?= put_value($source, 'pieces', '')?>"  />
						</th>
						<th class="text-right yield-unit-price">
							<input type="text" class="form-control" name="yield[unit_price]" value="<?= put_value($source, 'unit_price', '')?>"/>
						</th>
						<th class="yield-amount text-right" colspan="2">

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

				<?php $processed = isset($data['result']) && count($data['result']) ? $data['result'] : [[]]; ?>

    			<tbody data-length="<?= count($processed)?>">
    				<?php foreach( $processed AS $index => $item ):?>
    					<tr>
    						<td colspan="2" style="border:0"></td>
	    					<td>
	    						<?php if(isset($item['id'])):?>
	    							<?= form_hidden("yield[to][{$index}][id]", $item['id'])?>
	    						<?php endif;?>
	    						<?= arr_group_dropdown("yield[to][{$index}][product_id]", $result_items, 'id', ['text' => 'description', 'attr' => ['name' => 'category-id', 'value' => 'fk_category_id']], put_value($item, 'fk_inventory_product_id', FALSE), 'category_description', "class=\"form-control produce-product\" data-name=\"yield[to][idx][product_id]\"")?>
    						</td>
    						<td>
    							<?php $kgs = (float)put_value($item, 'quantity', ''); ?>
    							<input type="text" class="form-control produce-quantity" step="0.01" name="<?= "yield[to][{$index}][quantity]" ?>" value="<?= $kgs ?: '' ?>" data-name="yield[to][idx][quantity]"/>
							</td>
    						<td>
    							<?php $pieces = (float)put_value($item, 'pieces', ''); ?>
    							<input type="text" step="0.01" class="form-control produce-pieces" name="<?= "yield[to][{$index}][pieces]"?>" value="<?= $pieces ?: '' ?>" data-name="yield[to][idx][pieces]"/>
							</td>
							<td>
								<a class="btn btn-danger btn-sm btn-flat remove-line">
									<i class="fa fa-times"></i>
								</a>
							</td>
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
	    </div>
	    <div class="box-footer clearfix">
	    	<button type="submit" class="btn btn-success btn-flat">Submit</button>
	    	<a class="pull-right btn btn-default btn-flat" id="cancel" href="<?= base_url('production/yielding')?>">Go back</a>
	    </div>
    </form>
</div>