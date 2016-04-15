<div class="box box-solid">
    <div class="box-body">
        <table id="oplr" class="table table-bordered table-condensed borderless"
               data-url-fetch="<?= base_url('reports/outstanding_packing_list/fetch') ?>">
            <tbody>
                <tr class="borderless static">
                    <td colspan="6" class="text-center text-bold text-underline font-16">OUTSTANDING PAYABLES PER SUPPLIER</td>
                </tr>
                <tr class="borderless static">
                    <td colspan="6" class="no-padding text-center">As of: <?=date("F d, Y")?></td>
                </tr>
                <tr class="borderless static">
                    <td class="text-right" colspan="2">Supplier:</td>
                    <td colspan="2" class="font-normal">
                        <a data-toggle="modal" data-target="#report-modal">
                            <?= $supplier_info ? $supplier_info['name'] : 'Click to choose a supplier'?>
                        </a>
                    </td>
                    <td class="text-right">Contact Number: </td>
                    <td class="font-normal"><?= $supplier_info ? $supplier_info['contact_number'] : ''?></td>
                </tr>
                <tr class="borderless static">
                    <td class="text-right" colspan="2">Supplier Address:</td>
                    <td colspan="2" class="font-normal"><?= $supplier_info ? $supplier_info['address'] : ''?></td>
                    <td class="text-right">Contact Person: </td>
                    <td class="font-normal"><?= $supplier_info ? $supplier_info['contact_person'] : ''?></td>
                </tr>
                <tr class="static">
                    <td rowspan="2" colspan="2" class="text-center">DATE</td>
                    <td class="text-center" colspan="3">PURCHASE RR DETAILS</td>
                    <td rowspan="2" class="text-center">BALANCE</td>
                </tr>
                <tr class="static">
                    <td class="text-center">RR NO.</td>
                    <td class="text-center">PO NO.</td>
                    <td class="text-center">AMOUNT</td>
                </tr>
                <?php if($data === FALSE):?>
                    <tr><td colspan="6" class="text-center">Please select a supplier to begin</td></tr>
                <?php elseif (count($data) === 0): ?>
                    <tr><td colspan="6" class="text-center">No outstanding payables for the selected supplier.</td></tr>
                <?php else:?>
                    <?php $rr_url = base_url("purchases/receiving/manage?do=update-purchase-receiving&id=")?>
                    <?php $po_url = base_url("purchases/orders/manage?do=update-purchase-order&id=")?>
                    <?php $total = 0?>
                    <?php foreach($data AS $row):?>
                        <tr>
                            <td class="text-center" colspan="2"><?= date('m/d/Y', strtotime($row['date']))?></td>
                            <td class="text-center"><a target="_blank" href="<?="$rr_url{$row['id']}"?>"><?= $row['id']?></a></td>
                            <td class="text-center"><a target="_blank" href="<?="$po_url{$row['po_no']}"?>"><?= $row['po_no']?></a></td>
                            <td class="text-right"><?= number_format($row['amount'], 2)?></td>
                            <td class="text-right"><?= number_format($row['amount'], 2)?></td>
                            <?php $total += $row['amount'];?>
                        </tr>
                    <?php endforeach;?>
                    <tr><td colspan="4" class="no-border"></td><td class="text-center "><strong>TOTAL:</strong></td><td class="text-right "><strong><?=number_format($total,2)?></strong></td></tr>
                <?php endif;?>
                <tr><td colspan="14" class="no-border">Time elapsed: <?= $this->benchmark->elapsed_time();?>s</tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="report-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Supplier list</h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Supplier name: </label>
                        <?= arr_group_dropdown('supplier_id', $suppliers, 'id', 'name', $supplier_info ? $supplier_info['id'] : FALSE, FALSE, 'class="form-control"')?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-flat">Generate</button>
                    <button type="button" data-dismiss="modal" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>