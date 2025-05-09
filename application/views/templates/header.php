<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?php echo base_url(); ?>/assets/images/hunt_control_logo.png" type="image/png">
    <title><?php echo $title; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet"
        href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet"
        href="<?php echo base_url(); ?>assets/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/AdminLTE/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/AdminLTE/dist/css/skins/_all-skins.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">



    <!-- jQuery 3 -->
    <script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url(); ?>assets/AdminLTE/bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url(); ?>assets/AdminLTE/dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo base_url(); ?>assets/AdminLTE/dist/js/demo.js"></script>

    <script type="text/javascript">
        var base_url = "<?php echo base_url(); ?>";
        var aws_s3_url = 'https://d7s85wyrr26qk.cloudfront.net/';

        if (typeof Object.assign != 'function') {
            Object.assign = function (target) {
                'use strict';
                if (target == null) {
                    throw new TypeError('Cannot convert undefined or null to object');
                }

                target = Object(target);
                for (var index = 1; index < arguments.length; index++) {
                    var source = arguments[index];
                    if (source != null) {
                        for (var key in source) {
                            if (Object.prototype.hasOwnProperty.call(source, key)) {
                                target[key] = source[key];
                            }
                        }
                    }
                }
                return target;
            };
        }


        var colorDeer = 'rgba(0, 166, 90, 1)';
        var colorDeer_alpha = 'rgba(0, 166, 90, 0.2)';
        var colorHog = 'rgba(227, 197, 103 , 1)';
        var colorHog_alpha = 'rgba(227, 197, 103 , 0.2)';
        var colorTurkey = 'rgba(135,115,72, 1)';
        var colorTurkey_alpha = 'rgba(135,115,72, 0.2)';
        var colorOther = 'rgba(0,115,183, 1)';
        var colorOther_alpha = 'rgba(0,115,183, 0.2)';
        var colorOther2 = 'rgba(64, 224, 208, 1)';
        var colorOther3 = 'rgba(100, 149, 237, 1)';
        var colorOther4 = 'rgba(204, 204, 255, 1)';
        var colorRed = 'rgba(255, 0, 0 , 1)';
        var colorRed_alpha = 'rgba(255, 0, 0 , 0.2)';

        var chartColors = [colorDeer, colorHog, colorTurkey, colorOther, colorOther2, colorOther3, colorOther4]

        $.fn.digits = function () {
            return this.each(function () {
                $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
            })
        }
    </script>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a href="#" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><img src="<?php echo base_url(); ?>assets/images/hunt_control_logo.png"
                        height="40"></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="<?php echo base_url(); ?>assets/images/login_cover.png"
                        height="40"></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Menu</span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user"></i><span><?php if (isset($admin_user)) echo $admin_user['email']; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="<?php echo base_url(); ?>auth/logout"
                                            class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header" >Management</li>
                    <li class="<?php if ($page == 'manager/viewManagerList')
                        echo 'active'; ?>" <?php if (isset($admin_user) && isset($admin_user['org_id'])) echo 'hidden'; ?>>
                        <a href="<?php echo base_url(); ?>manager/viewManagerList">
                            Managers
                        </a>
                    </li>
                    <li class="<?php if ($page == 'user/viewUserList')
                        echo 'active'; ?>">
                        <a href="<?php echo base_url(); ?>user/viewUserList">
                            Users
                        </a>
                    </li>                    
                    <li class="<?php if ($page == 'camera/viewCameraList')
                        echo 'active'; ?>">
                        <a href="<?php echo base_url(); ?>camera/viewCameraList">
                            Cameras
                        </a>
                    </li>                    
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
        