@include('layout.formheader')
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="product_type" class="layui-form-label">
                    <span class="x-red">*</span>产品名称
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="product_type" value="{{$data['product_type']}}" name="product_type" required="" lay-verify="required"
                           autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label for="buy_link" class="layui-form-label">
                    <span class="x-red">*</span>购买链接
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="buy_link" value="{{$data['buy_link']}}" name="buy_link" required="" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label for="buy_link" class="layui-form-label">
                    <span class="x-red">*</span>规格
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="spec" name="spec" required="" value="{{$data['spec']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label for="buy_link" class="layui-form-label">
                    <span class="x-red">*</span>其他名称
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="othername" name="othername" required="" value="{{$data['othername']}}" lay-verify="required"
                           autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label for="stock" class="layui-form-label">
                    <span class="x-red">*</span>库存
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="stock" value="{{$data['stock']}}" name="stock" required="" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>
                </div>
            </div>

            <input type="hidden" name="product_id" value="{{$data['id']}}">

            <div class="layui-form-item">
                <label class="layui-form-label">产品图片：</label>
                <input name="images" id="thumb_logo" lay-verify="check_cover_pic"  value="{{$data['images']}}" type="hidden" />
                <div class="layui-input-block">
                    <div class="input-group col-sm-2">
                        <button type="button" class="layui-btn" id="cover_pic">
                            <i class="layui-icon">&#xe67c;</i>上传图片
                        </button>
                        <div id="sm_logo" style="margin-top:10px;">
                            <img src="{{$data['images']}}" style="height: 120px;">
                        </div>
                    </div>
                </div>
            </div>



            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>销售状态</label>
                <div class="layui-input-block">

                    <input type="radio" name="status" value="1" lay-skin="primary" title="在售"  @if($data['status']==1) checked="checked" @endif>
                    <input type="radio" name="status" value="2" lay-skin="primary" title="停售"  @if($data['status']==2) checked="checked" @endif>

                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>产品类别</label>
                <div class="layui-input-block">
                    <input type="radio" name="type" value="mask" lay-skin="primary" title="口罩" @if($data['type']=='mask') checked="checked" @endif>
                    <input type="radio" name="type" value="facemask" lay-skin="primary" title="面罩" @if($data['type']=='facemask') checked="checked" @endif>
                    <input type="radio" name="type" value="filterbox" lay-skin="primary" title="滤毒盒" @if($data['type']=='filterbox') checked="checked" @endif>
                    <input type="radio" name="type" value="cottonfilter" lay-skin="primary" title="过滤棉" @if($data['type']=='cottonfilter') checked="checked" @endif>
                    <input type="radio" name="type" value="filtercover" lay-skin="primary" title="过滤棉盖" @if($data['type']=='filtercover') checked="checked" @endif>
                </div>
            </div>


            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    保存
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    layui.use(['form', 'layer'],
        function() {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;



            //监听提交
            form.on('submit(add)',
                function(data) {
                    //发异步，把数据提交给php
                    $.ajax({
                        url: 'EditProduct',
                        type: 'post',
                        data: data.field,
                        success: function (info) {
                            // console.log(info);return
                            if (info.code ===200) {
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

<script>
    $(document).ready(function(){
        // 上传图片
        layui.use('upload', function(){
            var upload = layui.upload;
            var topic_id = $("#topic_id").val()
            //执行实例
            var uploadInst = upload.render({
                elem: '#cover_pic' //绑定元素
                ,url: "/upload/uploads" //上传接口
                ,data: {} //可选项。额外的参数，如：{id: 123, abc: 'xxx'}
                ,done: function(res){
                    console.log(res);
                    //上传完毕回调
                    $("#thumb_logo").val(res.data);
                    $("#sm_logo").html('<img src="' + res.data + '" style="height: 120px;"/>');
                }
                ,error: function(){
                    //请求异常回调
                }
            });
        });
    });
</script>


</body>
</html>
