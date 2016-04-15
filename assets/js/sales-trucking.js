function resetModal(modalSelector) {
    $(modalSelector + " input").val("");
}
function initializeEditable(element) {
    var url = $("tbody").attr("data-edit-url");
    element.editable({
        url: url,
        placement: 'right',
        pk: function() {
            return $(this).closest("tr").attr("data-pk");
        },
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
    initializeEditable($(".editable"));
    $("form").ajaxForm(function(data) {
        data = $.parseJSON(data);
        if (data.error_flag) {
            $(".modal-body").prepend(data.message);
            //initializeEditable();
        } else {
            var input = [];
            input[0] = '<a class="editable" data-name="trucking_name">' + $("#trucking-name").val() + '</a>';
            input[1] = '<a class="editable" data-name="driver">' + $("#driver").val() + '</a>';
            input[2] = '<a class="editable" data-name="plate_number">' + $("#plate-number").val() + '</a>';
            input[3] = '<a class="remove-item"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>';
            $("tbody").append("<tr data-pk='" + data.error_flag.id + "'><td>" + input.join("</td><td>") + "</td><tr>");
            initializeEditable($(".editable"));
            $(".modal").modal("hide");
            resetModal(".modal");
        }
        $(".modal-content").loadify("disable");
    });
    $("form").submit(function() {
        $(".modal-body").find("p").remove();
        $(".modal-content").loadify("enable");
        return false;
    });
    $("tbody").on('click', '.remove-item', function() {
        $(this).promixDeleteItem({
            url: $("tbody").attr("data-delete-url"),
            pk: $(this).closest("tr").attr("data-pk")
        });
    });
});