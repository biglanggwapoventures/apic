<?php $url = base_url('trucking/packing_list'); ?>
<style type="text/css">
    .text-white-important{
        color:#FFFFFF!important;
    }
    table tbody td:nth-child(7),td:nth-child(8),td:nth-child(6){
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
            </div>
            <?= form_open('', array('role' => 'form', 'data-action' => $form_action)) ?>
            <div class="box-body clearfix">
                <div class="callout callout-danger hidden" id="messages">
                    <h4>Oops!</h4>
                    <ul class="list-unstyled"></ul>
                </div>
                <div class="row">
                    <div class="col-sm-6">
     
                    </div>
                    <div class="col-sm-6 text-right">

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="customer-name">Customer</label>
                            <input type="hidden" name="data-trip-ticket-url" disabled="disabled" value="<?= base_url('trucking/packing_list/get_trip_ticket') ?>"/>
                            <?= form_dropdown('fk_sales_customer_id', $customers, $defaults['fk_sales_customer_id'], 'class="form-control" id="customer-name"') ?>
                          
                        </div> 
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group" id="trip-ticket">
                            <label for="trip-ticket">Trip Ticket</label>
                            <?php if($defaults['fk_trip_ticket_id']){?>
                            <?= form_dropdown('fk_trip_ticket_id', $trip_ticket, $defaults['fk_trip_ticket_id'], 'class="form-control" id="default-trip"');?>
                            <?php }?>
                        </div>  
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <?= form_input(array('name' => 'date', 'class' => 'form-control datepicker', 'id' => 'date', 'value' => $defaults['date'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="tariff">Tariff</label>
                            <input type="hidden" name="data-tariff-detail-url" disabled="disabled" value="<?= base_url('trucking/packing_list/get_tariff_details') ?>"/>
                            <?= form_dropdown('fk_tariff_id', $tariffs, $defaults['fk_tariff_id'], 'class="form-control" id="tariff"');?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Origin/Destination</label>
                            <p class="form-control-static text-center bg-green" id="option">
                            <?php if($defaults['option']==1) echo 'Origin'; else if($defaults['option']==2) echo 'Destination'?></p>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Location</label>
                             <p class="form-control-static text-center bg-green" id="location">
                            <?= $defaults['location'];?></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Km reading start</label>
                            <?= form_input('km_reading_start', put_value($defaults, 'km_reading_start',0), 'class="form-control text-right pformat"')?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Km reading end</label>
                            <?= form_input('km_reading_end', put_value($defaults, 'km_reading_end',0), 'class="form-control text-right pformat"')?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row" id="packing-list-details">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered table-condensed table-hover" style="border-bottom: none;border-left: none;border-right: none;">
                            <thead>
                                <tr class="bg-navy">
                                    <th width="19%">Location</th>
                                    <th>Rate</th>
                                    <th>Heads/pieces</th>
                                    <th>Amount</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="order-line">

                                <?php $ids = isset($less) ? array_column(json_decode(json_encode($less), TRUE), 'detail_id') : [];?>
                                <tr id="template">
                                    <td class="text-center">
                                        <?php if(isset($defaults['less']['id'][0])): ?>
                                            <input type="hidden" name="less[id][]" class="detail-id" value="<?= $defaults['less']['id'][0] ?>"/>
                                        <?php endif; ?>

                                        <?php if (isset($less)):?>
                                            <span class="location"><p class="form-control-static center"><?= $defaults['less']['location'][0] ?></p></span>
                                            <input class="locationH" name="less[fk_location_id][]" value="<?= $defaults['less']['fk_location_id'][0] ?>" hidden></input>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <span class="rate"><?= $defaults['less']['rate'][0] ?></span>
                                        <input class="rateH" name="less[rate][]" value="<?= $defaults['less']['rate'][0] ?>" hidden></input>
                                    </td>
                                    <td>
                                        <?= form_input(array('name' => 'less[pcs][]', 'class' => 'form-control pformat for-calculation pcs text-right', 'value' => number_format($defaults['less']['pcs'][0],2))); ?>
                                    </td>
                                    <td class="text-right">
                                    <span class="amount"></span>
                                    <input class="amountH" name="less[amount][]" value="<?= $defaults['less']['amount'][0] ?>" class="hidden" hidden></input>

                         
                                    </td>
                                    <td class="text-center"><button type='button' class='btn btn-danger btn-sm btn-flat remove-line'><i class='fa fa-times'></i></button></td>
                                </tr>
                                 
                                <?php for ($x = 1; $x < count($defaults['less']['fk_location_id']); $x++): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if(isset($defaults['less']['id'][$x])): ?>
                                                <?= form_hidden('less[id][]', $defaults['less']['id'][$x]); ?>
                                            <?php endif; ?>

                                            <span class="location"><p class="form-control-static"><?= $defaults['less']['location'][$x] ?><p></span>
                                            <input class="locationH" name="less[fk_location_id][]" value="<?= $defaults['less']['fk_location_id'][$x] ?>" hidden></input>      

                                        </td>
                                        <td class="text-right">
                                        <span class="rate"> <?= $defaults['less']['rate'][$x] ?></span>
                                        <input class="rateH" name="less[rate][]" value="<?= $defaults['less']['rate'][$x] ?>" hidden></input>
                                        </td>
                                        <td>
                                        <?= form_input(array('name' => 'less[pcs][]', 'class' => 'form-control pformat for-calculation pcs text-right', 'value' => number_format($defaults['less']['pcs'][$x],2))); ?>
                                        </td>
                                        <td class="text-right">
                                            <span class="amount"></span>
                                            <input class="amountH" value="<?= $defaults['less']['amount'][$x] ?>" name="less[amount][]" class="hidden" hidden></input>
                                        </td>
                                        <td class="text-center"><button type='button' class='btn btn-danger btn-flat btn-sm remove-line'><i class='fa fa-times'></i></button></td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                            <tfoot>
                                <tr><td colspan="4" class="no-border"></td><td class="text-center"><a class="add-line btn btn-flat btn-primary btn-sm"><i class="fa fa-plus"></i></a></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                   <div class="col-md-6 col-md-offset-3">
                        <table class="table table-bordered tabel-condensed">
                            <tbody>
                                 <tr>
                                    <td colspan="2" class="text-center active"><strong>SUMMARY</strong></td>
                                </tr>
                                 <tr>
                                    <td class="bg-teal"><strong>TOTAL AMOUNT</strong></td>
                                    <td class="text-right" id="total-amount"></td>
                                </tr>
                                <tr>
                                    <td  class="warning"><strong>LESS ADJUSTMENTS</strong></td>
                                    <td><?= form_input('adjustments', put_value($defaults, 'adjustments',0), 'class="form-control text-right for-calculation pformat adjustments"')?></td>
                                </tr>
                                <tr>
                                    <td  class="bg-aqua"><strong>OTHER CHARGES</strong></td>
                                    <td><?= form_input('other_charges', put_value($defaults, 'other_charges',0), 'class="form-control text-right for-calculation pformat other-charges"')?></td>
                                </tr>
                                <tr>
                                    <td  class="bg-green"><strong>NET AMOUNT DUE</strong></td>
                                    <td class="text-right" class="net-amount" id="net-amount-due">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <hr>
            <?php if (can_set_status()): ?>
                 <div class="checkbox">
                    <label><input type="checkbox" name="approved_by"<?= ($defaults['approved_by'])?" checked":"";?>/> Mark this packing list as <b>approved</b></label>
                </div>
            <?php endif;?>
            </div>
            <div class="box-footer clearfix">
                <button type="submit" class="btn btn-success btn-flat">Submit</button>
                <a class="btn btn-default btn-flat" href="<?= base_url('trucking/packing_list')?>" id="cancel">Go back</a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<div class="modal fade bs-loading-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hidden" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Loading...</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">Retrieving customer's price list...</p>
            </div>
        </div>
    </div>
</div>

