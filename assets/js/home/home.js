(function ($) {
    $(document).ready(function () {
        /*=====================
         DATETIME PICKER
         =====================*/
        $(".datetimepicker").datetimepicker({timeFormat: 'hh:mm tt'});

        /*=====================
         DATATABLES
         =====================*/
        $('.task-table').dataTable({
            bPaginate: false,
            bLengthChange: false,
            bFilter: false,
            bSort: true,
            bInfo: false,
            bAutoWidth: true,
            bJQueryUI: false
        });

        /*=====================
         ADD NEW TASK (AJAX)
         =====================*/
        $("#add-task-form").submit(function () {
            $(".has-error").removeClass("has-error");
            $(".help-block").text("");
            $.post($(this).attr("action"), $(this).serialize()).done(function (response) {
                if (response.error_flag) {
                    var error = response.data;
                    $(error).each(function (i) {
                        $("[name=" + error[i].field + "]").parent().addClass("has-error").find("span.help-block").text(error[i].message);
                    });
                } else {
                    $("#add-task-form select,input,textarea").val('');
                    $("#add-task-modal").modal("hide");
                }
            }).fail(function () {
                alert('Internal Server Error: Please contact developer.');
            });
            return false;
        });


    });
})(jQuery);
