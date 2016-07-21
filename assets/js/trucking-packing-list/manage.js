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

    // $("table").on('change', 'select.tariff_details_list', function () {
    //     var $this = $(this);
    //     $this.closest("tr").find(".rate").text($this.find("option:selected").data('rate'));
    //     $this.closest("tr").find(".rateH").val($this.find("option:selected").data('rate'));
    //     doCalculation();
    // });

    function doCalculation() {
        $("tbody tr").each(function () {
            $this = $(this);
            var lineUnitPrice = $this.closest("tr").find(".rate").text();
            var pcs = $this.closest("tr").find(".pcs").val();
            $this.closest("tr").find(".amountH").val(numeral(parseFloat(lineUnitPrice) * parseFloat(pcs)).format('0,0.00'));
            $this.closest("tr").find(".amount").text(numeral(parseFloat(lineUnitPrice) * parseFloat(pcs)).format('0,0.00'));
        });
    }

    $("table").on('change', 'select.tariff_details_list', function () {
        var $this = $(this);
        // $this.closest("tr").find(".locationH").val($this.find("option:selected").data('rate'));
        $this.closest("tr").find(".rateH").val($this.find("option:selected").data('rate'));
        $this.closest("tr").find(".rate").text($this.find("option:selected").data('rate'));
        doCalculation();
    });
    $("table").on('blur', '.for-calculation', function () {
        $("tbody tr").each(function(){
            $this = $(this);
            $this.closest("tr").find('.pformat').priceFormat({prefix: ''});
        });
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
        newLine();
    });

    function newLine(){
        var template = $("tr#template").clone().removeAttr("id");
        var tariffId = $('#tariff').val();
        $.get($("input[name=data-tariff-detail-url]").val(), {id: tariffId}).done(function (response) {
            template.find('select').remove();
            template.find('.locationH').remove()
            template.find('td:first span').remove();
            template.find('td:first').prepend(response.details);
            template.find(".detail-id").remove();
            template.find(".rateH").val(0);
            template.find(".amountH").val(0);
            template.find(".amount").text('');
            template.find('.pcs').val('');
            template.find('.text-clear').text('0.00');
            template.find('.pformat').priceFormat({prefix: ''});
            template.find(".rate").text("");
            $("tbody").append(template);
        }).fail(function (jqxhr, textStatus, error) {
            alert('fail');
        });

        $("tbody tr").each(function(){
            $this = $(this);
            $this.closest("tr").find('.pformat').priceFormat({prefix: ''});
        });
    }

    function resetLine(line) {
        line.find(".detail-id").remove();
        line.find('.select-clear,.input-clear').val('');
        line.find(".amountH").val(0);
        line.find(".amount").text('');
        line.find('.pformat').priceFormat({prefix: ''});
        line.find('.text-clear').text('0.00');
        line.find(".rate").text("");
        line.find(".rateH").val(0);
    }

    $(document).load(function () {
            doCalculation();
    });

	(function(){
        doCalculation();
	})();
	})
})(jQuery)

