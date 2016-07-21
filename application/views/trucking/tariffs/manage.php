<?php $url = base_url('trucking/tariffs'); ?>
<div class="row" id="form">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $title ?></h3>
            </div>
            <form data-action="<?= $action ?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden">
                      <ul class="list-unstyled">
                        
                      </ul>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Code: </label>
                            <input name="code" style="text-align: left" type="text" class="form-control" value="<?= put_value($data, 'code', '')?>">
                        </div>
                    </div>
                    <div class="row">
                         <div class="form-group col-md-4">
                            <label>Option</label>
                            <?=option_dropdown('option', put_value($data, 'option', ''), 'class="form-control option"')?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Location</label>
                            <?= form_dropdown('fk_location_id', ['' => ''] + array_column($locations, 'name', 'id'), put_value($data, 'fk_location_id', ''), 'class="form-control"')?>
                        </div>
                    </div>
                </div> 

            <fieldset style="margin-top:15px;">
                <legend class="tableLabel"></legend>
                <div class="row">
                    <div class="col-md-12" id="tableData">
                        <table class="table location-details" id="less">
                            <thead>
                                <tr class="bg-navy">
                                    <th id="optionVal">Location</th><th>Rate</th><th>Kms</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php $less_items = empty($data['less']) ? [[]] : $data['less']; ?>
                                    <?php foreach($less_items AS $index => $item):?>
                                    <tr>
                                        <td>
                                        <?php if(isset($item['id'])):?>
                                            <?= form_hidden("less[{$index}][id]", $item['id'])?>
                                            <?php endif;?>
                                            <input type="hidden" value="" name="" data-name="" />
                                            <?= form_dropdown("less[{$index}][fk_location_id]", ['' => ''] + array_column($locations, 'name', 'id'), put_value($item, 'fk_location_id', ''), 'class=" form-control" data-name="less[idx][fk_location_id]"')?>
                                        </td>
                                        <td><?= form_input("less[{$index}][rate]",put_value($item, 'rate',''), 'class="form-control pformat rate text-right" type="number" data-name="less[idx][rate]"')?></td>
                                        <td><?= form_input("less[{$index}][kms]",put_value($item, 'kms', ''), 'class="form-control pformat kms text-right" type="number" data-name="less[idx][kms]"')?></td>
                                        <td>
                                            <a class="btn btn-danger btn-flat btn-sm remove-line" role="button"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <a class="btn btn-default btn-flat btn-sm" id="add-line" role="button"><i class="fa fa-plus"></i> Add new line</a>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>   
                </fieldset>
            <?php if (can_set_status()): ?>
                 <div class="checkbox">
                    <label><input type="checkbox" name="approved_by"<?= ($data['approved_by'])?" checked":"";?>/> Mark this tariff as <b>approved</b></label>
                </div>
            <?php endif;?>
                <div class="box-body">
                    <button id="send" class="btn btn-flat btn-success <?= can_update($data) ? '' : 'disabled'?>">Submit</button>
                    <a class="btn btn-flat btn-warning" id="cancel" href="<?= $url?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

</script>