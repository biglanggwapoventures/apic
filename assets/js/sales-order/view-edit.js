$(function () {
    $(document).ready(function () {
        function getCustomerError(text) {
            $('<option />', {'text': text ? text : 'No customers recorded.', 'value': 0}).appendTo('#customer');
        }
        /*=====================
         Retrieve customer list
         =====================*/
        $.getJSON($('#create-order').attr('data-get-customer-list-url')).done(function (data) {
            if (!data.length) {
                getCustomerError('');
                return;
            }
            var transform = {'tag': 'option', 'html': '${name}', 'value': '${id}'};
            $('#customer').json2html(data, transform);
            $('#customer').prepend($('<option />', {'text': 'Please select a customer', 'value': 0}));
        }).fail(function (data) {
            getCustomerError('Error ' + data.status + ': ' + data.statusText);
        });
        /*=====================
         Initialize datepicker on datepicker fields
         =====================*/
        $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
        $(".datepicker.has-default").datepicker('setDate', new Date());
        /*=====================
         On customer change
         =====================*/
        $('#customer').change(function () {
            var $val = parseInt($(this).val());
            if ($val) {
                var $price_list = [];
                $('tbody tr:not(#no-customer-selected)').remove();
                $('#no-customer-selected').find('td[colspan=9]').removeClass('bg-red').addClass('bg-light-blue').text('Retrieving customer\'s price list...');
                $.getJSON($('#create-order').attr('data-get-customer-pricing-url'), {customer_id: $val}).done(function (data) {
                    var products = {list: data};
                    /*=====================
                     Start build template
                     =====================*/
                    var transform = {
                        all: [{
                                tag: 'tr', children: [
                                    {tag: 'td', html: function () {return json2html.transform(this, transform.productSelect);}},
                                    {tag: 'td'},
                                    {tag: 'td'},
                                ]
                            }],
                        productSelect: [{tag: 'select', class: 'form-control input-sm', children: function () {return json2html.transform(this.list, transform.productOptions);}}],
                        productOptions: [{tag: 'option', html: '${product_description} (${product_formulation_code})', value: '${product_id}', 'price':'${price}'}]
                    };
                    $('tbody').json2html(products, transform.all);
                }).fail(function () {
                    //handle error
                });
            } else {
                $('#no-customer-selected').removeClass('hidden').find('td[colspan=9]').removeClass('bg-light-blue').addClass('bg-red').text('Please select a customer to continue.');
            }
        });
    });
}(jQuery));