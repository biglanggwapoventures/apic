<input type="hidden" name="data-so-link" value="<?= base_url('sales/orders/update') ?>">
<div class='row'>
    <div class='col-md-12'>
        <form class="form-inline" role="form" action='<?= base_url('reports/order_reports/generate') ?>'>
            <div class="form-group">
                <label class="sr-only" for="daterangepicker">Date range</label>
                <input type="text" class="form-control" name='daterange' id="daterangepicker" placeholder="Select date range">
            </div>
            <div class="form-group">
                <label class="sr-only" for="customer">Customer</label>
                <?php $customer_list = dropdown_format($customer_list, 'id', array('company_name', 'customer_code'), '[ALL CUSTOMERS]') ?>
                <?= form_dropdown('customer_id', $customer_list, FALSE, 'class="form-control"', 'customer') ?>
            </div>
            <button type="submit" class="btn btn-success">Generate report</button>
        </form>

        <div class='box box-info'>
            <div class="box-body no-padding table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%">S.O. No.</th>
                            <th style="width: 5%">P.O No.</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Packaging</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th style="width: 10%">Status</th>
                        </tr>
                    </thead>
                    <tbody id='report-content'>

                        <tr>
                            <td colspan="9" class='text-center'>To start, specify a range date(or keep it blank) and a customer.</td>
                        </tr>

                    </tbody></table>
            </div>
        </div>
    </div>
</div>