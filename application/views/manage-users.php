<div class="row">
    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-heading text-center">
                <strong>Account Information</strong>
            </div>
            <div class="panel-body clearfix">
                <form class="form-horizontal" id="personal-information" action="<?=$form_action?>" method="post" enctype="multipart/form-data">
                    <div class="callout callout-danger hidden">
                        <ul class="list-unstyled">
                        </ul>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-sm-3">Username *</label>
                        <div class="col-sm-8">
                            <?php if(isset($data['Username'])):?>
                                <p class="form-control-static"><?= $data['Username']?></p>
                            <?php else:?>
                                 <input type="text" class="form-control" name="username"/>
                            <?php endif;?>
                        </div>   
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">First Name *</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="<?= isset($data['FirstName']) ? $data['FirstName'] : ''?>" name="firstname"/>
                        </div>   
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Last Name *</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="<?= isset($data['LastName']) ? $data['LastName'] : ''?>" name="lastname"/>
                        </div>   
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Email Address</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" value="<?= isset($data['Email']) ? $data['Email'] : ''?>" name="email"/>
                        </div>   
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control"  name="password"/>
                        </div>   
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Confirm Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="confirm_password"/>
                        </div>   
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Shared Token</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="shared_token" value="<?= isset($data['shared_token']) ? $data['shared_token'] : '' ?>"/>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Display photo</label>
                        <div class="col-sm-8">
                            <input type="file" name="dp"/>
                             <span class="help-block">Max dimension: 1024x768 px | Max filesize: 2MB | Accepts only: jpeg,png,jpg</span>
                        </div>   

                    </div>
                    <hr/>
                    <div class="form-group">
                        <label class="control-label col-sm-3">User type</label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                            <?php   $admin = isset($data['TypeID']) && $data['TypeID'] == M_Account::TYPE_ADMIN ? 'checked="checked"' : '' ?>
                              <input type="radio" name="type" value="<?= M_Account::TYPE_ADMIN?>" <?= $admin?>/> Administrator
                            </label>
                            <label class="radio-inline">
                            <?php   $standard = isset($data['TypeID']) && $data['TypeID'] == M_Account::TYPE_NORMAL ? 'checked="checked"' : '' ?>
                              <input type="radio" name="type" value="<?= M_Account::TYPE_NORMAL?>" <?= $standard?>> Standard user
                            </label>
                        </div>  
                    </div>
                    <div class="form-group">
                        <?php $locked = isset($data['Locked']) && $data['Locked'] ? 'checked="checked"' : '';?>
                        <label class="control-label col-sm-3">Actions(s)</label>
                        <div class="col-sm-8">
                            <div class="checkbox"><label><input type="checkbox" value="1" name="lock" <?=$locked?>/> Lock user</label></div>
                        </div>  
                    </div>
                     <div class="form-group">
                        <label class="control-label col-sm-3">Module Access</label>
                        <?php $no_mod = isset($data['TypeID']) && $data['TypeID']  == M_Account::TYPE_ADMIN ? 'disabled="disabled"' : ''?>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <?php $inv = isset($data['module_access']['inventory']) && $data['module_access']['inventory'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin" value="1" name="module[inventory]" type="checkbox" <?=$inv.' '. $no_mod?>/> Inventory 
                                </label><br/>
                                <label>
                                     <?php $sales = isset($data['module_access']['sales']) && $data['module_access']['sales'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin"  value="1" name="module[sales]" type="checkbox" <?=$sales.' '. $no_mod?>/> Sales 
                                    </label><br/>
                                <label>
                                     <?php $pur = isset($data['module_access']['purchases']) && $data['module_access']['purchases'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin" value="1" name="module[purchases]" type="checkbox" <?=$pur.' '. $no_mod?>/> Purchases 
                                </label><br/>
                                <label>
                                     <?php $pro = isset($data['module_access']['production']) && $data['module_access']['production'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin" value="1" name="module[production]" type="checkbox" <?=$pro.' '. $no_mod?>/> Production 
                                </label><br>
                                <label>
                                     <?php $acc = isset($data['module_access']['accounting']) && $data['module_access']['accounting'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin" value="1" name="module[accounting]" type="checkbox" <?=$acc.' '. $no_mod?>/> Accounting 
                                </label><br/>
                                <label>
                                 <?php $rep = isset($data['module_access']['reports']) && $data['module_access']['reports'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin" value="1" name="module[reports]" type="checkbox" <?=$rep.' '. $no_mod?>/> Reports 
                                </label><br/>
                                <label>
                                 <?php $rep = isset($data['module_access']['special_reports']) && $data['module_access']['special_reports'] ? 'checked="checked"': ''?>
                                    <input class="disabled-type-admin" value="1" name="module[special_reports]" type="checkbox" <?=$rep.' '. $no_mod?>/> Special Reports 
                                </label><br/>
                            </div>
                        </div>  
                    </div>
                    <hr/>
                    <button type="submit" class="btn btn-success btn-flat">Save</button>
                    <a class="btn btn-warning btn-flat pull-right cancel" href="<?=base_url('user_accounts')?>">Go back</a>
                </form>
            </div>
        </div>
    </div>
</div>