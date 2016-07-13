<?php $url = base_url('tracking/tariffs'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $title ?></h3>
            </div><!-- /.box-header -->
            
            <form data-action="<?= $action ?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden">
                      <ul class="list-unstyled">
                        
                      </ul>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Code: </label>
                            <input name="agent_code" style="text-align: left" type="text" class="form-control" value="<?= put_value($data, 'tariff_code', '')?>">
                        </div>
                    </div>
                    <div class="row">
                         <div class="form-group col-md-4">
                            <label>Option</label>
                            <?= option_dropdown('option', put_value($data, 'option', ''), 'class="form-control"')?>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Location</label>
                            <?= option_dropdown('option', put_value($data, 'option', ''), 'class="form-control"')?>
                        </div>
                    </div>

                </div><!-- /.box-body -->  

                <fieldset style="margin-top:15px;">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table location-details">
                            <thead>
                                <tr class="active">
                                    <th>Location</th><th>Rate</th><th>Kms</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td>
                                                <input type="hidden" value="" name="" data-name="" />
                                             <?= option_dropdown('option', put_value($data, 'option', ''), 'class="form-control"')?>
                                        </td>
                                        <td>   
                                            <input name="rate" style="text-align: left" type="text" class="form-control" value="<?= put_value($data, 'Rate', '')?>">
                                        </td>
                                        <td class="text-right line-average">
                                            <input name="kms" style="text-align: left" type="text" class="form-control" value="<?= put_value($data, 'Kms', '')?>">
                                        </td>
                                        <td>
                                            <a class="btn btn-danger btn-flat btn-sm remove-line" role="button"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <a class="btn btn-default btn-flat btn-sm" id="add-line" role="button"><i class="fa fa-plus"></i> Add new line</a>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>   
                </fieldset>

                <div class="box-body">
                    <button class="btn btn-flat btn-success <?= can_update($data) ? '' : 'disabled'?>">Submit</button>
                    <a class="btn btn-flat btn-warning" id="cancel" href="<?= $url?>">Cancel</a>
                </div><!-- /.box-footer -->  
            </form>
        </div>
    </div>
</div>
<script>

$(document).ready(function(){
    var index = $('.location-details tbody tr').length;
    $('#add-line').click(function(){
            var tr = $('.location-details tbody tr');
            if(tr.hasClass('hidden')){
                tr.find('input').removeAttr('disabled');
                tr.removeClass('hidden');
            }else{
                var clone = $(tr[0]).clone();
                // clone.find('input').val('').attr('name', function(){
                //     return $(this).data('name').replace('idx', index);
                // });
                clone.find('[type=hidden]').remove();
                clone.appendTo('.location-details tbody');
                index++;
            }
            doSequencing();
    });

    $('.location-details').on('click', '.remove-line', function(){
            if($('.location-details tbody tr').length > 1){
                $(this).closest('tr').remove();
            }else{
                $(this).closest('tr').addClass('hidden')
                    .find('input').val('').attr('disabled', 'disabled')
                    .end()
                    .find('[type=hidden]').remove();
            }
        });
});
</script>