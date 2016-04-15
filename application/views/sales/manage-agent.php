<div class="row">
    <div class="col-md-9">
        <div class="box box-success">
            <form class="pm-inventory-form"  method="post" action="<?= $url ?>">
                <div class="box-header">
                    <h3 class="box-title"><?= $form_title ?></h3>
                </div>
                <div class="box-body">
                    <?php if (isset($validation_errors)): ?>
                        <div class="callout callout-danger">
                            <h4>Errors!</h4>
                            <ul class="list-unstyled"><?= $validation_errors ?></ul>
                        </div>
                    <?php endif; ?>
                     <?php if (isset($form_submission_success)): ?>
                        <div class="callout callout-info">
                            <h4>Success</h4>
                            <ul class="list-unstyled"><?= $form_submission_success ?></ul>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">

                            <?= form_open($url, array('role' => 'form')) ?>
                            <div class="form-group">
                                <label for="agent-name">Name</label>
                                <?= form_input(array('name' => 'name', 'class' => 'form-control', 'id' => 'agent-name', 'required' => 'required', 'value' => $defaults['name'])); ?>
                            </div>  
                            <div class="form-group">
                                <label for="agent-area">Area</label>
                                <?= form_input(array('name' => 'area', 'class' => 'form-control', 'id' => 'agent-area', 'required' => 'required', 'value' => $defaults['area'])); ?>
                            </div>
                            <div class="form-group">
                                <label>Quota per month (Amount)</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= form_input(array('name' => 'unit_quantity', 'type'=>'number', 'class' => 'form-control', 'id' => 'agent-area', 'value' => $defaults['unit_quantity'])); ?>
                                    </div>
                                     <div class="col-md-6">
                                         <?php $attr = 'class="form-control"';?>
                                        <?= form_dropdown('fk_inventory_unit_id',  $units,$defaults['fk_inventory_unit_id'], $attr) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="quota-amount">Quota per month (Amount)</label>
                                <?= form_input(array('name' => 'amount', 'class' => 'form-control price', 'id' => 'quota-amount','value' => number_format($defaults['amount'],2))); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <?= form_button(array('type' => 'submit', 'class' => 'btn btn-primary', 'content' => 'Save')); ?>
                    <a href="<?= base_url('sales/agents') ?>" class="btn btn-danger pull-right">Cancel</a>
                </div>
                <?= form_close() ?> 
            </form>
        </div>
    </div>
</div>

