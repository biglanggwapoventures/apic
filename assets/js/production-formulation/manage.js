(function ($) {
    $(document).ready(function(){

        var index = $('[data-name=index]').data('value'),
            getNetWeight = function(){
                var netWeight = 0;
                $.each($('.quantity'), function(i, v){
                    console.log
                    netWeight += parseFloat($(v).val() || 0);
                })
                return netWeight.toFixed(3);
            },
            displayNetWeight = function(){
                $('#net-weight').text(function(){
                    return numeral(getNetWeight()).format('0,0.000');
                });
            },
            getNetCost = function(){
                var netCost = 0;
                $.each($('.net-cost'), function(i, v){
                    console.log
                    netCost += parseFloat(numeral().unformat($(v).text() || 0));
                })
                return netCost.toFixed(2);
            },
            displayNetCost = function(){
                $('#net-cost').text(function(){
                    return numeral(getNetCost()).format('0,0.00');
                });
            },
            getCostPerKilogram = function(){
                return (getNetCost() / getNetWeight()).toFixed(2);
            },
            displayCostPerKilogram = function(){
                $('#kgs-cost').text(function(){
                    return numeral(getCostPerKilogram()).format('0,0.00');
                });
            };

        $('tbody').on('click', '.remove-line', function(){
            var tr = $('tbody tr');
            if(tr.length === 1){
                tr.addClass('hidden').find('select,input').val('').attr('disabled', 'disabled');
                tr.find('.reset').text('');
            }else{
                $(this).closest('tr').remove();
            }
            displayNetWeight();
            displayNetCost();
            displayCostPerKilogram();
        });

        $('.add-line').click(function(){
            var tr = $('tbody tr');
            if(tr.length === 1 && tr.hasClass('hidden')){
                tr.removeClass('hidden').find('select,input').removeAttr('disabled');
            }else{
                var template = $(tr[0]).clone();
                template.find('input[type=hidden]').remove();
                template.find('select,input').val('').attr('name', function(){
                    return $(this).data('name').replace('idx', index);
                });
                template.find('.reset').text('');
                template.appendTo('tbody');
                index++;
            }
        });

        $('tbody').on('change', '.mats', function(){
            // display unit
            var that = $(this),
                unit = that.find('option:selected').data('unit');
            that.closest('tr').find('.unit-description').text(unit);
            // display unit cost and net cost
            if(isAdmin){
                var productId = that.val(),
                    url = $('[data-name=get-cost-url]').data('value')+productId;

                $.getJSON(url)
                .done(function(response){
                    if(response.result){
                        var cost = response.data.cost, // get cost from response
                            tr = that.closest('tr');

                        tr.find('.unit-cost').text(function(){
                            return numeral(cost).format('0,0.00');
                        });

                        tr.find('.net-cost').text(function(){
                            var quantity = parseFloat(tr.find('.quantity').val()) || 0,
                                netCost = quantity * parseFloat(cost);
                            return numeral(netCost).format('0,0.00');
                        });
                    }
                })
                .fail(function(){
                    alert('Internal server error has occured. Please try again later.');
                });
            }
            that.closest('tr').find('.link').attr('href', function(){
                return $(this).data('href').replace('pid', that.val())
            })
            displayNetWeight();
            displayNetCost();
            displayCostPerKilogram();
        });

        $('tbody').on('input', '.quantity', function(){
            var that = $(this),
                cost = parseFloat(numeral().unformat(that.closest('tr').find('.unit-cost').text() || 0)),
                netCost = cost * parseFloat(that.val() || 0);
            that.closest('tr').find('.net-cost').text(function(){
                return numeral(netCost).format('0,0.00');
            });
            displayNetWeight();
            displayNetCost();
            displayCostPerKilogram();
        });

        $('form').submit(function(e){
            e.preventDefault();
            $('.callout-danger').addClass('hidden');
            var that = $(this);
            $.post(that.data('action'), that.serialize())
            .done(function(response){
                if(response.result){
                    window.location.href = $('#btn-cancel').attr('href');
                    return;
                }
                $('.callout-danger').removeClass('hidden').find('ul').html('<li>'+response.messages.join('</li><li>')+'</li>');
                $('html, body').animate({scrollTop: 0}, 'slow');
            })
            fail(function(){
                alert('An internal server error has occured. Please try again in a few moments.');
            });
        });

    });
})(jQuery);