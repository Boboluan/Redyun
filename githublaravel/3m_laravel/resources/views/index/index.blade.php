@include('layout.header')
    <body class="index">
        <!-- 顶部开始 -->
        <div class="container">
            <div class="logo">
                <a href="/admin/index/index">后台管理系统</a></div>
            <div class="left_open">
                <a><i title="展开左侧栏" class="iconfont">&#xe699;</i></a>
            </div>

            <ul class="layui-nav right" lay-filter="">
                <li class="layui-nav-item">
                    <a href="javascript:;">{{$user}}</a>
                    <dl class="layui-nav-child">
                        <!-- 二级菜单 -->
                        <dd>
                            <a href="/admin/Logout">退出</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
        <!-- 顶部结束 -->
        <!-- 中部开始 -->
        <!-- 左侧菜单开始 -->
        <div class="left-nav">
            <div id="side-nav">
                <ul id="nav">


                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="产品管理">&#xe6f6;</i>
                            <cite>产品管理</cite>
                            <i class="iconfont nav_right"></i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('产品列表','/admin/ProductListPage')">
                                    <i class="iconfont">&#xe699;</i>
                                    <cite>产品列表</cite></a>
                            </li>

                        </ul>
                    </li>



                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="物质管理">&#xe723;</i>
                            <cite>物质管理</cite>
                            <i class="iconfont nav_right">&#xe6cb;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('物质列表','/admin/MatterListPage')">
                                    <i class="iconfont">&#xe699;</i>
                                    <cite>物质列表</cite></a>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="反馈管理">&#xe69b;</i>
                            <cite>反馈管理</cite>
                            <i class="iconfont nav_right">&#xe6cb;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('反馈列表','/admin/FeedbackList')">
                                    <i class="iconfont">&#xe699;</i>
                                    <cite>反馈列表</cite></a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="统计">&#xe6b2;</i>
                            <cite>统计管理</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('统计','/admin/CountList')">
                                    <i class="iconfont">&#xe699;</i>
                                    <cite>数据列表</cite></a>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="管理员管理">&#xe726;</i>
                            <cite>管理员管理</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('管理员列表','/admin/UserList')">
                                    <i class="iconfont">&#xe699;</i>
                                    <cite>管理员列表</cite></a>
                            </li>
                        </ul>
                    </li>



                </ul>
            </div>
        </div>
        <!-- <div class="x-slide_left"></div> -->
        <!-- 左侧菜单结束 -->
        <!-- 右侧主体开始 -->
        <div class="page-content">
            <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
                <ul class="layui-tab-title">
                    <li class="home">
                        <i class="layui-icon">&#xe68e;</i>我的桌面</li></ul>
                <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right">
                    <dl>
                        <dd data-type="this">关闭当前</dd>
                        <dd data-type="other">关闭其它</dd>
                        <dd data-type="all">关闭全部</dd></dl>
                </div>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <iframe src='/admin/index/earth' id="myiframe"  frameborder="0" scrolling="yes"  class="x-iframe"></iframe>
                    </div>
                </div>
                <div id="tab_show"></div>
            </div>
        </div>
        <div class="page-content-bg"></div>
        <style id="theme_style"></style>
        <!-- 右侧主体结束 -->
        <!-- 中部结束 -->
    </body>

<script>
    //解决iframe 里面又开启一个iframe
    var _topWin = window;
    while (_topWin != _topWin.parent.window) {
        _topWin = _topWin.parent.window;
    }
    if (window != _topWin)_topWin.document.location.href = '/admin/index/index';
</script>
</html>
