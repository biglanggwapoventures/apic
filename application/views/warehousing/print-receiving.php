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
        <h4>PRODUCT RECEIVING</h4>
        <p>PRODUCT RECEIVING #:<span class="trip-ticket-id"><?= $contents['ID'] ?></span></p>
        <table class="no-border general-info">
            <tr><td>Date:</td><td><?= $contents['datetime'] ?></td></tr>
            <tr><td>Remarks:</td><td><?= $contents['remarks'] ?></td></tr>
        </table>
        <table class="no-border delivery-details">
            <thead><tr><th>description</th><th>quantity</th><tr></thead>
            <!--<tfoot><tr><td colspan="2">By <?= $contents['generated_by'] ?></td></tr></tfoot>-->
            <tbody>
                
                <?php 
                    if(isset($contents['products']) && $contents['products']){
                        foreach ($contents['products'] as $c): ?>
                    <tr>
                        <td><?= "{$c['description']} ({$c['description']})" ?></td>
                        <td><?= "{$c['quantity']} {$c['unit_description']}" ?></td>
                    </tr>
                    <?php 
                        endforeach; 
                     }else{
                         echo "<tr><td>No Products</td></tr>";
                     }
                     ?>
            </tbody>
        </table>
    </body>
</html>
