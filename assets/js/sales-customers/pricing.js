(function($){
    $(document).ready(function(){
        var index = $('[data-name=index]').data('value');
        /*=====================
         INITIALIZE PRICE FORMAT
         =====================*/
        $('.price').priceFormat({prefix:''});
        /*=====================
         INITIALIZE STICKY TABLE HEADER
         =====================*/
        $('table.promix').stickyTableHeaders({fixedOffset: $('.content-header')});

        $('#add-line').click(function(){
            var tr = $('table.promix > tbody > tr');
            if(tr.length === 1 && tr.hasClass('hidden')){
                tr.removeClass('hidden').find('input,select').removeAttr('disabled');
            }else{
                var clone = $(tr[0]).clone();
                clone.find('input[type=hidden]').remove();
                clone.find('input,select').val('').attr('name',  function(){
                    return $(this).data('name').replace('idx', index);
                });
                clone.find('.packaging').text('');
                clone.find('.price').priceFormat({prefix:''});
                clone.appendTo('table.promix > tbody');
                index++;
            }
        });

        $('table.promix tbody').on('click', '.remove-line', function(){
            var tr = $('table.promix > tbody > tr');
            if(tr.length === 1){
                tr.addClass('hidden').find('input,select').val('').attr('disabled','disabled');
                tr.find('input[type=hidden]').remove();
                tr.find('.packaging').text('');
            }else{
                $(this).closest('tr').remove();
            }
        });

        $('form').submit(function(e){
            e.preventDefault();
            $.post($(this).data('action'), $(this).serialize())
            .done(function(response){
                if(!response.error_flag){
                    $.growl.notice({title:'Done', message:'Price list succesfully saved!'});
                    return;
                }
                alert('Cannot save price list due to an unknown error. Please try again later.');
            })
            .fail(function(){
                alert('An internal server error has occured. Please try again later.');
            })
            
        })

        $('table').on('change', '.item', function(){
            var unit = $(this).find('option:selected').data('packaging');
            $(this).closest('tr').find('.packaging').text(unit);
        })
    });
})(jQuery);