<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
        <h3 class="box-title">Master List</h3>
        <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= base_url('user_accounts/create')?>">Add new user account</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div>
    <div class="box-body no-padding">
        <table class="table table-striped">
            <thead><tr class="info"><th>Username</th><th>Full Name</th><th>Email</th><th>User type</th><th>Status</th><th></th></tr></thead>
            <tbody id="list" data-delete-url="<?=base_url('user_accounts/ajax_delete')?>">
            <?php foreach($users AS $row):?>
                <tr data-pk="<?=$row['ID']?>">
                    <td>
                        <a href="<?= base_url("user_accounts/update/{$row['ID']}") ?>"><?= $row['Username']?></a>
                    </td>
                    <td><?= "{$row['FirstName']} {$row['LastName']}"?></td>
                    <td><?= $row['Email']?></td>
                    <td>
                        <?php if($row['TypeID'] == M_Account::TYPE_ADMIN ):?>
                            <span class="label label-info">Administrator</span>
                        <?php else:?>
                            <span class="label label-default">Standard User</span>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if($row['Locked'] ):?>
                            <span class="label label-danger"><i class="fa fa-lock"></i> Locked</span>
                        <?php else:?>
                           <span class="label label-success"><i class="fa fa-ok"></i>Active</span>
                        <?php endif;?>
                        
                    </td>
                    <td>
                        <a class="btn btn-flat btn-xs btn-danger delete"><i class="fa fa-times"></i> Delete</a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>