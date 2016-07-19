(function($){
	$(document).ready(function(){	
	    $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
	    if (!$(".datepicker").val() || $(".datepicker").val() === '') {
	        $(".datepicker").datepicker("setDate", new Date());
	    }

		$('.pformat').priceFormat({prefix:''})

		    var messageBox = $('.callout.callout-danger');

        $('form').submit(function(e){

            e.preventDefault();

            var that = $(this);

            messageBox.addClass('hidden');

            $('[type=submit]').attr('disabled', 'disabled');

            $.post(that.data('action'), that.serialize())

            .done(function(response){
            	console.log(response)
                if(response.error_flag){
                    messageBox.removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
                    $('html, body').animate({scrollTop: 0}, 'slow');
                    return;
                }
                window.location.href = $('#cancel').attr('href');
            })
            .fail(function(){
                alert('An internal error has occured. Please try again in a few moment.');
            })
            .always(function(){
                $('[type=submit]').removeAttr('disabled');
            });
        });

    $("#customer-name").change(function () {
        var $this = $(this);
        if ($this.val()) {
            getTripTicket($this.val());
        } else {
        }
    });

    $("#tariff").change(function () {
        var $this = $(this);
        if ($this.val()) {
            getTariffDetails($this.val());
        } else {
        }
    });

    function getTariffDetails(tariffId) {
        $.get($("input[name=data-tariff-detail-url]").val(), {id: tariffId}).done(function (response) {
        	console.log(response);
        	if(response.options==1) $('#option').text('Origin'); else $('#option').text('Destination')
        	$('#location').text(response.location);
			$('tr#template td:first').find('select').remove();
            $('tr#template td:first').append(response.details);
        }).fail(function (jqxhr, textStatus, error) {
        	alert('fail');
        });
    }

    function getTripTicket(customerId) {
        $.get($("input[name=data-trip-ticket-url]").val(), {id: customerId}).done(function (response) {
            $('#trip-ticket').find('select').remove();
            $('#trip-ticket').append(response);
        }).fail(function (jqxhr, textStatus, error) {
     
        });
    }

    $("table").on('change', 'select.tariff_details_list', function () {
        var $this = $(this);
        $this.closest("tr").find(".rate").text($this.find("option:selected").data('rate'));
        // doCalculation();
    });

    function doCalculation() {
        var lineQuantity, lineDiscount, lineUnitPrice, totalAmount = numeral();
        $("tbody tr").each(function () {
            var lineGross = numeral(), lineNet = numeral(), lineTotalDiscount = numeral();
            $this = $(this);

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

    $("table").on('blur', '.for-calculation', function () {
    	$("tbody tr").each(function () {
    		$this = $(this);
	    	var lineUnitPrice = $this.closest("tr").find(".rate").text();
	    	var pcs = $this.closest("tr").find(".pcs").val();
	    	$this.closest("tr").find(".amount").val(numeral(parseFloat(lineUnitPrice) * parseFloat(pcs)).format('0,0.00'));

        });
    	// $this.closest("tr").find(".rate").text(lineGross.format("0,00.00"));
     //    $this.closest("tr").find(".pcs").text(numeral(parseFloat(lineUnitPrice) - parseFloat(lineDiscount)).format('0,0.00'));
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
        line.find('.amount').val('0.00');
        line.find(".rate").text("");
    }
    $("table").on('click', '.remove-line', function () {
        var tbody = $(this).closest('tbody');
        var that = $(this).closest('tr');
        if (tbody.find('tr').length === 1) {
            resetLine(that);
        } else {
            $(this).closest("tr").remove();
        }

        // doCalculation();
    });


	(function(){
		var tr = $('#less tbody tr');
		// if(tr.length === 1 && !tr.find('.item-less').val()){
		// 	tr.find('.remove-line').trigger('click');
		// 	alert("asd")
		// }else{
		// 	tr.find('.item-less').trigger('change');
		// 	alert("sdsd")
		// }
	})();
	})
})(jQuery)

// $(document).ready(function () {
//     var priceList = {};
//     if (!$("select[name=fk_sales_customer_id]").length) {
//         $("#no-customer-selected-overlay").addClass("hidden");
//         $("#sales-order-details").removeClass("hidden");
//     }
//     $(".has-amount").priceFormat({prefix: ''});
//     function getPriceList(customerId) {
//         $.get($("input[name=data-price-list-url]").val(), {id: customerId}).done(function (response) {
//             $('tr#template td:first').find('select').remove();
//             $('tr#template td:first').append(response);
//             $('.bs-loading-modal').modal('hide');
//         }).fail(function (jqxhr, textStatus, error) {
//             $('.bs-loading-modal .modal-header button.close').removeClass('hidden');
//             $('.bs-loading-modal .modal-body p').text('An error has occured while trying to retrieve customer\'s price list.');
//         });
//     }
//     function doCalculation() {
//         var lineQuantity, lineDiscount, lineUnitPrice, totalAmount = numeral();
//         $("tbody tr").each(function () {
//             var lineGross = numeral(), lineNet = numeral(), lineTotalDiscount = numeral();
//             $this = $(this);
//             lineQuantity = numeral($this.closest("tr").find(".product-quantity").val()); //get quantity
//             lineDiscount = numeral().unformat($this.closest("tr").find(".discount").val()); //get discount
//             lineUnitPrice = numeral().unformat($this.closest("tr").find(".unit-price").val()); //get unit price
//             lineGross = (lineGross.add(lineUnitPrice).multiply(lineQuantity));
//             $this.closest("tr").find(".gross-amount").text(lineGross.format("0,00.00"));
//             $this.closest("tr").find(".net-unit-price").text(numeral(parseFloat(lineUnitPrice) - parseFloat(lineDiscount)).format('0,0.00'));
//             lineNet = lineGross.subtract(lineQuantity.multiply(lineDiscount)); //total discount = quantity * discount);
//             $this.closest("tr").find(".net-amount").text(lineNet.format("0,00.00"));
//             totalAmount.add(lineNet);

//         });
//         $(".total-amount").html('<strong>' + totalAmount.format("0,00.00") + '</strong>');
//     }

//     $("select[name=fk_sales_customer_id]").change(function () {
//         var $this = $(this);
//         if ($this.val()) {
//             $('.bs-loading-modal').modal({keyboard: false, backdrop: 'static'}).modal('show');
//             $("#no-customer-selected-overlay").addClass("hidden");
//             $("#sales-order-details").removeClass("hidden");
//             getPriceList($this.val());
//         } else {
//             $("#no-customer-selected-overlay").removeClass("hidden");
//             $("#sales-order-details").addClass("hidden");
//         }
//     });
//     $("table").on('change', 'select.product-list', function () {
//         var $this = $(this);
//         $this.closest("tr").find(".unit").text($this.find("option:selected").data('unit'));
//         $this.closest("tr").find(".discount").val($this.find("option:selected").data('discount'));
//         $this.closest("tr").find(".unit-price").val($this.find("option:selected").data('price'));
//         doCalculation();
//     });
//     $("table").on('blur', '.for-calculation', function () {
//         doCalculation();
//     });
//     $("table").on('change', '.for-calculation', function () {
//         doCalculation();
//     });
//     $("table").on('click', '.remove-line', function () {
//         var tbody = $(this).closest('tbody');
//         var that = $(this).closest('tr');
//         if (tbody.find('tr').length === 1) {
//             resetLine(that);
//         } else {
//             $(this).closest("tr").remove();
//         }

//         doCalculation();
//     });
//     $(".add-line").click(function () {
//         var template = $("tr#template").clone().removeAttr("id");
//         resetLine(template);
//         $("tbody").append(template);
//         $("tbody").find("tr:last").find(".has-amount").priceFormat({prefix: ''});
//     });

//     function resetLine(line) {
//         line.find(".detail-id").remove();
//         line.find('.select-clear,.input-clear').val('');
//         line.find('.text-clear').text('0.00');
//         line.find(".unit").text("");
//     }

//     var messageBox = $('.callout.callout-danger');

//         $('form').submit(function(e){

//             e.preventDefault();

//             var that = $(this);

//             messageBox.addClass('hidden');

//             $('[type=submit]').attr('disabled', 'disabled');

//             $.post(that.data('action'), that.serialize())

//             .done(function(response){
//                 if(response.error_flag){
//                     messageBox.removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
//                     $('html, body').animate({scrollTop: 0}, 'slow');
//                     return;
//                 }
//                 window.location.href = $('#cancel').attr('href');
//             })
//             .fail(function(){
//                 alert('An internal error has occured. Please try again in a few moment.');
//             })
//             .always(function(){
//                 $('[type=submit]').removeAttr('disabled');
//             });
//         });

// });