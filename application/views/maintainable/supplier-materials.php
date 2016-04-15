<style type="text/css">
    .list tr td:nth-child(2){
        width: 5%;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title">Assign materials to: <?= $supplier['name'] ?></h3>
                <!-- tools box -->
            </div><!-- /.box-header -->
            <form method="POST" action="<?= base_url('maintainable/suppliers/save_assigned_materials/' . $supplier['id']) ?>">
                <div class="box-body">
                    <?php $save = $this->session->flashdata('save_success');?>
                    <?php if (is_array($save) && $save['result'] === TRUE): ?>
                        <div class="callout callout-info">
                            <h4>Success!</h4>
                            <p>Succesfully updated assigned materials.</p>
                        </div>
                    <?php elseif (is_array($save) && $save['result'] === FALSE): ?>
                        <div class="callout callout-danger">
                            <h4>Error!</h4>
                            <p>An error has occured while saving. Please check your inputs and try again.</p>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">

                            <table class="table list">
                                <tbody>
                                    <?php if (empty($assigned)): ?>
                                        <tr>
                                            <td>
                                                <?= arr_group_dropdown('fk_inventory_product_id[]', $options, 'id', 'description', FALSE, 'category_description', 'class="form-control"')?>
                                                <?//= generate_dropdown('fk_inventory_product_id[]', $options, FALSE, ''); ?>
                                            </td>
                                            <td><a class="btn btn-danger btn-flat btn-sm remove-line"><i class="fa fa-times"></i></a></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php foreach ($assigned as $item): ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="id[]" value="<?= $item['id'] ?>"/>
                                                <?= arr_group_dropdown('fk_inventory_product_id[]', $options, 'id', 'description', $item['fk_inventory_product_id'], 'category_description', 'class="form-control"')?>
                                                <?//= generate_dropdown('fk_inventory_product_id[]', $options, $item['fk_inventory_product_id'], 'class="form-control"'); ?>
                                            </td>
                                            <td><a class="btn btn-danger btn-flat btn-sm remove-line"><i class="fa fa-times"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot> 
                                    <tr><td colspan="2"><a class="btn btn-flat btn-default btn-sm add-line"><i class="fa fa-plus"></i> Add new line</a></td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!-- /.box-body -->  
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-check"></i> Save</button>
                    <a href="<?= base_url('maintainable/suppliers') ?>" class="btn btn-warning btn-flat pull-right">Go back to suppliers list</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('.add-line').click(function () {
        var clone = $('.list tr:first').clone();
        clone.find('select').val('');
        clone.appendTo('.list tbody');
        clone.find('[type=hidden]').remove();
    });
    $('.list').on('click', '.remove-line', function () {
        var row = $(this).closest('tr');
        if ($('.list tbody tr').index(row) === 0 && $('.list tbody tr').length === 1) {
            row.find('select').val('');
            row.find('[type=hidden]').remove();
        } else {
            row.remove();
        }
    });
</script>