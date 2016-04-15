<style type='text/css'>tbody tr td{vertical-align: middle!important;}</style>
<div class="row">
    <div class="col-md-10">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">Manage all users</h3>
                <div class='box-tools'>
                    <button data-target='#add-user-modal' data-toggle='modal' type="button" class="btn btn-success pull-right btn-sm"><i class="fa fa-plus"></i></button></div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-condensed">
                    <tbody id='user-details' data-edit-url='<?= base_url('users/manage_users/a_edit_details') ?>'>
                        <tr><th>Username</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Email</th>
                            <th>User role</th>
                            <th>Action(s)</th>
                        </tr>
                        <?php if (isset($user_listing) && is_array($user_listing)): ?>
                            <?php foreach ($user_listing as $user): ?>
                                <tr data-pk='<?= $user['ID'] ?>'><td><a class='editable' data-name='Username'><?= $user['Username'] ?></a></td>
                                    <td><a class='editable' data-name='FirstName'><?= $user['FirstName'] ?></a></td>
                                    <td><a class='editable' data-name='LastName'><?= $user['LastName'] ?></a></td></a>
                                    <td><a class='editable' data-name='Email'><?= $user['Email'] ?></a></td>
                                    <td><a class='editable is-select' data-name='TypeID' data-value='<?= $user['TypeID'] ?>'><?= (int) $user['TypeID'] === (int) M_Account::TYPE_ADMIN ? 'Site Administrator' : 'Normal User' ?></a></td>
                                    <td>
                                        <a role='button' href="javascript:void(0)" class="btn btn-danger btn-sm remove-item"><i class="fa fa-times"></i></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-10">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">Edit user's module access</h3>
            </div>
            <div class="box-body no-padding">

                <table class="table table-hover table-condensed">
                    <tbody data-save-access-url='<?= base_url('users/manage_users/a_update_module_access') ?>' id='mod-access'>
                        <tr><th></th>
                            <th>Inventory</th>
                            <th>Sales</th>
                            <th>Purchases</th>
                            <th>Production</th>
                            <th>Warehousing</th>
                            <th>Accounting</th>
                            <th>Reports</th>
                            <th>Action(s)</th>
                        </tr>
                        <?php if (isset($normal_user_listing) && is_array($normal_user_listing)): ?>
                            <?php foreach ($normal_user_listing as $user): ?>
                                <tr><td data-user-id='<?= $user['ID'] ?>'><?= $user['Username'] ?></td>
                                    <?php foreach ($user['module_access']['rights'] as $key => $rights): ?>
                                        <?php $checked = '' ?>
                                        <?php if ((int) $rights === 1): ?>
                                            <?php $checked = 'checked'; ?>
                                        <?php endif; ?>
                                        <td>
                                            <input type='checkbox' class='module' name='<?= "{$key}" ?>' value='1' <?= $checked ?>>
                                        </td>
                                    <?php endforeach; ?>
                                    <td><a href='javascript:void(0)' tabindex="0" role='button' class='btn btn-info btn-sm btn-save-access'><i class='fa fa-save'></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="add-user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('users/manage_users/a_add') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new user</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username" class='control-label'>Desired Username</label>
                        <input type="text" name="Username" class="form-control" id="username" placeholder="Username" required>
                    </div>
                    <div class='row'>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label for="first-name" class='control-label'>First name</label>
                                <input type="text" name="FirstName" class="form-control" id="firstname" placeholder="First Name" required>
                            </div>
                        </div>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label for="last-name" class='control-label'>Last name</label>
                                <input type="text" name="LastName" class="form-control" id="lastname" placeholder="Last Name" required>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label for="email" class='control-label'>Email</label>
                                <input type="email" name="Email" class="form-control" id="email" placeholder="(This is optional)">
                            </div>
                        </div>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label for="role" class='control-label'>Site Role</label>
                                <select name='TypeID' class='form-control' id='role' required>
                                    <option value='' selected disabled></option>
                                    <option value='<?= M_Account::TYPE_ADMIN ?>'>Site Administrator</option>
                                    <option value='<?= M_Account::TYPE_NORMAL ?>'>Normal User</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label for="password" class='control-label'> Password</label>
                                <input type="password" name="Password" class="form-control" id="password" required>
                            </div>
                        </div>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label for="confirm-password">Confirm Password</label>
                                <input type="password" name="ConfirmPassword" class="form-control" id="confirm-password" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>