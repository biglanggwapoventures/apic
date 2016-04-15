$(document).ready(function() {
    //bootstrap editable initialization
    $.fn.editable.defaults.ajaxOptions = {type: 'post'};
    $.fn.editable.defaults.mode = 'popup';
    $('.editable.is-select').editable({source: [{value: 1, text: 'Site Administrator'}, {value: 2, text: 'Normal User'}], type: 'select',
        url: $("tbody#user-details").attr("data-edit-url"),
        placement: 'right',
        pk: function() {
            return $(this).closest("tr").attr('data-pk');
        },
        success: function(response) {
            if (response.error_flag) {
                return response.message;
            }else{
                location.reload(true);
            }
        }});
    $('.editable').editable({
        url: $("tbody#user-details").attr("data-edit-url"),
        placement: 'right',
        pk: function() {
            return $(this).closest("tr").attr('data-pk');
        },
        success: function(response) {
            if (response.error_flag) {
                return response.message;
            }
        }
    });
    //checkbox design
    $('input[type="checkbox"]').iCheck({checkboxClass: 'icheckbox_minimal'});
    //save user mod access  
    $('tbody#mod-access').on('click', 'a.btn-save-access', (function() {
        $('a.btn-save-access').popover("destroy");
        var btn = $(this);
        btn.toggleClass("disabled");
        var param = {};
        param.user_id = btn.closest('tr').find('td:first').attr('data-user-id');
        $(this).closest('tr').find("input.module").each(function() {
            if ($(this).is(':checked')) {
                param[$(this).attr('name')] = 1;
            }
        });
        $.post($('tbody#mod-access').attr('data-save-access-url'), param).done(function(data) {
            btn.toggleClass("disabled").popover({content: data.message, trigger: 'focus'}).popover("show");
        }).fail(function() {
            btn.toggleClass("disabled").popover({content: 'Internal Server Error'}).popover("show");
        });
    }));
    //confirm password client side validation
    $("input[name=ConfirmPassword]").keyup(function() {
        var $this = $(this);
        $this.parent().removeClass("has-error").find("span").remove();
        if ($this.val() !== $("input[name=Password]").val()) {
            $this.parent().addClass("has-error").append('<span class="help-block">Passwords do not match!</span>');
        }
    });
    //add user function
    $("#add-user-modal form").submit(function() {
        $(this).find(".form-group").removeClass("has-error").find("span").remove();
        if ($("input[name=ConfirmPassword]").val() !== $("input[name=Password]").val()) {
            $(this).parent().addClass("has-error").append('<span class="help-block">Passwords do not match!</span>');
        }
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (!data.error_flag) {
                    location.reload(true);
                }
                var fields = Object.keys(data.message);
                $(fields).each(function(i) {
                    var msg = data.message[fields[i]];
                    if (msg) {
                        $("[name=" + fields[i] + "]").parent().addClass("has-error").append('<span class="help-block">' + msg + '</span>');
                    }
                });
            },
            error: function(xhr, err) {
                alert('Internal Server Error');
            }
        });
        return false;
    });
});
