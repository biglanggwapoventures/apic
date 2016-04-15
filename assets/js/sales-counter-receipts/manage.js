$(document).ready(function() {
    styleCheckBoxes();
    function styleCheckBoxes() {
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass: 'iradio_minimal-red'
        });
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
    }
    function doCalculation() {
        var total = 0;
        $("tbody tr").each(function(i) {
            var $this = $(this);
            if ($this.find("input[type=checkbox]").is(":checked")) {
                total += numeral().unformat($this.find("span.has-amount").text());
            }
        });
        $(".total-amount").text(numeral(total).format("0,00.00"));
    }
    function generateLinktoPL(plId) {
        return $("input[name=data-link-to-pl]").val() + "/" + plId;
    }

    $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    if (!$(".datepicker").val() || $(".datepicker").val() === '') {
        $(".datepicker").datepicker("setDate", new Date());
    }
    $("select[name=fk_sales_customer_id]").change(function() {
        $val = $(this).val();
        if (!$val) {
            $("tbody").html('<tr><td colspan="4" class="text-center">Please select a customer</td></tr>');
            return;
        }
        $.getJSON($("input[name=data-list-pl-url]").val(), {customer_id: $val})
                .done(function(json) {
                    $("tbody").html("");
                    if (json.error_flag) {
                        $("tbody").html('<tr><td colspan="4" class="text-center">No available entries</td></tr>');
                        return;
                    }
                    var tableRow = [];
                    $(json.data).each(function(i) {
                        var tableCell = [];
                        tableCell[0] = '<div class="checkbox"><label><input name="details[fk_sales_delivery_id][]" type="checkbox" value="' + json.data[i].id + '"/> P.L. # ' + json.data[i].id + '</label>' +
                                '<a style="margin-left:10px;" target="_blank" href=' + generateLinktoPL(json.data[i].id) + '><i class="glyphicon glyphicon-link"></i></a></div>';
                        tableCell[1] = json.data[i].invoice_number;
                        tableCell[2] = json.data[i].date;
                        tableCell[3] = '<span class="has-amount">' + numeral(json.data[i].total_amount).format("0,00.00") + '</span>';
                        tableRow.push("<tr><td>" + tableCell.join("</td><td>") + "</td></tr>");
                    });
                    $("tbody").html(tableRow.join(""));
                    styleCheckBoxes();
                })
                .fail(function(jqxhr, textStatus, error) {
                    alert("There is an error trying to retrieve customer's sales orders.");
                });
    });
    $("tbody").on('change', 'input[type=checkbox]', function() {
        doCalculation();
    });
    $("form").submit(function() {
        $("button[type=submit]").addClass("disabled");
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.error_flag) {
                    $("button[type=submit]").removeClass("disabled");
                    $(".callout-danger").removeClass("hidden");
                    $(".callout-danger ul").html("<li>" + data.message.join("</li><li>") + "</li>");
                } else {
                    window.location.href = data.data.redirect;
                }
            },
            error: function(xhr, err) {
                $("button[type=submit]").removeClass("disabled");
                alert("Error has occured. Please try again later.");
            }
        });
        return false;
    });
});//iCheck for checkbox and radio inputs
