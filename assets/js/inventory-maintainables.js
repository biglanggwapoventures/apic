function resetModal(modalSelector) {
    $(modalSelector + " input").val("");
}
function initializeEditable() {
    $('.tbody-item').editable({
        name: 'description',
        type: 'text',
        url: $(".pm-inventory-tbody").attr("data-edit-url"),
        placement: 'right',
        success: function(response) {
            response = $.parseJSON(response);
            if (response.error_flag) {
                return response.message;
            }
        }
    });
}
$(function() {
    $.fn.editable.defaults.ajaxOptions = {type: "post"};
    $.fn.editable.defaults.mode = 'popup';
    initializeEditable();
    $(".pm-inventory-form").ajaxForm(function(data) {
        data = $.parseJSON(data);
        if (!data.error_flag) {
            $(".pm-inventory-modal").modal("hide");
            $(".pm-inventory-tbody").append("<tr ><td><a data-pk='" + data.data.key + "' class='tbody-item'>"
                    + $(".pm-inventory-modal input[name=description]").val() + "</a></td><td><a class='tbody-item-remove'><i class='fa fa-times'></i> Remove</a></td></tr>");
            resetModal(".pm-inventory-modal");
            initializeEditable();
        } else {
            $(".modal-body > .form-group").prepend(data.message);
        }
        $(".modal-content").loadify("disable");
    });
    $(".pm-inventory-form").submit(function() {
        $(".modal-body > .form-group > p").remove();
        $(".modal-content").loadify("enable");
        return false;
    });
    $(".pm-inventory-tbody").on('click', '.tbody-item-remove', function() {
        $(this).promixDeleteItem({
            url: $(".pm-inventory-tbody").attr("data-delete-url"),
            pk: $(this).closest("tr").find(".tbody-item").attr("data-pk")
        });
    });
});