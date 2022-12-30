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

                    <div class="layui-btn-group demoTable">
                        <button class="layui-btn" data-type="add" onclick="xadmin.open('添加物质','/admin/AddMatter',800,600)"><i class="fa fa-plus"></i> 添加物质</button>
                        <button class="layui-btn layui-btn-danger" data-type="getCheckData">批量删除</button>
                    </div>
                    <table id="demo" lay-filter="test"></table>

                    <script type="text/html" id="opeBar">
                        <div class="layui-btn-group">
                            <button type="button" class="layui-btn layui-btn-sm" lay-event="edit">
                                <i class="layui-icon">&#xe642;</i>
                            </button>

                            <button type="button"  class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">
                                <i class="layui-icon">&#xe640;</i>
                            </button>
                        </div>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script>
    layui.use('table', function(){
        var table = layui.table;

        //第一个实例
        table.render({
            elem: '#demo'
            ,height: ''
            ,url: 'MatterList' //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {type: 'checkbox', fixed: 'left',}
                ,{field: 'id', title: 'ID', width:60, sort: true, fixed: 'left'}
                ,{field: 'chinese_name', title: '中文物质名称', width:''}
                ,{field: 'english_name', title: '英文物质名称', width:'', sort: true}
                ,{field: 'cas', title: 'cas', width:''}
                ,{field: 'mac', title: 'mac', width: ''}
                ,{field: 'twa', title: 'twa', width: '', sort: true}
                ,{field: 'stel', title: '超限倍数', width: '', sort: true}
                ,{field: 'remark', title: '备注', width: ''}
                ,{field: 'recommend', title: '推荐', width: '', sort: true}
                ,{fixed: 'right', width: 120, title: '操作', align: 'center', toolbar: '#opeBar'}
            ]]
        });

        //工具条事件
        table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）

            if(layEvent === 'del'){ //删除
                layer.confirm('真的删除行么', function(index){
                    id = obj.data.id;
                    //执行删除
                    $.ajax({
                        url:'DelMatter',
                        data:{matter_id:id},
                        dataType:'json',
                        type:'post',
                        success:function (res) {
                            if(res.code===200){
                                layer.msg(res.msg);
                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            }else{
                                layer.msg(res.msg);
                            }
                        }
                    })
                    layer.close(index);
                    //向服务端发送删除指令
                });
            } else if(layEvent === 'edit'){ //编辑
                //do something
                xadmin.open('修改物质','EditEMatter?matter_id='+obj.data.id,800,600)
            }
        });

        var $ = layui.$, active = {

            getCheckData: function(obj){ //获取选中数据
                var data = table.checkStatus('demo').data;
                var ids=[];
                for(item of data){
                    ids.push(item.id);
                }
                if(ids.length===0){
                    layer.msg('请勾选数据');
                    return false;
                }
                layer.confirm('确认要删除这些数据吗？',function(index){
                    //捉到所有被选中的，发异步进行删除
                    $.post('delAllMatter',{ids:ids},function(res){
                        if(res.code===200){
                            layer.msg(res.msg, {icon: 1,time:1500},function () {
                                table.reload('demo');
                            });
                        }else{
                            layer.msg(res.msg);
                        }
                    });
                });
            }
        };

        $('.demoTable .layui-btn').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

    });
</script>
</html>
