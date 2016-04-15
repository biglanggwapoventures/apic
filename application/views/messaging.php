<style type="text/css">
    .user-item{
        border-top:1px solid #e9eaed;
        padding:5px;
        margin-top:0px!important;
        border-left: 3px solid transparent;
        cursor:pointer;
    }
    .user-item.unread{
        background: #f6f6f6;
    }
    .user-item:hover{
        background: #f5f7fa;
    }
    .user-item.active{
        background: #3c8dbc;
    }
    .user-item.active:hover  .media-heading{
        color:#fff!important;
    }
    .user-item.active:hover  .conversation-last-message{
        color:#fff!important;
    }
    .user-item.active  .media-heading{
        color:#fff!important;
    }
    .user-item.active  .conversation-last-message{
        color:#fff!important;
    }
    .user-image{
        height:50px;
        width:50px;
    }
    .user-list-wrapper{
        border-left:1px solid #e9eaed;
        border-right:1px solid #e9eaed;
        position:relative;
    }
    .conversation-last-message{
        min-width: 0;
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
        color:#9197a3;
    }
    textarea.message-content{
        resize: none;
    }
    .media-body{
        width:100%;
    }
</style>
<!-- MAILBOX BEGIN -->
<div class="mailbox row">
    <div class="col-xs-12">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-3">
                        <!-- Navigation - folders-->
                        <div class="user-list-wrapper">
                            <div id="user-list" style='bottom:0;top:0;'>
                                <div class="media user-item active" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar2.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">John Doe</h4>
                                        <div class="conversation-last-message">
                                            Did you know that one man is something worth dying fdor?
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item unread" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar04.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">Sarge Plenos</h4>
                                        <div class="conversation-last-message">
                                            Sumbong tika mama nako ha sige kag panungog!
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">John Carole</h4>
                                        <div class="conversation-last-message">
                                            Let's go play dota2 leh!
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar3.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">Hannah Juezan Pinote</h4>
                                        <div class="conversation-last-message">
                                            Did you know that one man is something worth dying for?
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar5.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">John Doe</h4>
                                        <div class="conversation-last-message">
                                            Did you know that one man is something worth dying fdor?
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item unread" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar04.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">Sarge Plenos</h4>
                                        <div class="conversation-last-message">
                                            Sumbong tika mama nako ha sige kag panungog!
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">John Carole</h4>
                                        <div class="conversation-last-message">
                                            Let's go play dota2 leh!
                                        </div>
                                    </div>
                                </div>
                                <div class="media user-item" style="">
                                    <a class="media-left" href="#">
                                        <img style="" class="user-image" src="<?= base_url('assets/img/avatar3.png') ?>" alt="...">
                                    </a>
                                    <div class="media-body user-last-message">
                                        <h4 class="media-heading">Hannah Juezan Pinote</h4>
                                        <div class="conversation-last-message">
                                            Did you know that one man is something worth dying for?
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.col (LEFT) -->
                    <div class="col-xs-8 col-sm-8 col-md-9">
                        <div class='box-header' style='border-bottom: 1px solid #eee'>
                            <h4 class='box-title'><i class='fa fa-circle text-success'></i> John Doe </h4>
                        </div>
                        <div class='box-body'>
                            <div id='conversation-wrapper'>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                                <div class="media">
                                    <a class="media-left media-top" href="#">
                                        <img src="<?= base_url('assets/img/avatar2.png') ?>" style='width:40px;height:40px;' alt="...">
                                    </a>
                                    <div class="media-body">
                                        <small class='pull-right'>1/7, 11:28pm</small>
                                        <a href='#'><h4 class="media-heading">John Doe </h4></a>
                                        <div class='conversation-message'>
                                            Mao man iya ingon brah
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='box-footer'>
                            <textarea class='message-content form-control' placeholder="Write a message..."></textarea>
                            <form class="form-inline" style='padding-top:5px;float:right'>
                                <div class="checkbox" style='margin-right:10px'>
                                    <label style='color:#a6a6a6'>
                                        <input id='toggle-send-button' type="checkbox" checked>Press Enter to send
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-xs hidden" id='btn-send'>Send</button>
                            </form>
                        </div>


                        <!-- MESSAGE BOX -->
                    </div><!-- /.col (RIGHT) -->
                </div><!-- /.row -->
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col (MAIN) -->
</div>
<!-- MAILBOX END -->
