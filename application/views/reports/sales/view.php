<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid">
            <div class="box-header">
                <div class="box-tools">
                    <form id="report" action="<?= base_url('reports/sales/fetch') ?>" class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" id="daterangepicker" name="daterange" placeholder="Date range"/>
                        </div>
                        <div class="form-group">
                            <?= generate_customer_dropdown('customer', FALSE, 'class="form-control input-sm"', 'All customers') ?>
                        </div>
                        <div class="form-group">
                            <?= generate_product_dropdown('product', FALSE, 'class="form-control input-sm"', 'All products') ?>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-flat">Generate</button>
                    </form>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-condensed table-hover" id="sr">
                            <tbody>
                            <thead><tr class="active"><th class="text-center">PL NO.</th><th class="text-center">SO NO.</th><th class="text-center">PO NO.</th><th class="text-center">CUSTOMER</th><th class="text-center">DATE</th><th class="text-center">PRODUCT</th><th class="text-center">QUANTITY</th><th class="text-center">UNIT PRICE</th><th class="text-center">DISCOUNT</th><th class="text-center">AMOUNT</th></tr></thead>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="report-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <p class="text-center"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>