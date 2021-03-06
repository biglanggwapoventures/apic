$(function () {
    var fnz = {};
    fnz.err = 'No items to show.';
    fnz.notification = $('#btn-view-more').parent().find('.notification');
    var getParams = {
        page: 1
    };
    $(document).ready(function () {
        function getData() {
            $.getJSON($('table.promix').attr('data-master-list-url'), getParams).done(function (data) {
                if (data.hasOwnProperty('data') && data.data.length > 0) {
                    if (data.data.length === 100) {
                        fnz.notification.text('');
                        $('#btn-view-more').text('Click to view more').removeClass('disabled hidden');
                    } else if (data.data.length < 100) {
                        fnz.err = 'End of list. No more data to show';
                        fnz.notification.text(fnz.err);
                        $('#btn-view-more').addClass('hidden');
                    }
                    populateTable('table.promix', data.data);
                } else {
                    fnz.notification.text(fnz.err);
                    $('#btn-view-more').addClass('hidden');
                }
            }).fail(function () {
                fnz.err = 'Internal server error. Try to refresh page. If error still persists, contact developer.';
                fnz.notification.text(fnz.err);
                $('#btn-view-more').addClass('hidden');
            });
        }
        function populateTable(tableSelector, data) {
            var transforms = {
                tag: 'tr', 'data-pk': '${id}', children: [
                    {tag: 'td', children: [{tag:'a', href:function(){return $('table.promix').attr('data-edit-url') + this.id}, html:'${id}' }]},
                    {tag: 'td', html: '${po_number}'},
                    {tag: 'td', html: '${date}'},
                    {tag: 'td', html: '${company_name}'},
                    {tag: 'td', html: '${total_amount}'},
                    {
                        tag: 'td', children: [
                            {tag: 'span', class: function () {
                                    switch (parseInt(this.status)) {
                                        case 4:
                                            return 'label label-success';
                                        case 7:
                                            return 'label label-danger';
                                        default:
                                            return 'label label-warning';
                                    }
                                }, html: function () {
                                    switch (parseInt(this.status)) {
                                        case 4:
                                            return 'Approved';
                                        case 7:
                                            return 'Cancelled';
                                        default:
                                            return 'Pending approval';
                                    }
                                }
                            }
                        ]
                    },
                    {
                        tag: 'td', children: [
                            {tag: 'div', class: 'row-actions', children: [
                                    {tag: 'a', class: 'btn btn-danger btn-xs btn-flat item-remove', html: 'Delete'}
                                ]
                            }
                        ]
                    }
                ]
            };
            $(tableSelector).json2html(data, transforms);
        }
        /*=====================
         INITIALIZE STICKY TABLE HEADER
         =====================*/
        $('table.promix').stickyTableHeaders({fixedOffset: $('.content-header')});
        /*=====================
         INITIALIZE DATEPICKER ON DATE FIELDS
         =====================*/
        $(".datepicker.has-default").datepicker({dateFormat: 'yy-mm-dd'});
        /*=====================
         RETRIEVE DATA
         =====================*/
        getData();
        $('#btn-view-more').click(function () {
            fnz.err = 'End of list. No more data to show';
            $(this).text('Loading..').addClass('disabled');
            getParams.page++;
            getData();
        });
        $('form#advanced-search').submit(function () {
            fnz.err = 'No data matched your search criterias.';
            getParams.page = 1;
            $.each($(this).serializeArray(), function (i, field) {
                getParams[field.name] = field.value;
            });
            $('table.promix tbody').html('');
            getData();
            return false;
        });
        /*=====================
         REMOVE DATA
         =====================*/
        $('table.promix').on('click', '.item-remove', function (e) {
            $(this).promixDeleteItem({
                url: $('table.promix').attr('data-delete-url'),
                pk: $(this).closest('tr').attr('data-pk'),
                dialog_placement: 'left'
            });
        });

    });
}(jQuery));