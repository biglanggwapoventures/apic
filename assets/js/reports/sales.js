(function ($) {
    $(document).ready(function () {
        var params = {
            offset: 1,
            daterange: '',
            customer: '',
            product: ''
        };
        $('table').stickyTableHeaders({fixedOffset: $('.content-header')});
        $('#daterangepicker').daterangepicker({format: 'YYYY-MM-DD'});
        $('form#report').submit(function () {
            params.offset = 1;
            params.daterange = $('[name=daterange]').val();
            params.customer = $('[name=customer]').val();
            params.product = $('[name=product]').val();
            params.include_summary = 1;
            $('#report-modal .modal-title').text('Loading');
            $('#report-modal .modal-body p').text('Generating report. Please wait...');
            $('#report-modal').modal('show');
            $.get($(this).attr('action'), params).done(function (data) {
                $('table#sr tbody').empty();
                $('table#sr tbody').append(data);
                $('#report-modal').modal('hide');
            });
            return false;
        });
        $('table').on('click', 'button.btn-more', function () {
            $('#report-modal .modal-title').text('Loading');
            $('#report-modal .modal-body p').text('Loading more data...');
            $('#report-modal').modal('show');
            delete params.include_summary;
            var btn = $(this);
            params.offset++;
            $.get($('form#report').attr('action'), params).done(function (data) {
                btn.closest('tr').prev().after(data);
                btn.closest('tr').remove();
                $('#report-modal').modal('hide');
            });
        });
    });
})(jQuery);
