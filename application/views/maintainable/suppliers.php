<style type="text/css">
    .list tr th:nth-child(7), .list tr td:nth-child(7){
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
                            <li><a data-toggle="modal" data-target="#add-entry-modal" href="#">Add new supplier</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding">
                <table class="table table-hover  list">
                    <?php $url = base_url('maintainable/suppliers'); ?>
                    <thead><tr><th>ID</th><th>Name</th><th>Address</th><th>Contact number</th><th>Contact person</th><th>TIN Number</th><th></th></tr></thead>
                    <tbody>
                        <tr class="hidden" id="entry-template">
                            <td></td>
                            <td><a class="editable name" data-name="name"></a></td>
                            <td><a class="address editable" data-name="address"></a></td>
                            <td><a class="contact-num editable" data-name="contact_number"></a></td>
                            <td><a class="contact-person editable" data-name="contact_person"></a></td>
                            <td><a class="tin editable" data-name="tin_number"></a></td>
                            <td class="options">
                                <a href="#" class="link btn btn-xs btn-flat btn-info">Assign materials</a>
                                <a href="#" class="btn btn-xs btn-flat btn-danger remove-item">Delete</a>
                            </td>
                        </tr>
                        <?php if (empty($listing)): ?>
                            <tr class="empty-notif">
                                <td colspan="7" class="text-center">No items recorded.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php foreach ($listing as $item): ?>
                            <tr data-pk="<?= $item['id'] ?>">
                                <td><?= $item['id'] ?></td>
                                <td><a class="editable name" data-name="name"><?= $item['name'] ?></a></td>
                                <td><a class="address editable" data-name="address"><?= $item['address'] ?></a></td>
                                <td><a class="contact-num editable" data-name="contact_number"><?= $item['contact_number'] ?></a></td>
                                <td><a class="contact-person editable" data-name="contact_person"><?= $item['contact_person'] ?></a></td>
                                <td><a class="tin editable" data-name="tin_number"><?= $item['tin_number'] ?></a></td>
                                <td class="options">
                                    <a href="<?=$url.'/assign_materials/'.$item['id']?>" class="btn btn-xs btn-flat btn-info">Assign materials</a>
                                    <a href="#" class="btn btn-xs btn-flat btn-danger remove-item">Delete</a>
                                </td>
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
                        <label for="name">Name</label>
                        <input required="required" type="text" name="name" class="form-control" id="name" placeholder="Supplier Name">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input required="required" type="text" name="address" class="form-control" id="driver" placeholder="Address">
                    </div>
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input required="required" type="text" name="contact_number" class="form-control" id="contact-number" placeholder="Contact Number">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact-person">Contact Person (optional)</label>
                                <input type="text" name="contact_person" class="form-control" id="contact-person" placeholder="Contact Person">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tin-no">TIN No. (optional)</label>
                                <input type="text" name="tin_number" class="form-control" id="tin-no" placeholder="TIN No.">
                            </div>
                        </div>

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
        
        var url = '<?=$url?>/assign_materials/';
        
        //update handler
        var initializeEditable = function (element) {
            element.editable({
                type: 'text',
                url: "<?= base_url('maintainable/suppliers/ajax_update') ?>",
                pk: function () {
                    return $(this).closest('tr').data('pk');
                },
                name: function () {
                    return $(this).data('name');
                },
                success: function (response, newValue) {
                    if (response.error_flag === true)
                        return handleError(response.data, false);
                }
            });
        }
        $.fn.editable.defaults.mode = 'inline';
        initializeEditable($('.list a.editable'));

        var handleError = function (errorArray, htmlBreak) {
            var notification = '';
            $.each(errorArray, function (key, value) {
                notification += value + (htmlBreak ? '<br/>' : '\n');
            });
            return notification;
        }

        //event handler on creating new entry
        $('#new-entry').submit(function (event) {
            event.preventDefault();

            var $this = $(this);

            $('[type=submit]').text('Submitting...').addClass('disabled');
            $('input').attr('readonly', 'readonly');

            var request = $.post("<?= base_url('maintainable/suppliers/ajax_add') ?>", $this.serialize());

            request.done(function (response) {
                if (response.hasOwnProperty('error_flag') && response.error_flag === false) {
                    $('.empty-notif').remove();
                    var template = $('#entry-template').clone().removeClass('hidden').removeAttr('id').attr('data-pk', response.data.id);
                    template.find('td:first').text(response.data.id);
                    template.find('td a.link').attr('href', url+response.data.id);
                    template.find('td a.name').text($('#add-entry-modal [name=name]').val());
                    template.find('td a.tin').text($('#add-entry-modal [name=tin_number]').val());
                    template.find('td a.address').text($('#add-entry-modal [name=address]').val());
                    template.find('td a.contact-num').text($('#add-entry-modal [name=contact_number]').val());
                    template.find('td a.contact-person').text($('#add-entry-modal [name=contact_person]').val());
                    initializeEditable(template.find('a.editable'));
                    template.appendTo(".list");
                    $('input').val('');
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

            var request = $.post("<?= base_url('maintainable/suppliers/ajax_delete') ?>", {
                'id': _id
            });

            request.done(function (response) {
                if (response.hasOwnProperty('error_flag') && response.error_flag === false) {
                    $this.closest('tr').remove();
                }
            })

            request.always(function () {
                if ($('.list tbody tr').length === 1) {
                    $('.list').append('<tr class="empty-notif"><td colspan="7" class="text-center">No items recorded.</td></tr>');
                }
            });
        });
    });
</script>