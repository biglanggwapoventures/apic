<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#FFFFFF!important">
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a data-toggle="modal" data-target="#add-medication-modal">Create new medication</a></li>
                            <li><a>Search</a></li>
                        </ul>
                    </div>                     
                </div>
                <h3 class="box-title">
                    Master List
                </h3>
            </div>

            <div class="box-body no-padding">
                <table class="table table-condensed table-hover promix master-list">
                    <thead><tr class="info"><th style="width:40%">Product Code</th><th style="width:40%">Description</th><th>&nbsp;</th></tr></thead>
                    <tbody>
                        <?php $option_template = is_adm() ? '<a class="remove-item btn-flat btn btn-xs btn-danger btn-info">Remove</a>' : '<a class="remove-item btn-flat btn btn-xs btn-danger btn-info disabled">Remove</a>'; ?>
                        <?php if (is_array($items)): ?>
                            <?php foreach ($items as $i): ?>
                                <tr data-pk="<?= $i['id'] ?>">
                                    <td><?= $i['product_code'] ?></td><td><?= $i['description'] ?></td><td><?= $option_template ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="add-medication-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="add" data-action="<?= base_url('inventory/medications/add') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add medication</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code"  name="product_code" required="required"/>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" name="description" id="description" required="required"/>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?=base_url('assets/js/jquery.tablesorter.min.js')?>"></script>
<script type="text/javascript">
    $('document').ready(function () {
        var optionTemplate = '<?= $option_template ?>';
        $('table.master-list').tablesorter({headers:{2:{sorter:false}}});
        $('form.add').submit(function (e) {
            var form = $(this);
            $.post(form.data('action'), form.serialize()).done(function (response) {
                if (response.result) {
                    $('table.master-list tbody').append('<tr><td>' + $('#product_code').val() + '</td><td>' + $('#description').val() + '</td><td>' + optionTemplate + '</td></tr>');
                }
                $('#add-medication-modal form input').val('');//clear fields
                $('#add-medication-modal').modal('hide');
            }).fail(function () {
                alert('fail');
            });
            e.preventDefault();
        });
        $('.master-list').on('click', '.remove-item', function(e){
            var confirmed = confirm('Are you sure you want to delete this item?');
            if(!confirmed){
                return;
            }
            var $this = $(this);
            $.post('<?=base_url('inventory/medications/remove')?>', {id:$this.closest('tr').data('pk')}).done(function(data){
                if(data.result){
                    $this.closest('tr').remove();
                }
            }).fail(function(){
                alert('Internal server error!');
            });
            e.preventDefault();
        });
    });
</script>