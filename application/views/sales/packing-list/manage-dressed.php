<div class="box box-solid">
    <div class="box-header with-border"><h3 class="box-title"><?= $section_title ?></h3></div>
    <div class="box-body">
        <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
        <form data-action="<?= $form_action ?>">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Customer</label>
                        <?php if(!isset($data['company_name'])):?>
                            <?= generate_customer_dropdown('fk_sales_customer_id', FALSE,  'class="form-control"')?>
                        <?php else:?>
                            <p class="form-control-static">
                                <?= "[{$data['customer_code']}] {$data['company_name']}"?>
                            </p>
                        <?php endif;?>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>SO#</label>
                        <?php if(!isset($data['fk_sales_order_id'])):?>
                            <select class="form-control" name="fk_sales_order_id"><option></option></select>
                        <?php else:?>
                            <p class="form-control-static">
                                <?= "SO# {$data['fk_sales_order_id']}"?>
                            </p>
                        <?php endif;?>
                    </div>
                </div>
                 <div class="col-sm-2">
                     <div class="form-group">
                        <label>Invoice#</label>
                        <input type="text" class="form-control" value="<?= put_value($data, 'invoice_number', '')?>" name="invoice_number"/>
                    </div>
                </div>
                <div class="col-sm-4">
                     <div class="form-group">
                        <label>Sales Agent</label>
                        <?= form_dropdown('fk_sales_agent_id', $agents, put_value($data, 'fk_sales_agent_id', FALSE), 'class="form-control"');?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Trucking</label>
                        <?= form_dropdown('fk_sales_trucking_id', $truckings, put_value($data, 'fk_sales_trucking_id', FALSE), 'class="form-control"');?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Trucking Assistant</label>
                        <?= form_dropdown('fk_trucking_assistant_id', $trucking_assistants, put_value($data, 'fk_trucking_assistant_id', FALSE), 'class="form-control"');?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Departure date &amp; time</label>
                        <?php $date = isset($data['date']) ? date_create($data['date'])->format('m/d/Y H:i A'): ''?>
                        <input type="text" class="form-control datetimepicker" value="<?= $date?>" name="date"/>
                    </div>
                </div>
            </div>
             <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" name="remarks" rows="3"><?= put_value($data, 'remarks', '')?></textarea>
            </div>
            <hr/>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <?php $product_unit_price = 0;?>
                        <?php if(isset($sales_order)):?>
                            <?php $indexed_items = array_column($sales_order['items_ordered'], NULL, 'id')?>
                            <?php unset($sales_order);?>
                            <?php $selected = $data['details'][0]['fk_sales_order_detail_id']?>
                            <?php $product_unit_price = $indexed_items[$selected]['unit_price'];?>
                        <?php else:?>
                            <?php $indexed_items = [];?>
                            <?php $selected = FALSE; ?>
                        <?php endif;?>

                        <label>Product</label>
                        <?php $text = ['text' => 'product_description', 'attr' => ['name' => 'data-unit-price', 'value' => 'unit_price']]?>
                        <?= arr_group_dropdown('fk_sales_order_detail_id', $indexed_items, 'id', $text, $selected, FALSE, 'class="form-control"')?>
                    </div>
                </div>
            </div>

            <fieldset style="margin-top:15px;">
                <legend class="text-center mt-10">DELIVERY DETAILS</legend>
                <div class="row">
                    <div class="col-md-8">
                        <table class="table delivery-details">
                            <thead>
                                <tr class="active">
                                    <th>NO.</th><th>NO. PIECES</th><th>KILOGRAMS</th><th>AVERAGE</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_kgs = 0;?>
                                <?php $total_pieces = 0;?>
                                <?php $details = isset($data['details']) ? $data['details'] : [[]];?>
                                <?php foreach($details AS $i => $row):?>
                                    <tr>
                                        <td><?= $i + 1?></td>
                                        <td>
                                            <?php if(isset($row['id'])):?>
                                                <input type="hidden" value="<?= $row['id']?>" name="dd[<?= $i?>][id]" data-name="dd[idx][id]" />
                                            <?php endif;?>
                                            <?php $pieces = put_value($row, 'delivered_units', '');?>
                                            <input type="number"  value="<?= $pieces?>" step="0.01" name="dd[<?= $i?>][delivered_units]" data-name="dd[idx][delivered_units]" data-field="pieces" class="form-control get-summary get-average"/>
                                        </td>
                                        <td>   
                                            <?php $kgs = put_value($row, 'this_delivery', '');?>
                                            <input type="number" value="<?= $kgs?>" step="0.01" name="dd[<?= $i?>][this_delivery]" data-name="dd[idx][this_delivery]" data-field="kgs" class="form-control get-summary get-average"/>
                                        </td>
                                        <td class="text-right line-average">
                                            <?= $pieces > 0 ? number_format($kgs / $pieces, 2): ''?>
                                        </td>
                                        <td>
                                            <a class="btn btn-danger btn-flat btn-sm remove-line" role="button"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                    <?php $total_kgs += $kgs;?>
                                    <?php $total_pieces += $pieces;?>
                                <?php endforeach;?>
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
                    <div class="col-md-4">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>TOTAL NO. PIECES:</td>
                                    <td id="total-pieces" class="text-bold text-right">
                                        <?= number_format($total_pieces, 2)?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TOTAL NO. OF KILOGRAMS:</td>
                                    <td id="total-kgs" class="text-bold text-right">
                                        <?=  number_format($total_kgs, 2)?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>PRICE PER KILOGRAM:</td>
                                    <td id="price-kgs" class="text-bold text-right">
                                        <?= number_format($product_unit_price, 2)?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TOTAL PAYABLE:</td>
                                    <td id="payable" class="text-bold text-right">
                                        <?= number_format($product_unit_price * $total_kgs, 2)?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset>
            <hr>
            <button class="btn btn-flat btn-success">Submit</button>
            <a class="btn btn-default btn-flat pull-right" id="go-back" href="<?= base_url('sales/deliveries')?>" role="button">Go back</a>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){

        $('.datetimepicker').datetimepicker({format:'MM/DD/YYYY hh:mm A'});

        // initialiaze urls for ajax
        var SOUrl = '<?= base_url('sales/customers/a_get_undelivered_so')?>',
            SODetailsURL = '<?= base_url('sales/orders/a_fetch_details')?>'
            SOSelect = $('select[name=fk_sales_order_id]'),
            productSelect = $('select[name=fk_sales_order_detail_id]'),
            messageBox = $('.callout.callout-danger')
            index = $('.delivery-details tbody tr').length;


        $('select[name=fk_sales_customer_id]').change(function(){
            var that = $(this),
                customerId = that.val();

            productSelect.empty();

            if(!customerId){
                SOSelect.empty();
                return;
            }
            
            $.getJSON(SOUrl, {customer_id:customerId})
            .done(function(response){
                var options = ['<option></option>'];

                if(response.length){
                     $.each(response, function(i, v){
                        options.push('<option value="'+v.id+'">SO# '+v.id+'</option>');
                    });
                }
                
                SOSelect.html(options.join(''));
            })
            .fail(function(){
                alert('A server error has occured.');
            })
        });

        SOSelect.change(function(){

            var that = $(this),
                SONumber = that.val();


            if(!SONumber){
                productSelect.empty();
            }

            $.getJSON(SODetailsURL, {order_id:SONumber})
            .done(function(response){
                var options = ['<option></option>'];

                $.each(response.items_ordered, function(i, v){
                    options.push('<option value="'+v.id+'" data-unit-price="'+v.unit_price+'">'+v.product_description+'</option>');
                });
                
                productSelect.html(options.join(''));
            })
            .fail(function(){
                alert('A server error has occured.');
            });



        });

        productSelect.change(function(){
            var unitPrice = $(this).find('option:selected').data('unit-price');
            $('#price-kgs').text(numeral(unitPrice).format('0,0.00'));
            getTotalPayable();
        })

        $('#add-line').click(function(){
            var tr = $('.delivery-details tbody tr');
            if(tr.hasClass('hidden')){
                tr.find('input').removeAttr('disabled');
                tr.removeClass('hidden');
            }else{
                var clone = $(tr[0]).clone();
                clone.find('input').val('').attr('name', function(){
                    return $(this).data('name').replace('idx', index);
                });
                clone.find('[type=hidden]').remove();
                clone.appendTo('.delivery-details tbody');
                index++;
            }
            doSequencing();
        });

        $('.delivery-details').on('click', '.remove-line', function(){
            if($('.delivery-details tbody tr').length > 1){
                $(this).closest('tr').remove();
            }else{
                $(this).closest('tr').addClass('hidden')
                    .find('input').val('').attr('disabled', 'disabled')
                    .end()
                    .find('[type=hidden]').remove();
            }
            doSequencing();
            getSummary('pieces');
            getSummary('kgs');
        });

        $('form').submit(function(e){

            e.preventDefault();

            var that = $(this);

            messageBox.addClass('hidden');

            $('[type=submit]').attr('disabled', 'disabled');

            $.post(that.data('action'), that.serialize())

            .done(function(response){
                if(response.error_flag){
                    messageBox.removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
                    $('html, body').animate({scrollTop: 0}, 'slow');
                    return;
                }
                window.location.href = $('#go-back').attr('href');
            })
            .fail(function(){
                alert('An internal error has occured. Please try again in a few moment.');
            })
            .always(function(){
                $('[type=submit]').removeAttr('disabled');
            });
        });

        $('.delivery-details').on('blur', '.get-summary', function(){
            var field = $(this).data('field');
            getSummary(field);
        });

        $('.delivery-details').on('blur', '.get-average', function(){
            var val = parseFloat($(this).val()),
                field = $(this).data('field'),
                tr = $(this).closest('tr'),
                average = 0;
            if(field === 'kgs'){
                var pieces = (parseFloat(tr.find('[data-field=pieces]').val()) || 0);   
                average = val / pieces;
            }else{
                var kgs = (parseFloat(tr.find('[data-field=kgs]').val()) || 0);
                average = kgs / val;
            }
            tr.find('.line-average').text(numeral(average).format('0,0.00'));
        });

        function doSequencing(){
            $.each($('.delivery-details tbody tr'), function(i, v){
                $(v).find('td:first').text(i+1);
            })
        }

        function getSummary(field){
            var total = 0;
            $('.delivery-details tr [data-field='+field+']').each(function(){
                total += parseFloat($(this).val()) || 0;
            })
            $('#total-'+field).text(numeral(total).format('0,0.00'));
            getTotalPayable();
        }

        function getTotalPayable(){
            var totalKilograms = parseFloat(numeral().unformat($('#total-kgs').text() || 0)),
                unitPrice = parseFloat(numeral().unformat($('#price-kgs').text() || 0));
            $('#payable').text(numeral(totalKilograms*unitPrice).format('0,0.00'));
        }

    });
</script>