<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <button type="button" id="print-me">Click me to print!</button>
    </body>
    <script type="text/javascript" src="<?= base_url('assets/js/jquery-2.1.1.min.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/js/printer/printer.js') ?>"></script>
    <script type="text/javascript">
        (function ($) {
            $(document).ready(function () {
                $("#print-me").printPage({
                    url: "<?=  base_url('development/dev_print/pdf')?>",
                    message: "Your document is being created"
                });
            });
        })(jQuery);
    </script>
</html>
