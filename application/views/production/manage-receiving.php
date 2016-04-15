<form action="<?=$form_action?>">
    <div class="box box-solid">
       <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
            <h3 class='box-title'><?= $form_title ? $form_title : '' ?></h3>
        </div><!-- /.box-heading -->
        <div class="box-body">
            <div class='callout callout-danger hidden'>
                <ul class='list-unstyled'>
                </ul>
            </div>
            <div class='row'>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label>Datetime Received: </label>
                        <input type="text" name="datetime" class="form-control datetimepicker" required="required"
                                value="<?= isset($data['rr']['datetime']) ? $data['rr']['datetime'] : date('m/d/Y h:i:s A')?>"/>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label>Production code:</label>
                        <?php if(isset($data['unreceived'])):?>
                            <?= arr_group_dropdown('jo_no', $data['unreceived'], 'id', 'production_code', FALSE, FALSE, 'required="required" class="form-control" id="job-orders" data-get-details-url="'.base_url('production/job_order/get_details').'"')?>
                        <?php else:?>
                            <p class="form-control-static"><?= $data['rr']['production_code']?></p>
                        <?php endif;?>
                    </div>
                </div>
            </div><!-- /.row -->
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control"><?= isset($data['rr']['remarks']) ? $data['rr']['remarks'] : ''?></textarea>
            </div>
            <hr>
            <div class='row'>
                <div class='col-md-12'>
                    <table class='table'>
                        <thead><tr class='active'><th>Product Description</th><th>Tons/Mix</th><th>Quantity</th><th>Packaging</th></tr></thead>

                        <tbody id="details">
                            <?php if(isset($data['rr'])):?>
                                <?php foreach($data['rr']['details'] AS $row):?>
                                    <tr>
                                        <td>
                                            <?php if($row['id']):?>
                                                <input type="hidden" name="id[]" value="<?=$row['id']?>"/>
                                            <?php endif;?>
                                            <input type="hidden" name="jo_detail_id[]" value="<?=$row['jo_detail_id']?>" />
                                            <?= "{$row['description']} [{$row['formulation_code']}]"?>
                                        </td>
                                        <td><?= $row['mix_number']?></td>
                                        <td><input type="number" step="0.01" class="form-control" min="0" name="quantity[]" value="<?= $row['quantity']?>"/></td>
                                        <td><?= $row['unit']?></td>
                                    </tr>
                                <?php endforeach;?>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr/>
            <?php if($this->session->userdata('type_id') == M_Account::TYPE_ADMIN):?>
                <?php $checked = isset($data['rr']['approved_by']) && !empty($data['rr']['approved_by']) ? 'checked="checked"' : ''?>
                <div class="checkbox"><label><input type="checkbox" name="is_approved" <?=$checked?>/> Mark this receiving report as approved</label></div>
            <?php endif;?>
        </div><!-- /.box-body -->
        <div class="box-footer">
            <input type='submit' value='Save' class='btn btn-flat btn-success'/>
            <a href='<?= base_url('production/receiving') ?>' class='btn btn-flat btn-danger pull-right cancel' role='button'>Go back</a>
        </div><!-- /.box-footer -->
    </div><!-- /.box -->
</form>