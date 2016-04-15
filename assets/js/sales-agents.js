$(".price").priceFormat({prefix: ''});
$('.tbody-item-remove').click(function() {
    $(this).promixDeleteItem({
        url: $(".pm-tbody").attr("data-delete-url"),
        pk: $(this).closest("tr").attr("data-pk"),
        dialog_placement: 'left'
    });
});