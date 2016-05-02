<style type="text/css">
    @media print {
      a[href]:after {
        content: none !important;
      }
    }
</style>
<div class="row" id="print-area">
    <div class="col-sm-12">
        <div class="box box-solid">
            <div class="box-header">
                <div class="box-tools" style="position: absolute;width: 100%;z-index: 999">
                    <a id="print-report" href="#"><i class="fa fa-print"></i> Print page</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="oplr" class="table table-bordered table-condensed borderless"
                               data-url-fetch="<?= base_url('reports/outstanding_packing_list/fetch') ?>"
                               data-customer-src="<?= htmlentities(format_customer_editable(TRUE)) ?>">
                            <tbody>
                                <tr class="borderless static"><td colspan="15" class="text-center text-bold text-underline font-16">OUTSTANDING PACKING LIST PER CUSTOMER</td></tr>
                                <tr class="borderless static"><td colspan="15" class="no-padding text-center">As of: <?=date("F d, Y")?></td></tr>
                                <tr class="borderless static"><td class="text-right" colspan="2">Customer:</td><td colspan="8" class="font-normal"><a id="customer-list-editable" data-type="select" data-title="Select customer"></a></td><td class="text-right" colspan="3">Credit Term: </td><td class="font-normal text-center" id="customer-credit-term"></td></tr>
                                <tr class="borderless static"><td class="text-right" colspan="2">Customer Code:</td><td colspan="8" class="font-normal" id="customer-code"></td><td class="text-right" colspan="3">Credit Limit: </td><td class="font-normal text-center" id="customer-credit-limit"></td></tr>
                                <tr class="static"><td rowspan="2" colspan="2" class="text-center">DATE</td><td class="text-center" colspan="4">PACKING LIST DETAILS</td><td colspan="7" class="text-center">PAYMENT DETAILS</td><td rowspan="2" class="text-center">BALANCE</td><td rowspan="2" class="text-center">MONTH<br>BALANCE</td></tr>
                                <tr class="static"><td class="text-center">PL#</td><td  class="text-center">SI#</td><td class="text-center" colspan="2">AMT</td><td class="text-center">DATE</td><td class="text-center">AR#</td><td class="text-center">CHK#</td><td class="text-center">CHK DATE</td><td class="text-center">DEPOSIT DATE</td><td class="text-center">AMT</td><td class="text-center">CM</td></tr>
                                 <tr><td colspan="15" class="text-center">Please select a customer to begin</td></tr>
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

<div id="print-div" class="hidden">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h5 style="margin-bottom:0;font-weight: bold">
                ARDITEZZA POULTRY INTEGRATION CORPORATION
            </h5>
        </div><br/>
        <div class="col-sm-12 text-center">
            <th colspan="10" class="text-center font-normal">
                Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067
            </th>
        </div>
    </div><br/>
    <div id="table-dummy"></div>
</div>