$(document).ready(function() {
    $.fn.editable.defaults.ajaxOptions = {type: "post"};
    $.fn.editable.defaults.mode = 'popup';
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
    function addRowContent(id, bankName, bankBranch, accountNumber) {
        var tableCell = [];
        tableCell[0] = '<a class="editable" data-name="bank_name" href="javascript:void(0)">' + bankName + '</a>';
        tableCell[1] = '<a class="editable" data-name="bank_branch" href="javascript:void(0)">' + bankBranch + '</a>';
        tableCell[2] = '<a class="editable" data-name="account_number" href="javascript:void(0)">' + accountNumber + '</a>';
        tableCell[3] = '<a href="javascript:void(0)" class="remove-item"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>';
        $("tbody").append('<tr data-pk="' + id + '"><td>' + tableCell.join("</td><td>") + '</td></tr>');
        initializeEditable($("tbody tr:last .editable"));
    }
    $.getJSON($("input[name=data-get-master-list-url]").val())
            .done(function(json) {
                if (json.flag_error) {
                    $("tbody").append('<tr><td colspan="4" class="text-center">No data fetched.</td></tr>');
                } else {
                    $(json.data).each(function(i) {
                        addRowContent(json.data[i].id, json.data[i].bank_name, json.data[i].bank_branch, json.data[i].account_number);
                    });
                }
            })
            .fail(function(jqxhr, textStatus, error) {
                $("tbody").append('<tr><td colspan="4" class="text-center">Error has occured!</td></tr>');
            });
    $("form").submit(function() {
        $(".modal-content").loadify("enable");
        $(".callout-danger ul li").remove();
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                $(".modal-content").loadify("disable");
                if (data.error_flag) {
                    $(".callout-danger").removeClass("hidden");
                    $(".callout-danger ul").append("<li>" + data.message.join("</li><li>") + "</li>");
                } else {
                    addRowContent(data.data.id, $("input[name=bank_name]").val(), $("input[name=bank_branch]").val(), $("input[name=account_number]").val());
                    $("#add-bank-account-modal input").val("");
                    $("#add-bank-account-modal").modal("hide");
                    $(".callout-danger").addClass("hidden").find("li").remove();
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
