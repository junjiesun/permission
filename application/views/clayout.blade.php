<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>权限管理 Demo</title>
    @section('css')
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon"/>
        <link href="/static/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
        <link href="/static/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
        <link href="/static/css/animate.min.css" rel="stylesheet">
        <link href="/static/css/style.min862f.css?v=4.1.0" rel="stylesheet">
        <link href="/static/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    @show
</head>
<body class="fixed-sidebar full-height-layout gray-bg">
    <div class="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span style="margin-right: 10px;"><img alt="image" class="img-circle" src="/images/logo.png" /></span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                               <span class="block m-t-xs">
                                   <strong class="font-bold">welcome,<?php echo $user['name']?>
                                   </strong>
                               </span>
                                </span>
                            </a>
                        </div>
                    </li>
                    <li class="<?php if($currentCM == 'index_index') echo 'active'; ?>">
                        <a href="/"><i class="fa fa-th-large"></i> <span class="nav-label">首页</span></a>
                    </li>
                    <?php

                    $page = (isset($page) && !empty($page)? strtolower($page): '');
                    $displayMenu = (isset($displayMenu) && !empty($displayMenu)? strtolower($displayMenu): '');
                    foreach ($menuList as $menu)
                    {
                        echo '<li ';
                        $active = false;
                        if(isset($menu['submenu']) )
                        {
                            foreach ( $menu['submenu'] as $smenu )
                            {
                                $menuCM = strtolower($smenu['controller'].'_'.$smenu['method']);

                                if ( $currentCM == $menuCM || $page == $menuCM || $displayMenu == $menuCM )
                                {
                                    $active = true;
                                }
                            }
                        }
                        echo $active?' class="active">':' >';
                        echo '<a href="javascript:void(0);">';
//                         echo '<i class="fa '.(!empty($menu['icon'])? $menu['icon']: 'fa-th-large').'"></i>';
                        echo '<i class="fa fa-th-large"></i>';
                        echo '<span class="nav-label">' . $menu['name'] . '</span><span class="fa arrow"></span>';
                        echo '</a>';
                        echo '<ul class="nav nav-second-level collapse" aria-expanded="false" style="height: 0px;">';

                        if( isset($menu['submenu']) )
                        {
                            foreach ( $menu['submenu'] as $smenu )
                            {
                                $menuCM = strtolower($smenu['controller'].'_'.$smenu['method']);

                                if ( $currentCM == $menuCM || $page == $menuCM || $displayMenu == $menuCM )
                                {
                                    echo '<li class="active">';
                                }
                                else
                                {
                                    echo '<li>';
                                }
                                echo '<a href="/'. $smenu['menuUrl'] .'">'. $smenu['name'] .'</a>';
                                echo '</li>';

                            }
                        }

                        echo '</ul>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <ul class="nav navbar-top-links navbar-right">

                        {{--<li><a href="/doclearpost"><i class="fa fa-trash-o"></i> 清理缓存</a></li>--}}
                        <li><a href="/logout"><i class="fa fa-sign-out"></i> 退出</a></li>
                    </ul>
                </nav>
            </div>
            <div class="row J_mainContent" id="content-main">
                @yield('content')
            </div>

        </div>
    </div>
        @section('js')
            <script src="/static/js/jquery.min.js?v=2.1.4"></script>
            <script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
            <script src="/static/js/plugins/metisMenu/jquery.metisMenu.js"></script>
            <script src="/static/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
            <script src="/static/js/plugins/layer/layer.min.js"></script>
            <script src="/static/js/hplus.min.js?v=4.1.0"></script>
            <script src="/static/js/plugins/pace/pace.min.js"></script>
            <script src="/static/monitor/plugin/myAjaxForm.js"></script>
            <script src="/static/monitor/monitor.js"></script>
            <script src="/static/js/alertmodal.js"></script>
    @show
</body>
</html>