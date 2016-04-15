<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <?php include_css('bootstrap.min.css'); ?>
        <style type="text/css">
            tr.text-center td{text-align: center;}
            tr.no-padding td{padding: 0px!important;}
            .job-details tr td{padding:0px!important;border:1px solid #ddd;text-align: center;}
            .job-details{border:1px solid #ddd!important;}
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                    <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                    GY Warehouse 1, A. Bacaltos Sr. St., Lawaan 1, Talisay City<br>
                    Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                    <strong>J&nbsp;O&nbsp;B&nbsp;&nbsp;&nbsp;O&nbsp;R&nbsp;D&nbsp;E&nbsp;R&nbsp;#&nbsp;<?= $data['jo']['id'] ?></strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-condensed">
                        <tbody>
                            <tr class="text-center no-padding"><td style="width:50%">Date & time</td><td style="width:50%">Production Code</td></tr>
                            <tr class="text-center no-padding"><td><strong><?= date('m/d/Y H:i:s A', strtotime($data['jo']['date_started'])) ?></strong></td><td><strong><?= $data['jo']['production_code'] ?></strong></td></tr>                        
                        </tbody>
                    </table>
                </div>
            </div>
            <table class="table table-condensed job-details">
                <tbody>
                    <tr><td colspan="5" class="text-center"><strong>JOB ORDER DETAILS</strong></td></tr>
                   <tr class="no-padding"><td>Seq. #</td><td>Product Description</td><td>Formulation Code</td><td>Tons</td><td>Customer</td></tr>
                    <?php foreach ($data['details'] as $key=>$item): ?>
                    <tr class="no-padding"><td><?=$key+1?></td><td><?=$item['description']?></td><td><?=$item['formulation_code']?></td><td><?=$item['mix_number']?></td><td><?=$item['customer']?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
