<?php $url = base_url('tracking/trip_tickets'); ?>
<div class="box box-solid">
    <div class="box-header with-border"><h3 class="box-title"><?= $section_title ?></h3></div>
    <div class="box-body">
        <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
        <form data-action="<?= $form_action ?>">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Customer</label>
                        <?php if(!isset($data['company_name'])):?>
                            <?= generate_customer_dropdown('fk_sales_customer_id', FALSE,  'class="form-control"')?>
                        <?php else:?>
                            <?= generate_customer_dropdown('fk_sales_customer_id', $data['fk_sales_customer_id'],  'class="form-control"')?>
                        <?php endif;?>
                    </div>
                </div>
                <div class="col-sm-4">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <?php $date = isset($data['date']) ? date_create($data['date'])->format('m/d/Y'): ''?>
                            <input type="text" class="form-control datepicker" value="<?= $date?>" name="date"/>

                        </div> 
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Trucking</label>
                        <?= form_dropdown('fk_sales_trucking_id', $truckings, put_value($data, 'fk_sales_trucking_id', FALSE), 'class="form-control"');?>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Trucking Assistant</label>
                        <?= form_dropdown('fk_trucking_assistant_id', $trucking_assistants, put_value($data, 'fk_trucking_assistant_id', FALSE), 'class="form-control"');?>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Trip Type</label>
                        <?=trip_dropdown('trip_type', put_value($data, 'trip_type', ''), 'class="form-control option"')?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="date">Remarks</label>
                         <?= form_textarea('remarks', put_value($data, 'remarks', ''), 'class="form-control" rows="3"')?>
                    </div>  
                </div>
            </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="approved_by"<?= ($data['approved_by'])?" checked":"";?>/> Mark this trip ticket as <b>approved</b></label>
                </div>
            <hr>
            <button class="btn btn-flat btn-success">Submit</button>
            <a class="btn btn-default btn-flat" id="go-back" href="<?= base_url('tracking/trip_tickets')?>" role="button">Go back</a>
        </form>
    </div>
</div>
