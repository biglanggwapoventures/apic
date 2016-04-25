<!DOCTYPE html>
<html style="background: #e1e2fa;">
    <head>
        <meta charset="UTF-8">

        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name='site-developer' content='Adrian Natabio'>
        <meta name='site-designer' content='Adrian Natabio'>
        <title>Arditezza Poultry Intergration Corp. | Log in</title>
        <?php include_css(array('AdminLTE.css', 'bootstrap.min.css', 'font-awesome.min.css')) ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body style="background: #e1e2fa;">
        <div class="form-box" id="login-box">
            <div class="header"><img class="img-responsive center-block" src="<?= base_url('assets/img/logo.jpeg') ?>"/></div>
            <div class="body bg-gray">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Username" required/>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required/>
                </div>  
            </div>
            <div class="footer">                                                               
                <button type="submit" class="btn bg-olive btn-block btn-flat">Sign me in</button>  
            </div>
        </div>  
        <!-- Bootstrap -->
        <?php include_js(array('jquery-2.1.1.min.js', 'bootstrap.min.js')) ?>
        <script type="text/javascript">
            $('[name=username]').keyup(function (e) {
                if (e.keyCode === 13) {
                    if($('[name=password]').val()){
                        login();
                        return;
                    }
                    $('[name=password]').focus();
                }
            });
            $('[name=password]').keyup(function (e) {
                if (e.keyCode === 13) {
                    login();
                }
            });
            $("button[type=submit]").click(function () {
                login();
            });
            function login() {
                $('input').parent().removeClass('has-error').find('span').remove();
                $("button[type=submit]").addClass('disabled');
                $.post('<?= base_url('login/a_do_login') ?>', {username: $('[name=username]').val(), password: $('[name=password]').val()}, 'json').done(function (data) {
                    if (!data.error_flag) {
                        window.location.href = '<?= base_url('home') ?>';
                        return;
                    }
                    $(data.message).each(function (i) {
                        var field = Object.keys(data.message[i])[0];
                        $('input[name=' + field + ']').parent().addClass("has-error").append('<span class="help-block">' + data.message[i][field] + '</span>');

                    });
                }).fail(function () {
                    alert('Internal Server Error!');
                }).always(function () {
                    $("button[type=submit]").removeClass('disabled');
                });
            }
        </script>
    </body>
</html>