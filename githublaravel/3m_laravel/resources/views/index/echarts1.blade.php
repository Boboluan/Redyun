@include('layout.formheader')
<body onload="startTime()">
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <blockquote class="layui-elem-quote">欢迎管理员：
                        <span class="x-red">{{$user}}</span>！当前时间:&nbsp;<font color="#0B395D"><span id="nowDateTimeSpan"></span></font>
                    </blockquote>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">数据统计</div>
                <div class="layui-card-body ">
                    <ul class="layui-row layui-col-space10 layui-this x-admin-carousel x-admin-backlog">
                        <li class="layui-col-md2 layui-col-xs6">
                            <a href="javascript:;" class="x-admin-backlog-body">
                                <h3>产品数</h3>
                                <p>
                                    <cite>{{$product}}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-md2 layui-col-xs6">
                            <a href="javascript:;" class="x-admin-backlog-body">
                                <h3>物质数</h3>
                                <p>
                                    <cite>{{$matter}}</cite></p>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">下载
                    <span class="layui-badge layui-bg-cyan layuiadmin-badge">月</span></div>
                <div class="layui-card-body  ">
                    <p class="layuiadmin-big-font">33,555</p>
                    <p>新下载
                        <span class="layuiadmin-span-color">10%
                                    <i class="layui-inline layui-icon layui-icon-face-smile-b"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">下载
                    <span class="layui-badge layui-bg-cyan layuiadmin-badge">月</span></div>
                <div class="layui-card-body ">
                    <p class="layuiadmin-big-font">33,555</p>
                    <p>新下载
                        <span class="layuiadmin-span-color">10%
                                    <i class="layui-inline layui-icon layui-icon-face-smile-b"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">下载
                    <span class="layui-badge layui-bg-cyan layuiadmin-badge">月</span></div>
                <div class="layui-card-body ">
                    <p class="layuiadmin-big-font">33,555</p>
                    <p>新下载
                        <span class="layuiadmin-span-color">10%
                                    <i class="layui-inline layui-icon layui-icon-face-smile-b"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">下载
                    <span class="layui-badge layui-bg-cyan layuiadmin-badge">月</span></div>
                <div class="layui-card-body ">
                    <p class="layuiadmin-big-font">33,555</p>
                    <p>新下载
                        <span class="layuiadmin-span-color">10%
                                    <i class="layui-inline layui-icon layui-icon-face-smile-b"></i></span>
                    </p>
                </div>
            </div>
        </div>
{{--        <div class="layui-col-md12">--}}
{{--            <div class="layui-card">--}}
{{--                <div class="layui-card-header">系统信息</div>--}}
{{--                <div class="layui-card-body ">--}}
{{--                    <table class="layui-table">--}}
{{--                        <tbody>--}}
{{--                        <tr>--}}
{{--                            <th>服务器地址</th>--}}
{{--                            <td>39.101.201.91</td></tr>--}}
{{--                        <tr>--}}
{{--                            <th>操作系统</th>--}}
{{--                            <td>WINNT</td></tr>--}}
{{--                        <tr>--}}
{{--                            <th>运行环境</th>--}}
{{--                            <td>WNPM</td></tr>--}}
{{--                        <tr>--}}
{{--                            <th>PHP版本</th>--}}
{{--                            <td>>=5.6</td></tr>--}}
{{--                        <tr>--}}
{{--                            <th>PHP运行方式</th>--}}
{{--                            <td>cgi-fcgi</td></tr>--}}
{{--                        <tr>--}}
{{--                            <th>MYSQL版本</th>--}}
{{--                            <td>5.7.26</td></tr>--}}
{{--                        <tr>--}}
{{--                            <th>laravel</th>--}}
{{--                            <td>8.0</td></tr>--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <!--        <div class="layui-col-md12">-->
        <!--            <div class="layui-card">-->
        <!--                <div class="layui-card-header">开发团队</div>-->
        <!--                <div class="layui-card-body ">-->
        <!--                    <table class="layui-table">-->
        <!--                        <tbody>-->
        <!--                        <tr>-->
        <!--                            <th>版权所有</th>-->
        <!--                            <td>xuebingsi(xuebingsi)-->
        <!--                                <a href="http://x.xuebingsi.com/" target="_blank">访问官网</a></td>-->
        <!--                        </tr>-->
        <!--                        <tr>-->
        <!--                            <th>开发者</th>-->
        <!--                            <td>马志斌(113664000@qq.com)</td></tr>-->
        <!--                        </tbody>-->
        <!--                    </table>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <style id="welcome_style"></style>
    </div>
</div>
</body>
<script>

    function startTime()
    {
        var today=new Date();//定义日期对象

        var yyyy = today.getFullYear();//通过日期对象的getFullYear()方法返回年

        var MM = today.getMonth()+1;//通过日期对象的getMonth()方法返回年

        var dd = today.getDate();//通过日期对象的getDate()方法返回年

        var hh=today.getHours();//通过日期对象的getHours方法返回小时

        var mm=today.getMinutes();//通过日期对象的getMinutes方法返回分钟

        var ss=today.getSeconds();//通过日期对象的getSeconds方法返回秒

        // 如果分钟或小时的值小于10，则在其值前加0，比如如果时间是下午3点20分9秒的话，则显示15：20：09

        MM=checkTime(MM);

        dd=checkTime(dd);

        mm=checkTime(mm);

        ss=checkTime(ss);

        var day; //用于保存星期（getDay()方法得到星期编号）

        if(today.getDay()==0)   day   =   "星期日 "

        if(today.getDay()==1)   day   =   "星期一 "

        if(today.getDay()==2)   day   =   "星期二 "

        if(today.getDay()==3)   day   =   "星期三 "

        if(today.getDay()==4)   day   =   "星期四 "

        if(today.getDay()==5)   day   =   "星期五 "

        if(today.getDay()==6)   day   =   "星期六 "

        document.getElementById('nowDateTimeSpan').innerHTML=yyyy+"-"+MM +"-"+ dd +" " + hh+":"+mm+":"+ss+"   " + day;
        setTimeout('startTime()',1000);//每一秒中重新加载startTime()方法
    }

    function checkTime(i)
    {
        if (i<10){

            i="0" + i;

        }
        return i;
    }
</script>

</body>
</html>
