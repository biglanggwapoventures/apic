<input type="text" data-name="raw-products" data-value="<?= htmlspecialchars(json_encode($raw_products)) ?>" disabled="disabled" hidden="hidden"/>
<input type="text" data-name="values" data-value="<?= htmlspecialchars(json_encode($values)) ?>" disabled="disabled" hidden="hidden"/>

<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
                <!-- tools box -->

                <i class="glyphicon glyphicon-plus-sign"></i>
                <h3 class="box-title">
                    <?= $FORM_TITLE ?>
                </h3>
            </div>
            <form class="form" action="<?= $URL_FORM_SUBMIT ?>" method="post">
                <div class="box-body">
                    <div class="callout callout-warning hidden" id="validation-errors">
                        <h4>Validation Errors!</h4>
                        <ul class="list-unstyled">
                            
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="formulation-code">Formulation Code:</label>
                                <input type="text" class="form-control" name="formulation_code" id="formulation-code" required="required"/>
                                <span class="help-block">
                                    * This is field is required!
                                </span>
                            </div>

                        </div>
                    </div>

                    <div class='row'>

                        <div class='col-md-8'>
                            <hr>
                            <table class="table table-condensed table-bordered" style="border-bottom:none;border-left: none" id="formula-table">
                                <thead>
                                    <tr class="active">
                                        <th>Raw Product</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit cost</th>
                                        <th>Net cost</th>
                                        <th style="width:5%"><a class="btn btn-flat btn-sm btn-info add-formula"><i class="fa fa-plus"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody><tr class="add-line-notif"><td colspan="6" class="text-center">Click the <i class="fa fa-plus"></i> above to add a line.</td></tr></tbody>
                                <tfoot>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    
                                    <tr class="active"><td>Formulation cost</td><td id="total-cost" class="text-right" style="font-size:130%"></td></tr>
                                    <tr class="active"><td>Net weight</td><td id="total-kgs" class="text-right" style="font-size:130%"></td></tr>
                                    <tr class="active"><td>Cost per kilogram</td><td id="cost-kg" class="text-right" style="font-size:130%"></td></tr>
                                    
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:30px">
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1" checked> Active
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="0"> Inactive
                        </label>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="btn-toolbar">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <a href="<?= $URL_BASE ?>" class="btn btn-danger" id="btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>