@include('layout.formheader')
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <form class="layui-form">



            <div class="layui-form-item">
                <label for="oldpass" class="layui-form-label">
                    <span class="x-red">*</span>原始密码</label>
                <div class="layui-input-inline">
                    <input type="password" id="old_password" name="old_password" required="" lay-verify="required" autocomplete="off" class="layui-input"></div>
                <div class="layui-form-mid layui-word-aux">6到16个字符</div>
            </div>

            <input type="hidden" name="Uid" value="{{$data['id']}}">

            <div class="layui-form-item">
                <label for="L_pass" class="layui-form-label">
                    <span class="x-red">*</span>新密码</label>
                <div class="layui-input-inline">
                    <input type="password" id="L_pass" name="password" required="" lay-verify="pass" autocomplete="off" class="layui-input"></div>
                <div class="layui-form-mid layui-word-aux">6到16个字符</div>
            </div>


            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                    <span class="x-red">*</span>确认密码</label>
                <div class="layui-input-inline">
                    <input type="password" id="L_repass" name="password_confirmation" required="" lay-verify="repass" autocomplete="off" class="layui-input"></div>
            </div>


            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="add" lay-submit="">保存</button></div>
        </form>
    </div>
</div>

</body>

<script>
    layui.use(['form', 'layer'],
        function() {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;

            //自定义验证规则
            form.verify({

                pass: [/(.+){6,12}$/, '密码必须6到12位'],
                repass: function(value) {
                    if ($('#L_pass').val() != $('#L_repass').val()) {
                        return '两次密码不一致';
                    }
                }
            });
            //监听提交
            form.on('submit(add)',
                function(data) {
                    //发异步，把数据提交给php
                    $.ajax({
                        url: 'SetPassword',
                        type: 'post',
                        data: data.field,
                        success: function (info) {
                            // console.log(info);return
                            if (info.code === 200) {
                                layer.msg(info.msg,{time:1500},function () {
                                    //关闭当前frame
                                    xadmin.close();
                                    // 可以对父窗口进行刷新
                                    xadmin.father_reload();

                                });
                            }else{
                                layer.msg(info.msg);
                            }
                        }
                    })
                    return false;
                });
        });
</script>

</html>
