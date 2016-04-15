<div class='row'>
    <div class='col-md-12'>
        <form class="form-inline" role="form" action='<?= base_url('reports/inventory_report/generate') ?>'>
            <div class="form-group">
                <label class="sr-only" for="daterangepicker">Date range</label>
                <input type="text" class="form-control" name='daterange' id="daterangepicker" placeholder="Select date range">
            </div>
            <div class="form-group">
                <label class="sr-only" for="product">Product</label>
                <?php $products = dropdown_format($products, 'id', array('description', 'code'), '[ALL PRODUCTS]') ?>
                <?= form_dropdown('fk_inventory_product_id', $products, FALSE, "class='form-control'") ?>
            </div>
            <button type="submit" class="btn btn-success">Generate report</button>
        </form>

        <div class='box box-info'>
            <div class="box-body no-padding table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Stock in</th>
                            <th>Stock out</th>
                            <th>Stock on hand</th>
                        </tr>
                    </thead>
                    <tbody id="report-content">
                    <tr>
                        <td colspan="6" class='text-center'>To start, specify a range date(or keep it blank) and/or choose a specific product.</td>
                    </tr>

                    </tbody></table>
            </div>
        </div>
    </div>
</div>