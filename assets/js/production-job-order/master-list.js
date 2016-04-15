$(function () {
    var fnz = {};
    fnz.err = 'No items to show.';
    fnz.notification = $('#btn-view-more').parent().find('.notification');
    var getParams = {
        page: 1
    };
    $(document).ready(function () {
        function initPrint() {
            $('.item-print').off();
            $('.item-print').each(function () {
                $(this).printPage({
                    url: $('table.promix').data('print-url') + $(this).closest('tr').attr('data-pk'),
                    message: "Your document is being created"
                });
            });
        }
        function getData() {
            $.getJSON($('table.promix').attr('data-master-list-url'), getParams).done(function (data) {
                if (data.hasOwnProperty('data') && data.data.length > 0) {
                    if (data.data.length === 50) {
                        fnz.notification.text('');
                        $('#btn-view-more').text('Click to view more').removeClass('disabled hidden');
                    } else if (data.data.length < 30) {
                        fnz.err = 'End of list. No more data to show';
                        fnz.notification.text(fnz.err);
                        $('#btn-view-more').addClass('hidden');
                    }
                    populateTable('table.promix', data.data);
                    initPrint();
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
                    {tag: 'td', children:[{tag:'a', href: function(){return $('table.promix').attr('data-edit-url') + this.id}, html: '${id}'}]},
                    {tag: 'td', html: '${production_code}'},
                    {tag: 'td', html: '${date_started}'},
                    {tag: 'td', children: [
                            {tag: 'span', class: function () {
                                    return parseInt(this.approved_by) ? 'label label-success' : 'label label-warning';
                                }, html: function () {
                                    return parseInt(this.approved_by) ? 'Approved' : 'Pending approval';
                                }}
                        ]},
                    {tag: 'td', children: [
                            {tag: 'div', class: 'row-actions', children: [
                                    {tag: 'a', class: function () {
                                            return parseInt(this.approved_by) ? 'btn btn-primary btn-xs btn-flat item-print' : 'btn btn-primary btn-xs btn-flat disabled';
                                        }, html: 'Print', href:function(){return $('table.promix').attr('data-receive-url') + this.id}},
                                    {tag: 'a', class: function () {
                                            return isAdmin ? 'btn btn-danger btn-xs btn-flat item-remove' : 'btn btn-danger btn-xs btn-flat hidden';
                                        }, html: 'Delete'}
                                ]
                            }
                        ]
                    }
                ]
            };
            $(tableSelector).json2html(data, transforms);
        }

        $('#daterangepicker').daterangepicker({format: 'MM/DD/YYYY'});

        /*=====================
         INITIALIZE STICKY TABLE HEADER
         =====================*/
        $('table.promix').stickyTableHeaders({fixedOffset: $('.content-header')});
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
            var that = $(this);
            var confirmed = confirm('Are you sure?');
            if(confirmed){
                var request = $.post($('table.promix').attr('data-delete-url'), {pk:that.closest('tr').attr('data-pk')});
                request.fail(function(){
                    $.growl.error({'title': 'Ooops!' ,'message':'Error on job order deletion.'});
                });
                request.done(function(response){
                    if(response.error_flag){
                        $.growl.error({'title': 'Ooops!', 'message':'Error on job order deletion.'});
                    }else{
                        that.closest('tr').remove();
                    }
                });
            }
        });

    });
}(jQuery));