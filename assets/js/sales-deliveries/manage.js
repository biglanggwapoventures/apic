$(document).ready(function () {
    var pk = $('[data-name=key]').data('value');
    var cmUrlSave = $('[data-name=cm-url-save]').data('value');
    $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'}); //initialize datepicker on every date fields
    $(".has-amount").priceFormat({prefix: ''}); //initialize price format on every date fields
    if (!$(".datepicker").val() || $(".datepicker").val() === '') {
        $(".datepicker").datepicker("setDate", new Date()); //field has default value, do not touch
    }
    $(".payment-datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    $(".check-datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    var bankAccounts = "<input name='check[bank_account][]' type='text' class='form-control'/>";
    var transactionsBody = $('tbody#pl-transactions'); //reference to the pl transaction table
    var checkBody = $('tbody#check-details'); //reference to the checks table
    function doCalculation() {
        var lineQuantity, lineDiscount, lineUnitPrice, totalAmount = numeral();
        $("tbody#pl-details tr").each(function () {
            var lineGross = numeral(), lineNet = numeral(), totalDiscount = numeral();
            var $this = $(this);
            lineQuantity = numeral($this.closest("tr").find(".this-delivery").val());
            lineDiscount = numeral().unformat($this.closest("tr").find(".discount").text());
            lineUnitPrice = numeral().unformat($this.closest("tr").find(".unit-price").text());
            lineGross = (lineGross.add(lineUnitPrice).multiply(lineQuantity));
            $this.closest("tr").find(".gross-amount").text(lineGross.format("0,00.00"));
            totalDiscount = lineQuantity.multiply(lineDiscount);
            lineNet = lineGross.subtract(totalDiscount);
            $this.closest("tr").find(".net-amount").text(lineNet.format("0,00.00"));
            totalAmount.add(lineNet);
            $(".total-amount").text(totalAmount.format("0,00.00"));
        });
    }
    $("select[name=customer-id]").change(function () {
        $this = $(this);
        $.getJSON($("input[name=data-list-so-url]").val(), {customer_id: $this.val()})
                .done(function (so) {
                    var options = [];
                    $("select[name=fk_sales_order_id]").html("<option value=''>Please select S.O. No.</option>");
                    $(so).each(function (i) {
                        options.push("<option data-agent=\""+so[i].sales_agent+"\" value='" + so[i].id + "'>SO NO. " + so[i].id + "</option>");
                    });
                    $("select[name=fk_sales_order_id]").append(options.join(""));
                })
                .fail(function (jqxhr, textStatus, error) {
                    alert("There is an error trying to retrieve customer's sales orders.");
                });
    });
    $("select[name=fk_sales_order_id]").change(function () {
        var id = $(this).val();
        if (!id || !id.length) {
            $("tbody#pl-details").html("<tr><td colspan='7' class='text-center'>Please select a customer and his/her corresponding S.O. No.</td></tr>");
            return;
        }
        $('#sales-agent').text($(this).find('option:selected').data('agent'));
        $.getJSON($("input[name=data-so-details-url]").val(), {order_id: id})
                .done(function (details) {
                    var tbodyContent = [];
                    $(details.items_ordered).each(function (i) {
                        var tableCell = [],
                            l = details.items_ordered[i];
                        tableCell[0] = l.product_description;
                        tableCell[1] = '<div class="t">'+l.unit_description+'</div>hds/pcs';
                        tableCell[2] = '<div class="t">'+l.product_quantity+'</div>'+l.total_units;
                        tableCell[3] = '<div class="t">'+l.quantity_delivered+'</div>'+l.units_delivered;
                        tableCell[4] = '<div class="t"><input type="number" step="0.01" min="0" class="form-control this-delivery" name="details[this_delivery][]" /></div><input type="number" min="0" step="0.01" class="form-control" name="details[delivered_units][]" />';
                        tableCell[5] = "<span class='unit-price'>" + numeral(details.items_ordered[i].unit_price).format("0,00.00") + "</span>";
                        tableCell[6] = "<span class='discount'>" + numeral(details.items_ordered[i].discount).format("0,00.00") + "</span>";
                        tableCell[7] = "<span class='net-unit-price'>" + numeral(parseFloat(details.items_ordered[i].unit_price) - parseFloat(details.items_ordered[i].discount)).format("0,00.00") + "</span>";
                        tableCell[8] = "<span class='gross-amount'>0.00</span>";
                        tableCell[9] = "<span class='net-amount'>0.00</span><input type='hidden' name='details[fk_sales_order_detail_id][]' value='" + details.items_ordered[i].id + "'/>";
                        tbodyContent.push("<tr><td>" + tableCell.join("</td><td>") + "</td></tr>");
                    });
                    $("tbody#pl-details").html("").append(tbodyContent.join(""));
                    $("tbody#pl-details").find(".has-amount").priceFormat({prefix: ''});
                })
                .fail(function (jqxhr, textStatus, error) {
                    console.log("There is an error trying to retrieve customer's order details.");
                    $("tbody#pl-details").html("<tr><td colspan='7'><p class='bg-red text-center'>An error occured while trying to retrieve data. Please try again.</p></td></tr>");
                });
    });
    $("tbody#pl-details").on('keyup change', '.this-delivery, .discount', function () {
        doCalculation();
    });
    $("form").submit(function (e) {
        e.preventDefault();
        var that = $(this);
        $(".callout").addClass("hidden");
        $("button[type=submit]").addClass("disabled");
        $.post(that.attr('action'), that.serialize())
        .done(function(response){
            data = $.parseJSON(response);
            var message = typeof data.message === 'string' ? data.message : data.message.join('</li><li>');
            if(data.error_flag) {
                $(".callout").removeClass("hidden");
                 $(".callout ul").html("").append("<li>" + message + "</li>");
                $("html, body").animate({scrollTop: 0}, "slow");
            } else {
                if (data.data.hasOwnProperty('redirect')) {
                    window.location.href = data.data.redirect;
                }else{
                    $.growl.notice({title:'Done!', message:data.message})
                }
            }
        })
        .fail(function(){
            $(".callout ul").html("")
            .append("<li>An internal server error has occured. Please try again later. If the error still persists, please contact system administrator</li>");
        })
        .always(function(){
            $("button[type=submit]").removeClass("disabled");
        })
    });
    //quick paymnets
    $('.payment.add-line').click(function () {
        transactionsBody.find('tr.empty-notif').remove();
        transactionsBody.append(newTransactionLine());
        var newLine = transactionsBody.find('tr:last');
        newLine.find('.payment-datepicker').datepicker({dateFormat: 'yy-mm-dd'});
        newLine.find('.has-amount').priceFormat({prefix: ''});
    });
    $(document).on('click', '.payment.remove-line', function () {
        $(this).closest('tr').next('._c').remove().end().closest('tr').remove();
        if (!transactionsBody.children().length) {
            transactionsBody.append("<tr class='empty-notif'><td colspan='6' class='text-center'>No on-delivery transactions made.</td></tr>");
        }
    });
    $(document).on('click', '.check.add-line', function () {
        var checkBody = $(this).closest('tfoot').siblings('#check-details');
        checkBody.append(newCheckLine(false));
        var appended = checkBody.find('tr:last');
        appended.find('.check-datepicker').datepicker({dateFormat: 'yy-mm-dd'});
        appended.find('.has-amount').priceFormat({prefix: ''});
    });
    $(document).on('change', 'select.payment-types', function () {
        if ($(this).val() === 'Check') {
            $(this).closest('tr').after('<tr class="_c"><td style="border:none;"></td><td colspan="5"><table paymentid="' + ($(this).closest('.paymentDetailEntry').attr("paymentid")) + '" class="table no-border"><thead><tr class="active"><th style="width:20%">Bank Account</th><th style="width:10%">Check Number</th><th style="width:15%">Check Date</th><th style="width:15%">Deposit Date</th><th style="width:14%">Amount </th><th style="width:5%"><a class="btn btn-info btn-flat payment add-check-line" role="button"><i class="fa fa-plus"></i></a></th></tr></thead></tfoot><tbody id="check-details"></tbody></table></td></tr>')
                    .next().find('tbody#check-details').html(newCheckLine(($(this).closest('.paymentDetailEntry').attr("paymentid")))).find('.check-datepicker').datepicker({dateFormat: 'yy-mm-dd'}).end().find('.has-amount').priceFormat({prefix: ''});
            $(this).closest("tr").find("td:nth-child(5)").find("input").attr("readonly", "readonly");
        } else {
            $(this).closest("tr").find("td:nth-child(5)").find("input").removeAttr("readonly");
            $(this).closest('tr').next('._c').remove();
        }
    });
    $(document).on('click', '.add-check-line', function () {
        $(this).parent().parent().parent().parent().find("tbody").append(newCheckLine($(this).parent().parent().parent().parent().attr("paymentid"))).find('.check-datepicker').datepicker({dateFormat: 'yy-mm-dd'}).end().find('.has-amount').priceFormat({prefix: ''});
        $(this).parent().parent().parent().parent().find("tbody tr:last-child").append('<td><a class="btn btn-danger btn-flat payment remove-line" role="button"><i class="fa fa-times"></i></a></td>');
    });
    $(document).on('keyup', "input.checkAmount", function () {
        var totalCheckAmount = 0;
        $(this).parent().parent().parent().children().each(function () {
            totalCheckAmount = totalCheckAmount * 1 + (($(this).find(".checkAmount").val()).replace(/,/g, "") * 1);
        });
        $(this).parent().parent().parent().parent().parent().parent().prev().find(".has-amount").val(numeral(totalCheckAmount).format("0,0.00"));
    });
    /* CREDIT MEMO FUNCTIONS */
    $('#print-credit-memo').printPage({
        message: "Your document is being created"
    });
});
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
