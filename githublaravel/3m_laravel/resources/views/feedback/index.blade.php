@include('layout.formheader')

<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto" id="LAY-user">

            <div class="demoTable">

                {{--事件--}}

                <div class="layui-input-inline layui-show-xs-block">

                    <input class="layui-input" placeholder="开始日" name="start" id="start"></div>

                <div class="layui-input-inline layui-show-xs-block">

                    <input class="layui-input" placeholder="截止日" name="end" id="end"></div>

                <button class="layui-btn" data-type="reload">搜索</button>

                <button class="layui-btn layui-btn-danger" data-type="getCheckData">批量删除</button>

                <button class="layui-btn" data-type="exportData">导出</button>

            </div>

        </div>


        <div class="layui-card-body">
            <table id="demo" lay-filter="test"></table>

            <script type="text/html" id="opeBar">
                <div class="layui-btn-group">
                    {{--                    <button type="button" class="layui-btn layui-btn-sm" lay-event="edit">--}}
                    {{--                        <i class="layui-icon">&#xe642;</i>--}}
                    {{--                    </button>--}}

                    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">
                        <i class="layui-icon">&#xe640;</i>
                    </button>
                </div>
            </script>
        </div>
    </div>
</div>
</body>

<script>
    layui.use('table', function () {
        var table = layui.table;

        //第一个实例
        table.render({
            elem: '#demo'
            , height: ''
            , url: 'FeedbackList' //数据接口
            , method: 'post'
            , where:{product_name:''}
            , limit: 30
            , page: true //开启分页
            , cols: [[ //表头
                {type: 'checkbox', fixed: 'left',}
                , {field: 'id', title: 'ID', width: 60, sort: true, fixed: 'left'}
                , {field: 'title', title: '标题', width: ''}
                , {field: 'content', title: '反馈内容', width: ''}
                , {field: 'name', title: '姓名', width: ''}
                , {field: 'phone', title: '联系方式', width: '',}
                , {field: 'create_time', title: '反馈时间', width: '', sort: true}
                , {fixed: 'right', width: 120, title: '操作', align: 'center', toolbar: '#opeBar'}
            ]],
            id: 'demo',
        });

        //工具条事件
        table.on('tool(test)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）
            if (layEvent === 'del') { //删除
                layer.confirm('真的删除行么', function (index) {
                    id = obj.data.id;
                    // console.log(id,'2222')
                    // return false
                    //执行删除
                    $.ajax({
                        url: 'FeedbackDelete',
                        data: {feedback_id: id},
                        dataType: 'json',
                        type: 'post',
                        success: function (res) {
                            if (res.code === 200) {
                                layer.msg(res.msg);
                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            } else {
                                layer.msg(res.msg);
                            }
                        }
                    })
                    layer.close(index);
                    //向服务端发送删除指令
                });
            }
        });


        var $ = layui.$, active = {

            reload: function () {
                //执行重载
                table.reload('demo', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , where: {
                        start_time: $('#start').val(),
                        end_time: $('#end').val()
                    }
                });
            },

            getCheckData: function () { //获取选中数据
                var data = table.checkStatus('demo').data;
                var ids = [];
                for (item of data) {
                    ids.push(item.id);
                }
                if (ids.length === 0) {
                    layer.msg('请勾选数据');
                    return false;
                }
                layer.confirm('确认要删除这些数据吗？', function (index) {
                    //捉到所有被选中的，发异步进行删除
                    $.post('FeedbackDelete', {ids: ids}, function (res) {
                        if (res.code === 200) {
                            layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                                table.reload('demo');
                            });
                        } else {
                            layer.msg(res.msg);
                        }
                    });
                });
            },
            //导出
            exportData: function (){
                var start_time = $('#start').val();
                var end_time = $('#end').val();
                window.location = "{{url('/admin/FeedExportData')}}?start_time="+start_time+'&end_time='+end_time;
            }
        };

        $('.demoTable .layui-btn').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

    });


</script>


<script>
    layui.use('laydate',
        function () {
            var laydate = layui.laydate;

            //执行一个laydate实例
            laydate.render({
                elem: '#start' //指定元素
            });

            //执行一个laydate实例
            laydate.render({
                elem: '#end' //指定元素
            });

        });
</script>

</html>
