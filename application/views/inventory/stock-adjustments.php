<?php $url = base_url('inventory/stock_adjustments'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="<?= "{$url}/create"?>">New stock adjustment request</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                     
                </div>
                <h3 class="box-title">
                    Master List
                </h3>
            </div>

            <div class="box-body no-padding">
                
                <table id="adjustment-master-list" class="table table-striped table-condensed promix" data-master-list-url='<?= $url . '/ajax_master_list' ?>'  data-delete-url='<?= $url . '/ajax_delete/' ?>'>
                    <thead><tr class="info"><th>SA #</th><th>Date</th><th>Requested by</th><th>Status</th><th>&nbsp;</th></tr></thead>
                    <tbody>
                    <?php foreach($data as $row):?>
                        <tr data-pk="<?=$row['id']?>">
                            <td><a href="<?= "{$url}/update/{$row['id']}"?>"><?= str_pad($row['id'], 4, 0, STR_PAD_LEFT)?></a></td>
                            <td><?= date('m/d/Y', strtotime($row['date'])) ?></td>
                            <td><?= $row['username']?></td>
                            <td><?= $row['approved_by'] ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>'?></td>
                            <td>
                                <?php if($row['approved_by']):?>
                                    <a class="btn btn-flat btn-xs btn-primary print hidden" role="button">Print</a>
                                <?php else:?>
                                    <a class="btn btn-flat btn-xs btn-primary disabled hidden" role="button">Print</a>
                                <?php endif;?>
                                <?php if(is_admin()):?>
                                    <a class="btn btn-flat btn-xs btn-danger remove" role="button">Delete</a>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>