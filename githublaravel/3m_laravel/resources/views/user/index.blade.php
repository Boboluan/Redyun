@include('layout.formheader')
<body>
<div class="x-nav">
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">
        <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>
    </a>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">


                <div class="layui-card-body ">

                    <table class="layui-table layui-form">
                        <thead>
                        <tr>
                            <th>管理員账户</th>
                            <th>添加时间</th>
                            <th>操作</th></tr>
                        </thead>

                        <tbody>
                        @foreach($data as $vo)
                            <tr>
                            <tr>
                                <td>{{$vo['username']}}</td>
                                <td>{{$vo['create_time']}}</td>
                                <td class="td-manage">
                                    <button type="button" onclick="xadmin.open('编辑','UserEdit?uid={{$vo['id']}}')" class="layui-btn"><i class="layui-icon"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
