$("#product-class").change(function() {
    if (parseInt($(this).val()) === 1) {
        $(".finished-product-only").attr("disabled", "disabled");
    } else {
        $(".finished-product-only").removeAttr("disabled");
    }
});
$(".pm-inventory-tbody").on('click', '.tbody-item-remove', function() {
    $(this).promixDeleteItem({
        url: $(".pm-inventory-tbody").attr("data-delete-url"),
        pk: $(this).closest("tr").attr("data-pk"),
        dialog_placement:'left'
    });
});