$(document).ready(function () {
    var priceList = {};
    $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    if (!$(".datepicker").val() || $(".datepicker").val() === '') {
        $(".datepicker").datepicker("setDate", new Date());
    }
    if (!$("select[name=fk_sales_customer_id]").length) {
        $("#no-customer-selected-overlay").addClass("hidden");
        $("#sales-order-details").removeClass("hidden");
    }
    $(".has-amount").priceFormat({prefix: ''});
    function getPriceList(customerId) {
        $.get($("input[name=data-price-list-url]").val(), {id: customerId}).done(function (response) {
            $('tr#template td:first').find('select').remove();
            $('tr#template td:first').append(response);
            $('.bs-loading-modal').modal('hide');
        }).fail(function (jqxhr, textStatus, error) {
            $('.bs-loading-modal .modal-header button.close').removeClass('hidden');
            $('.bs-loading-modal .modal-body p').text('An error has occured while trying to retrieve customer\'s price list.');
        });
    }
    function doCalculation() {
        var lineQuantity, lineDiscount, lineUnitPrice, totalAmount = numeral();
        $("tbody tr").each(function () {
            var lineGross = numeral(), lineNet = numeral(), lineTotalDiscount = numeral();
            $this = $(this);
            lineQuantity = numeral($this.closest("tr").find(".product-quantity").val()); //get quantity
            lineDiscount = numeral().unformat($this.closest("tr").find(".discount").val()); //get discount
            lineUnitPrice = numeral().unformat($this.closest("tr").find(".unit-price").val()); //get unit price
            lineGross = (lineGross.add(lineUnitPrice).multiply(lineQuantity));
            $this.closest("tr").find(".gross-amount").text(lineGross.format("0,00.00"));
            $this.closest("tr").find(".net-unit-price").text(numeral(parseFloat(lineUnitPrice) - parseFloat(lineDiscount)).format('0,0.00'));
            lineNet = lineGross.subtract(lineQuantity.multiply(lineDiscount)); //total discount = quantity * discount);
            $this.closest("tr").find(".net-amount").text(lineNet.format("0,00.00"));
            totalAmount.add(lineNet);

        });
        $(".total-amount").html('<strong>' + totalAmount.format("0,00.00") + '</strong>');
    }

    $("select[name=fk_sales_customer_id]").change(function () {
        var $this = $(this);
        if ($this.val()) {
            $('.bs-loading-modal').modal({keyboard: false, backdrop: 'static'}).modal('show');
            $("#no-customer-selected-overlay").addClass("hidden");
            $("#sales-order-details").removeClass("hidden");
            getPriceList($this.val());
        } else {
            $("#no-customer-selected-overlay").removeClass("hidden");
            $("#sales-order-details").addClass("hidden");
        }
    });
    $("table").on('change', 'select.product-list', function () {
        var $this = $(this);
        $this.closest("tr").find(".unit").text($this.find("option:selected").data('unit'));
        $this.closest("tr").find(".discount").val($this.find("option:selected").data('discount'));
        $this.closest("tr").find(".unit-price").val($this.find("option:selected").data('price'));
        doCalculation();
    });
    $("table").on('blur', '.for-calculation', function () {
        doCalculation();
    });
    $("table").on('change', '.for-calculation', function () {
        doCalculation();
    });
    $("table").on('click', '.remove-line', function () {
        var tbody = $(this).closest('tbody');
        var that = $(this).closest('tr');
        if (tbody.find('tr').length === 1) {
            resetLine(that);
        } else {
            $(this).closest("tr").remove();
        }

        doCalculation();
    });
    $(".add-line").click(function () {
        var template = $("tr#template").clone().removeAttr("id");
        resetLine(template);
        $("tbody").append(template);
        $("tbody").find("tr:last").find(".has-amount").priceFormat({prefix: ''});
    });

    function resetLine(line) {
        line.find(".detail-id").remove();
        line.find('.select-clear,.input-clear').val('');
        line.find('.text-clear').text('0.00');
        line.find(".unit").text("");
    }
});