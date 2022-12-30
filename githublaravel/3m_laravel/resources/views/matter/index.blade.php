@include('layout.formheader')
<body>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto" id="LAY-user">
            <div class="demoTable">
                <div class="layui-inline">
                    <input class="layui-input" placeholder="搜索物质名称" name="id" id="demoReload" autocomplete="off">
                </div>
                <button class="layui-btn" data-type="reload">搜索</button>
                <button class="layui-btn" data-type="add" onclick="xadmin.open('添加物质','/admin/AddMatter',800,600)"><i
                        class="fa fa-plus"></i> 添加物质
                </button>
                <button class="layui-btn layui-btn-danger" data-type="getCheckData">批量删除</button>
            </div>
        </div>

        <div class="layui-card-body">

            <table id="demo" lay-filter="test"></table>

            <script type="text/html" id="opeBar">
                <div class="layui-btn-group">
                    <button type="button" class="layui-btn layui-btn-sm" lay-event="edit">
                        <i class="layui-icon">&#xe642;</i>
                    </button>

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
            , url: 'MatterList' //数据接口
            , where: {matter_name:''}
            , page: true //开启分页
            , limit: 50
            , cols: [[ //表头
                {type: 'checkbox', fixed: 'left',}
                , {field: 'id', title: 'ID', width: 60, sort: true, fixed: 'left'}
                , {field: 'chinese_name', title: '中文物质名称', width: ''}
                , {field: 'english_name', title: '英文物质名称', width: '', sort: true}
                , {field: 'cas', title: 'cas', width: ''}
                , {field: 'mac', title: 'mac', width: ''}
                , {field: 'twa', title: 'twa', width: '', sort: true}
                , {field: 'stel', title: '超限倍数', width: '', sort: true}
                , {field: 'status', title: '状态', width: '',}
                , {field: 'remark', title: '备注', width: ''}
                , {field: 'recommend', title: '推荐', width: '', sort: true}
                , {fixed: 'right', width: 120, title: '操作', align: 'center', toolbar: '#opeBar'}
            ]]
        });

        //工具条事件
        table.on('tool(test)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）

            if (layEvent === 'del') { //删除
                layer.confirm('真的删除行么', function (index) {
                    id = obj.data.id;
                    //执行删除
                    $.ajax({
                        url: 'DelMatter',
                        data: {matter_id: id},
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
            } else if (layEvent === 'edit') { //编辑
                //do something
                xadmin.open('修改物质', 'EditEMatter?matter_id=' + obj.data.id, 800, 600)
            }
        });

        var $ = layui.$, active = {
            reload: function () {
                var demoReload = $('#demoReload');
                //执行重载
                table.reload('demo', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , where: {
                        matter_name: demoReload.val()
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
                    $.post('delAllMatter', {ids: ids}, function (res) {
                        if (res.code === 200) {
                            layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                                table.reload('demo');
                            });
                        } else {
                            layer.msg(res.msg);
                        }
                    });
                });
            }
        };


        $('.demoTable .layui-btn').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });
    });
</script>
</html>
