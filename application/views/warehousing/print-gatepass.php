<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <style>
            html{width:50%;font-size: 12px;}
            body{margin: 0px 10px 0px 10px}
            .trip-ticket-id{padding-left:40px;font-size: 25px!important;}
            table{border-collapse:collapse;}
            table, td, th{border:1px solid black;}
            table.no-border{border: none!important;}
            table.no-border tr,td{border: none!important;}
            table.general-info td, table.delivery-details tbody td,th{text-transform: uppercase}
            table.general-info tr td:last-child{padding-left:  10px;}
            table.delivery-details{width:  100%;margin-top:8px;}
            table.delivery-details th{border:1px #000 dashed;border-right: none; border-left: none; padding-top:8px;padding-bottom: 8px;text-align: left}
            table.delivery-details tbody tr:first-child td{padding-top:8px;}
            table.delivery-details tfoot tr:first-child td{padding-top:8px; border-top:1px #000 dashed!important;}
        </style>
    </head>
    <body>
        <h4>PPLI TRIP TICKET</h4>
        <p>Trip Ticket #:<span class="trip-ticket-id"><?= $contents['general']['id'] ?></span></p>
        <table class="no-border general-info">
            <tr><td>date:</td><td><?= $contents['general']['exit_datetime'] ?></td></tr>
            <tr><td>truck #:</td><td><?= $contents['general']['fk_sales_trucking_id'] ?></td></tr>
            <tr><td>plate #:</td><td><?= $contents['general']['plate_number'] ?></td></tr>
            <tr><td>driver:</td><td><?= $contents['general']['driver'] ?></td></tr>
            <tr><td>truck boy:</td><td><?= $contents['general']['truck_boy'] ?></td></tr>
        </table>
        <table class="no-border delivery-details">
            <thead><tr><th>description</th><th>quantity</th><tr></thead>
            <tfoot><tr><td colspan="2">By <?= $contents['general']['generated_by'] ?></td></tr></tfoot>
            <tbody>
                <?php foreach ($contents['details'] as $c): ?>
                    <tr>
                        <td><?= "{$c['description']} ({$c['code']})" ?></td>
                        <td><?= "{$c['quantity']} {$c['unit']}" ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>
