check_credit_terms($("select#ct-id"));
function check_credit_terms(select) {
    var subject = $("input#ct-value");
    if (parseInt(select.val()) === 1) {
        subject.attr("disabled", "disabled");
    } else {
        subject.removeAttr("disabled");
    }
}
$("select#ct-id").change(function() {
    check_credit_terms($(this));
});
//price format for credit limit
$("input[name=credit_limit]").priceFormat({
    prefix: ''
});
//add line
$("#bank-account-add").click(function() {
    var removeLine = "<button type='button' class='btn btn-danger btn-sm bank-account-remove'><i class='fa fa-times'></i></button>";
    var template = $("#bank-accounts-group > tbody > #template").clone().removeAttr("id");
    template.find("td:last").append(removeLine);
    template.find("input").val("");
    $("#bank-accounts-group tbody").append(template);
});
//remove line
$("#bank-accounts-group").on('click', '.bank-account-remove', function() {
    $(this).closest("tr").remove();
});
//delete through ajax
$('.tbody-item-remove').click(function() {
    $(this).promixDeleteItem({
        url: $(".pm-tbody").attr("data-delete-url"),
        pk: $(this).closest("tr").attr("data-pk"),
        dialog_placement:'left'
    });
});
