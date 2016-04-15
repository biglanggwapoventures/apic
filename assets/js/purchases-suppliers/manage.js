$(document).ready(function() {
    $.fn.editable.defaults.ajaxOptions = {type: 'post'};
    $.fn.editable.defaults.mode = 'inline';
    function initializeEditable(element) {
        var url = $("tbody").attr("data-edit-url");
        element.editable({
            url: url,
            placement: 'right',
            pk: function() {
                return $(this).closest("tr").attr("data-pk");
            },
            success: function(response) {
                if (response.error_flag) {
                    return response.message;
                }
            },
            error: function(response) {
                alert(response.responseText);
            }
        });
    }
    function addRowContent(id, supplierName, tinNumber,address, contactPerson, contactNumber) {
        var tableCell = [];
        tableCell[0] = '<a class="editable" data-name="name" href="javascript:void(0)">' + supplierName + '</a>';
        tableCell[1] = '<a class="editable" data-name="tin_number" href="javascript:void(0)">' + tinNumber + '</a>';
        tableCell[2] = '<a class="editable" data-name="address" href="javascript:void(0)">' + address + '</a>';
        tableCell[3] = '<a class="editable" data-name="contact_person" href="javascript:void(0)">' + contactPerson + '</a>';
        tableCell[4] = '<a class="editable" data-name="contact_number" href="javascript:void(0)">' + contactNumber + '</a>';
        tableCell[5] = '<a href="javascript:void(0)" class="remove-item"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>';
        $("tbody").append('<tr data-pk="' + id + '"><td>' + tableCell.join("</td><td>") + '</td></tr>');
        initializeEditable($("tbody tr:last .editable"));
    }
    $.getJSON($("input[name=data-get-master-list-url]").val())
            .done(function(json) {
                if (json.flag_error) {
                    $("tbody").append('<tr><td colspan="5" class="text-center">No data fetched.</td></tr>');
                } else {
                    $(json.data).each(function(i) {
                        addRowContent(json.data[i].id, json.data[i].name, json.data[i].tin_number, json.data[i].address, json.data[i].contact_person, json.data[i].contact_number);
                    });
                }
            })
            .fail(function(jqxhr, textStatus, error) {
                $("tbody").append('<tr><td colspan="4" class="text-center">Error has occured!</td></tr>');
            });
    $("form").submit(function() {
        $(".modal-content").loadify("enable");
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                $(".modal-content").loadify("disable");
                if (data.error_flag) {
                    $(data.message).each(function(i) {
                        $formGroup = $('input[name=' + data.message[i].field_name + ']').parent();
                        $formGroup.addClass("has-error").find("span.help-block").remove();
                        $formGroup.addClass("has-error").append('<span class="help-block">' + data.message[i].error_message + '</span>');
                    });
                } else {
                    $("#supplier-modal").modal("hide");
                    $("#supplier-modal .modal-body .form-group").removeClass("has-error").find("span.help-block").remove();
                    addRowContent(data.data.id, $("input[name=name]").val(), $("input[name=tin_number]").val(), $("input[name=address]").val(), $("input[name=contact_person]").val(), $("input[name=contact_number]").val());
                    $("#supplier-modal .modal-body input").val("");
                }
            },
            error: function(xhr, err) {
                $(".modal-content").loadify("disable");
                alert("Error has occured. Please try again later.");
            }
        });
        return false;
    });
    $("tbody").on('click', '.remove-item', function() {
        $(this).promixDeleteItem({
            url: $("tbody").attr("data-delete-url"),
            pk: $(this).closest("tr").attr("data-pk"),
            dialog_placement: 'left',
            alreadyJSON: true
        });
    });
});