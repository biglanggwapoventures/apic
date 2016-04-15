<div class='row'>
    <div class='col-md-12'>
        <form class="form-inline" role="form" action='<?= base_url('reports/income_statement/generate') ?>'>
            <div class="form-group">
                <label class="sr-only" for="daterangepicker">Date range</label>
                <input type="text" class="form-control" name='daterange' id="daterangepicker" placeholder="Select date range">
            </div>
            <button type="submit" class="btn btn-success">Generate report</button>
        </form>

        <div class='box box-info'>
            <div class="box-body no-padding table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Income</th>
                            <th>Cost of Goods</th>
                            <th>Expenses</th>
                        </tr>
                    </thead>
                    <tfoot><tr><td colspan="3" class="text-right">Total Income:</td><td><b>0.00</b></td></tr></tfoot>
                    <tbody id='report-content'>

                        <tr>
                            <td colspan="4" class='text-center'>To start, specify a range date(or keep it blank).</td>
                        </tr>

                    </tbody></table>
            </div>
        </div>
    </div>
</div>