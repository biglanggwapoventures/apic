<style type="text/css">
    .list tr th:nth-child(4), .list tr td:nth-child(4){
        text-align: right;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title">Master List</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a data-toggle="modal" data-target="#add-entry-modal" href="#">Add new material/supply</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <table class="table table-hover  list">
                    <thead><tr><th style="width:10%">ID</th><th style="width:35%">Description</th><th style="width:35%">Unit</th><th>Action(s)</th></tr></thead>
                    <tbody>
                        <?php foreach($unit_listing as $item):?>
                            <?php $editable_src[] = ['text' => $item['description'], 'value' => $item['id']]?>
                        <?php endforeach;?>
                        <tr class="hidden" id="chart-tr-template">
                            <td></td>
                            <td><a class="description editable" data-type="text"></a></td>
                            <td><a class="editable unit"></a></td>
                            <td class="options"><a href="#" class="remove-item btn btn-xs btn-flat btn-danger">Delete</a></td>
                        </tr>
                        <?php if (empty($listing)): ?>
                            <tr class="empty-notif">
                                <td colspan="4" class="text-center">No items recorded.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($listing as $item): ?>
                            <tr data-pk="<?= $item['id'] ?>">
                                <td><?= $item['id'] ?></td>
                                <td><a class="description editable" data-type="text"><?= $item['description'] ?></a></td>
                                 <td><a data-value="<?= $item['fk_unit_id'] ?>" class="editable unit"><?= $item['unit_description'] ?></a></td>
                                <td><a href="#" class="remove-item btn btn-xs btn-flat btn-danger">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody></table>
            </div><!-- /.box-body -->  

        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="add-entry-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add new chart of account</h4>
            </div>
            <form role="form" id="new-entry">
                <div class="modal-body">
                    <div class="callout callout-danger hidden error-content"></div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text"  class="form-control" name="description" id="description" required="required"/>
                    </div>
                     <div class="form-group">
                        <label for="description">Unit</label>
                        <?= generate_dropdown('unit', dropdown_format($unit_listing, 'id', 'description'), FALSE, 'class="form-control"')?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
       var unitSrc = <?= json_encode($editable_src);?>;
        $.fn.editable.defaults.mode = 'inline';

        var descriptionField = $('#description');
        var _description;

        var handleError = function (errorArray, htmlBreak) {
            var notification = '';
            $.each(errorArray, function (key, value) {
                notification += value + (htmlBreak ? '<br/>' : '\n');
            });
            console.log(notification);
            return notification;
        }

        //event handler on creating new entry
        $('#new-entry').submit(function (event) {
            event.preventDefault();

            $('[type=submit]').text('Submitting...').addClass('disabled');
            $('input').attr('readonly', 'readonly');

            _description = descriptionField.val();

            var request = $.post("<?= base_url('maintainable/supplies/ajax_add') ?>", {
                'description': _description,
                unit: $('select[name=unit]').val()
            });

            request.done(function (response) {
                if (response.hasOwnProperty('error_flag') && response.error_flag === false) {
                    $('.empty-notif').remove();
                   
                    var template = $('#chart-tr-template').clone().removeClass('hidden').removeAttr('id').attr('data-pk', response.data.id);
                    template.find('td:first').text(response.data.id);
                    template.find('td a.description').text(_description);
                    template.find('td a.unit').text($('.modal select[name=unit] option:selected').text()).attr('data-value', $('.modal select[name=unit]').val());
                    template.appendTo(".list");
                    $('.modal').find('input,select').val('')
                    $('#add-entry-modal').modal('hide');
                    $('.error-content').addClass('hidden');
                } else {
                    $('.error-content').removeClass('hidden').html($('<p />', {html: handleError(response.data, true), class: 'text-danger'}));
                }
            });
            request.always(function () {
                $('[type=submit]').text('Submit').removeClass('disabled');
                $('input').removeAttr('readonly');
            });

        });

        //event handler on deleting entry
        $('.list').on('click', '.remove-item', function (event) {
            event.preventDefault();

            var $this = $(this);

            var _id = $this.closest('tr').data('pk');

            var confirmed = confirm('Do you really want to delete this item?');
            if (!confirmed) {
                return;
            }

            var request = $.post("<?= base_url('maintainable/supplies/ajax_delete') ?>", {
                'id': _id
            });

            request.done(function (response) {
                if (response.hasOwnProperty('error_flag') && response.error_flag === false) {
                    $this.closest('tr').remove();
                }
            })

            request.always(function () {
                if ($('.list tbody tr').length === 1) {
                    $('.list').append('<tr class="empty-notif"><td colspan="4" class="text-center">No items recorded.</td></tr>');
                }
            });
        });

        //update handler for description
        $('.list').editable({
            selector: 'tr td a.editable.description',
            type: 'text',
            name: 'description',
            url: "<?= base_url('maintainable/supplies/ajax_update') ?>",
            pk: function () {
                return $(this).closest('tr').data('pk');
            },
            success: function (response, newValue) {
                if (response.error_flag === true)
                    return handleError(response.data, false);
            }
        });
        
        //update handler for units
        $('.list').editable({
            selector: 'tr td a.editable.unit',
            name: 'unit',
            source: unitSrc,
            type: 'select',
            url: "<?= base_url('maintainable/supplies/ajax_update') ?>",
            pk: function () {
                return $(this).closest('tr').data('pk');
            },
            success: function (response, newValue) {
                if (response.error_flag === true)
                    return handleError(response.data, false);
            }
        });


    });
</script>