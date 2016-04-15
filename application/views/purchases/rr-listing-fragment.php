<?php if (isset($entries) AND is_array($entries) AND ! empty($entries)): ?>
    <?php foreach ($entries as $e): ?>
        <tr data-pk="<?= $e['id'] ?>">
            <td>
                <?php if (is_admin()): ?>
                    <?php if ((int) $e['is_locked'] === 1): ?>
                        <a href="javascript:void(0)" data-request="do_unlock" class="request-lock-state btn btn-flat btn-success btn-xs"><i class="fa fa-lock"></i></a>
                    <?php else: ?>
                        <a href="javascript:void(0)" data-request="do_lock" class="request-lock-state btn btn-flat btn-warning btn-xs"><i class="fa fa-unlock"></i></a>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td><a href="<?= base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$e['id']}") ?>"><?= str_pad($e['id'], 4, "0", STR_PAD_LEFT) ?></a></td>
            <td><?= $e['pr_number']; ?></td>
            <td><a target="_blank" href="<?= base_url("purchases/orders/manage?do=update-purchase-order&id={$e['fk_purchase_order_id']}") ?>"><?= str_pad($e['fk_purchase_order_id'], 4, "0", STR_PAD_LEFT) ?></a></td>
            <td><?= $e['date']; ?></td><td><?= $e['supplier'] ?></td><td><?= $e['total_amount'] ?></td><td><span class="label <?= $label_class ?>"><?= $status ?></span></td>
            <td>
                <a href="<?= "{$url}/do_print?id={$e['id']}"?>"  title='Print' role='button' class="btn btn-primary print-doc btn-flat btn-xs">Print</a>
                <?php if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN): ?>
                    <a href="javascript:void(0)" class="remove-item btn btn-danger btn-flat btn-xs">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="9" class="text-center">No results found</td></tr>
<?php endif; ?>