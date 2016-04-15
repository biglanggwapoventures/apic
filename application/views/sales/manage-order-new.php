<style type="text/css">
    .text-white-important{
        color:#FFFFFF!important;
    }
    table tbody td:last-child{
        text-align: center;
    }
    table tbody td:nth-child(7),td:nth-child(8){
        text-align: right;
    }
    table thead th{
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
                <h3 class="box-title"><?= $form_title; ?></h3>
                <div class="pull-right box-tools" >
                    <a title="Go to previous sales order" class="btn btn-primary btn-sm text-white-important"><i class="glyphicon glyphicon-backward"></i></a>    
                    <a title="Go to next sales order" class="btn btn-primary btn-sm text-white-important"><i class="glyphicon glyphicon-forward"></i></a>           
                </div><!-- /. tools -->
            </div>
            <form id="create-order" data-get-customer-list-url="<?= base_url('sales/customers/a_get_list') ?>"
                  data-get-customer-pricing-url="<?= base_url('sales/customers/a_price_list') ?>">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <select class="form-control" id="customer"></select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="po">P.O. No.</label>
                                <input type="text" class="form-control" id="po"/>
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-offset-2">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="text" class="form-control datepicker has-default" id="date"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks"></textarea>
                    </div>
                    <hr/>
                    <div class="callout callout-info">
                        <strong>Note:</strong> The discount field indicates discount per unit.
                    </div>
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr class="info">
                                <th style="width:15%">Product</th>
                                <th style="width:15%">Sales Agent</th>
                                <th style="width:10%">Quantity</th>
                                <th style="width:10%">Unit</th>
                                <th style="width:12%">Unit Price</th>
                                <th style="width:12%">Discount</th>
                                <th style="width:10%">Gross</th>
                                <th style="width:10%">Net</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr><td colspan="6"></td><td>Total amount:</td><td class="text-right">0.00</td><td></td></tr>
                        </tfoot>
                        <tbody>
                            <tr id="no-customer-selected"><td class="bg-red text-center" colspan="9">Please select a customer to continue.</td></tr>
                            <tr class="hidden">
                                <td><select class="form-control input-sm"></select></td>
                                <td><select class="form-control input-sm"></select></td>
                                <td><input type="number" class="form-control input-sm"/></td>
                                <td>bags (25 kgs)</td>
                                <td><input type="text" class="form-control input-sm"/></td>
                                <td><input type="text" class="form-control input-sm"/></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td><a class="btn btn-sm btn-danger btn-flat"><i class="glyphicon glyphicon-remove"></i></a></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <div class="box-footer">
                    <div class="btn-toolbar clearfix">
                        <button class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn btn-success"><i class="fa fa-check"></i> Save and approve</button>
                        <button class="btn btn-warning"><i class="fa fa-times"></i> Cancel</button>
                        <a class="btn btn-danger pull-right">Go back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>