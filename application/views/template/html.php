<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= isset($data_tab_title) && $data_tab_title ? $data_tab_title . ' | ' : '' ?>Arditezza Poultry Intergration Corporation</title>

        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="site-developer" content="Adrian Natabio">
        <?php $base_url = base_url(); ?>
        <?php $css_url = $base_url . 'assets/css/'; ?>
        <?php $js_url = $base_url . 'assets/js/'; ?>
        <?php $img_url = $base_url . 'assets/img/'; ?>
        <?php $uploads_url = $base_url . 'assets/uploads/'; ?>
        <!-- bootstrap 3.0.2 -->
        <link href="<?= $css_url . 'bootstrap.min.css' ?>" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?= $css_url . 'font-awesome.min.css' ?>" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="<?= $css_url . 'AdminLTE.css' ?>" rel="stylesheet" type="text/css" />
        <!-- jQuery UI style -->
        <link href="<?= $css_url . 'jQueryUI/jquery-ui-1.10.3.custom.min.css' ?>" rel="stylesheet" type="text/css" />
        <!-- Gritter style -->
        <link href="<?= $css_url . 'growl/jquery.growl.css' ?>" rel="stylesheet" type="text/css" />
        <link href="<?= $css_url . 'custom.css' ?>" rel="stylesheet" type="text/css" />
        <?php isset($data_css) ? include_css($data_css) : NULL ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/js/plugins/bootstrap-datetimepicker/bs-datetimepicker.min.css')?>">
        <script type="text/javascript">var isAdmin = <?= $this->session->userdata('type_id') == M_Account::TYPE_ADMIN ? 'true' : 'false' ?>;</script>
        <!-- jQuery 2.0.2 -->
        <script src="<?= $js_url . 'jquery-2.1.1.min.js' ?>"></script>
        <!-- jQuery UI script -->
        <script src="<?= $js_url . 'jquery-ui.min.js' ?>" type="text/javascript"></script>


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue fixed">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                APIC IS
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span> <?= $this->session->userdata('name')  ?><i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="<?= $this->session->userdata('avatar') ? "{$uploads_url}/{$this->session->userdata('avatar')}" :  "{$img_url}display-photo-placeholder.png" ?>" class="img-circle" alt="User Image" />
                                    <p>
                                        <?= $this->session->userdata('name') ?>   
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" id="update-profile-btn" class="btn btn-default btn-flat" data-url="<?= base_url( 'user_accounts/get_user/'.$this->session->userdata('user_id') ) ?>" data-toggle="modal" data-target="#updateUser">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?= $base_url . 'login/do_logout' ?>" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?= $this->session->userdata('avatar') ? "{$uploads_url}/{$this->session->userdata('avatar')}" :  "{$img_url}display-photo-placeholder.png" ?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info" style="text-overflow:ellipses;">
                            <p><?= $this->session->userdata('name') ? $this->session->userdata('name') : 'Unknown User' ?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form hidden">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <?php $mod_access = $this->session->userdata('module_access'); ?>
                    <?php $is_admin = (int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN; ?>
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="<?= $data_nav === NAV_HOME ? 'active' : '' ?>">
                            <a href="<?= base_url('home') ?>" style="cursor: pointer">
                                <i class="fa fa-home"></i> <span>Home</span>
                            </a>
                        </li>
                        <?php if ($is_admin || (int) $mod_access['inventory'] === 1): ?>
                            <li class="treeview <?= $data_nav === NAV_INVENTORY ? 'active' : '' ?>">
                                <a style="cursor: pointer">
                                    <i class="fa fa-briefcase"></i>
                                    <span>Inventory</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="<?= $base_url . 'inventory/products' ?>" ><i class="fa fa-angle-double-right"></i> Products</a></li>
                                    <li><a href="<?= $base_url . 'inventory/categories' ?>"><i class="fa fa-angle-double-right"></i> Categories</a></li>
                                    <li><a href="<?= $base_url . 'inventory/units' ?>" ><i class="fa fa-angle-double-right"></i> Units</a></li>
                                    <li class="hidden"><a href="<?= $base_url . 'inventory/stock_adjustments' ?>" ><i class="fa fa-angle-double-right"></i> Stock Adjustments</a></li>
                                    <li class="hidden"><a href="<?= $base_url . 'inventory/withdrawals' ?>" ><i class="fa fa-angle-double-right"></i> Stock Withdrawals</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($is_admin || (int) $mod_access['sales'] === 1): ?>
                            <li class="treeview <?= $data_nav === NAV_SALES ? 'active' : '' ?>">
                                <a href="#">
                                    <i class="fa fa-barcode"></i>
                                    <span>Sales</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="<?= $base_url . 'sales/orders' ?>" ><i class="fa fa-angle-double-right"></i> Orders</a></li>
                                    <li><a href="<?= $base_url . 'sales/deliveries' ?>" ><i class="fa fa-angle-double-right"></i> Packing List</a></li>
                                    <li><a href="<?= $base_url . 'sales/receipts' ?>" ><i class="fa fa-angle-double-right"></i> Receipts</a></li>
                                    <li><a href="<?= $base_url . 'sales/customer' ?>" ><i class="fa fa-angle-double-right"></i> Customers</a></li>
                                    <li><a href="<?= $base_url . 'sales/agents' ?>"><i class="fa fa-angle-double-right"></i> Sales Agents</a></li>
                                    <li><a href="<?= $base_url . 'sales/trucking' ?>"><i class="fa fa-angle-double-right"></i> Trucking</a></li>
                                    <li><a href="<?= $base_url . 'sales/trucking_assistants' ?>"><i class="fa fa-angle-double-right"></i> Trucking Assistants</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($is_admin || (int) $mod_access['purchases'] === 1): ?>
                            <li class="treeview <?= $data_nav === NAV_PURCHASES ? 'active' : '' ?>">
                                <a href="#">
                                    <i class="fa fa-shopping-cart"></i> <span>Purchases</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="<?= $base_url . 'purchases/orders' ?>" ><i class="fa fa-angle-double-right"></i> Orders</a></li>
                                    <li><a href="<?= $base_url . 'purchases/receiving' ?>" ><i class="fa fa-angle-double-right"></i> Receiving</a></li>
                                    <li><a href="<?= $base_url . 'purchases/disbursements' ?>" ><i class="fa fa-angle-double-right"></i> Disbursement</a></li>
                                    <li><a href="<?= $base_url . 'purchases/other_disbursements' ?>" ><i class="fa fa-angle-double-right"></i> Disbursements (Others)</a></li>
                                    <li class="<?= $this->uri->segment(2) === 'purchases' ? 'active' : '' ?>"><a href="<?= $base_url . 'purchases/suppliers' ?>" ><i class="fa fa-angle-double-right"></i> Suppliers</a></li>
                                    <li class="<?= $this->uri->segment(2) === 'purchases' ? 'active' : '' ?>"><a href="<?= $base_url . 'purchases/chart_of_accounts' ?>" ><i class="fa fa-angle-double-right"></i> Chart of accounts</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($is_admin || (int) $mod_access['production'] === 1): ?>
                            <li class="treeview hidden <?= $data_nav === NAV_PRODUCTION ? 'active' : '' ?>">
                                <a href="#">
                                    <i class="fa fa-gears"></i> <span>Production</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href='<?= $base_url . 'production/job_order' ?>' ><i class="fa fa-angle-double-right"></i> Job Orders</a></li>
                                    <li><a href='<?= $base_url . 'production/formulations' ?>'><i class="fa fa-angle-double-right"></i> Formulations</a></li>
                                    <li>
                                        <a href='<?= $base_url . 'production/receiving' ?>' ><i class="fa fa-angle-double-right"></i> Receiving</a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($is_admin || (int) $mod_access['accounting'] === 1): ?>
                            <li class="treeview <?= $data_nav === NAV_ACCOUNTING ? 'active' : '' ?>">
                                <a href="#">
                                    <i class="fa fa-folder-open"></i> <span>Accounting</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a  href="<?= $base_url . 'accounting/bank_accounts' ?>"><i class="fa fa-angle-double-right"></i> Bank Accounts</a></li>
                                    <li class="hidden"><a  href="<?= $base_url . 'accounting/dummy_checks' ?>"><i class="fa fa-angle-double-right"></i> Dummy Checks</a></li>
                                    <li class="hidden"><a  href="<?= $base_url . 'accounting/print_checks' ?>"><i class="fa fa-angle-double-right"></i> Print Checks</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <li class="hidden treeview <?= $data_nav === NAV_MAINTAINABLE ? 'active' : '' ?>">
                            <a href="#">
                                <i class="fa fa-cubes"></i> <span>Maintainable</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?= $this->uri->segment(2) === 'chart_of_accounts' ? 'active' : '' ?>"><a href="<?= $base_url . 'maintainable/chart_of_accounts' ?>" ><i class="fa fa-angle-double-right"></i> Chart of accounts</a></li>
                            </ul>
                        </li>
                        <?php if ($is_admin || (int) $mod_access['reports'] === 1): ?>
                            <li class="treeview <?= $data_nav === NAV_REPORTS ? 'active' : '' ?>">
                                <a href="#">
                                    <i class="fa fa-list-ol"></i> <span>Reports</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <!--NEW REPORTS-->
                                    <li class="hidden"><a  href='<?= $base_url . 'reports/sales' ?>'><i class="fa fa-angle-double-right"></i> Sales</a></li>
                                    <li><a  href='<?= $base_url . 'reports/product_type_sales' ?>'><i class="fa fa-angle-double-right"></i> Product Type Sales</a></li>
                                    <li><a href='<?= $base_url . 'reports/outstanding_packing_list' ?>'><i class="fa fa-angle-double-right"></i> Outstanding Packing List</a></li>
                                    <li><a  href='<?= $base_url . 'reports/outstanding_payables' ?>'><i class="fa fa-angle-double-right"></i> Outstanding Payables</a></li>
                                    <li><a  href='<?= $base_url . 'reports/receivables' ?>'><i class="fa fa-angle-double-right"></i> Aging of Receivables</a></li>
                                    <li><a  href='<?= $base_url . 'reports/payables' ?>'><i class="fa fa-angle-double-right"></i> Aging of Payables</a></li>
                                    <li><a  href='<?= $base_url . 'reports/collection_report' ?>'><i class="fa fa-angle-double-right"></i> Collection Report</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($is_admin): ?>
                            <li class="<?= $data_nav === NAV_USERS ? 'active' : '' ?>">
                                <a href="<?= base_url('user_accounts') ?>" style="cursor: pointer">
                                    <i class="fa fa-users"></i> <span>Users</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($is_admin): ?>
                            <li class="">
                                <a href="<?= base_url('backup/database') ?>" target="_blank">
                                    <i class="fa fa-download"></i> <span>Download DB Backup</span>
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <?php $data_gritter = $this->session->flashdata('FLASH_NOTIF') ?>
                <section class="content-header" data-gritter="<?= htmlspecialchars($data_gritter ? $data_gritter : json_encode(array())) ?>">
                    <h1 class="clearfix">
                        <?= $data_title ?>
                        <small><?= $data_subtitle ?> </small>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <?= $main_view ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <div class="modal fade" id="updateUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="form-horizontal" id="personal-information" action="<?= base_url('user_accounts/ajax_update/'.$this->session->userdata('user_id')) ?>" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Update <strong><?= $this->session->userdata('name') ?></strong></h4>
                        </div> <!-- end .modal-header -->
                        <div class="modal-body">
                                <div class="callout callout-danger hidden">
                                    <ul class="list-unstyled">
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Username *</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= $this->session->userdata('username') ?></p>
                                    </div>   
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">First Name *</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static" id="fname"></p>
                                    </div>   
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Last Name *</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static" id="lname"></p>
                                    </div>   
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Email Address</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static" id="email"></p>
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
                                    <label class="control-label col-sm-3">Display photo</label>
                                    <div class="col-sm-8">
                                        <input type="file" name="dp"/>
                                         <span class="help-block">Max dimension: 1024x768 px | Max filesize: 2MB | Accepts only: jpeg,png,jpg</span>
                                    </div>
                                </div>
                        </div> <!-- end .modal-body -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-flat pull-left">Save</button>
                            <button type="button" class="btn btn-warning btn-flat" data-dismiss="modal" aria-model="Close">Cancel</button>
                        </div> <!-- end .modal-footer -->
                    </form>
                </div>
            </div>
        </div>

        <!-- Bootstrap -->
        <script src="<?= $js_url . 'bootstrap.min.js' ?>" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="<?= $js_url . 'AdminLTE/app.js' ?>" type="text/javascript"></script>
        <!-- Gritter script -->
        <script src="<?= $js_url . 'plugins/growl/jquery.growl.js' ?>" type="text/javascript"></script>
        <!-- Main App -->
        <script src="<?= $js_url . 'main.js' ?>" type="text/javascript"></script>
        <script src="<?= $js_url . 'jquery.form.min.js' ?>" type="text/javascript"></script>
        <script src="<?= $js_url . 'update-profile.js' ?>" type="text/javascript"></script>

        <?php isset($data_javascript) ? include_js($data_javascript) : NULL; ?>
    </body>
</html>
