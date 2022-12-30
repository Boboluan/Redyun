@include('layout.formheader')
<style>
    .layui-form-label{
        margin:0 0 30px 0;
    }

    .item-label{
        margin-bottom:80px !important;
    }

</style>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="chinese_name" class="layui-form-label">
                    <span class="x-red">*</span>中文物质名称
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="product_type" value="{{$data['MatterInfo']['chinese_name']}}" name="chinese_name" required="" lay-verify="required"
                           autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>
                </div>
            </div>


            <div class="layui-form-item">
                <label for="english_name" class="layui-form-label">
                    <span class="x-red"></span>英文物质名称
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="buy_link" name="english_name" value="{{$data['MatterInfo']['english_name']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>
            </div>



            <div class="layui-form-item">
                <label for="cas" class="layui-form-label">
                    <span class="x-red"></span>CAS
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="stock" name="cas" value="{{$data['MatterInfo']['cas']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label for="mac" class="layui-form-label">
                    <span class="x-red"></span>mac
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="mac" name="mac" value="{{$data['MatterInfo']['mac']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>

            </div>


            <div class="layui-form-item">
                <label for="twa" class="layui-form-label">
                    <span class="x-red"></span>twa
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="twa" name="twa" value="{{$data['MatterInfo']['twa']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>

            </div>


            <div class="layui-form-item">
                <label for="stel" class="layui-form-label">
                    <span class="x-red"></span>stel
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="stel" name="stel" value="{{$data['MatterInfo']['stel']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>

            </div>


            <div class="layui-form-item">
                <label for="remark" class="layui-form-label">
                    <span class="x-red"></span>备注
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="remark" name="remark" value="{{$data['MatterInfo']['remark']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>

            </div>


            <div class="layui-form-item">
                <label for="recommend" class="layui-form-label">
                    <span class="x-red"></span>推荐
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="recommend" name="recommend" value="{{$data['MatterInfo']['recommend']}}" lay-verify=""
                           autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>状态</label>
                <div class="layui-input-block">
                    <input type="radio" name="status" value="1"  lay-filter="status"  title="显示"  @if($data['MatterInfo']['status']==1) checked @endif>
                    <input type="radio" name="status" value="0"  lay-filter="status"  title="隐藏"  @if($data['MatterInfo']['status']==0) checked @endif>
                </div>
            </div>



            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>产品类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="is_mask" value="true"  lay-filter="is_mask"  title="口罩"  @if(!empty($data['Mask'])) checked @endif>
                    <input type="radio" name="is_mask" value="false" lay-filter="is_mask"  title="面罩"  @if(empty($data['Mask'])) checked @endif>
                </div>
            </div>


            <input type="hidden" name="matter_id" value="{{$data['MatterInfo']['id']}}">

            {{--选择口罩--}}
            <div class="layui-form-item" id="maskbox">
                <label for="recommend" class="layui-form-label">
                    <span class="x-red"></span>口罩选择
                </label>
                <div class="layui-input-inline" style="width: 80%">
                    <select name="masktype_one" xm-select="selectId1">
                        <option value="" disabled="disabled">请选择口罩</option>
                        @foreach($choose['mask'] as $vo)
                                <option value="{{$vo['id']}}" @if(in_array($vo['id'],$data['Mask'])) selected @endif >{{$vo['product_type']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            {{--容器--}}
            <div id="vessel" style="display: none" >

                <blockquote class="layui-elem-quote">
                    型号一
                </blockquote>
                {{--选择面罩型号1--}}
                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>面罩类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="facemasktype_one" xm-select="selectId2">
                            <option value="" disabled="disabled">请选择面罩</option>
                            @foreach($choose['facemask'] as $voa)
                                <option value="{{$voa['id']}}" @if(in_array($voa['id'],$data['faceMask_one'])) selected @endif >{{$voa['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>




                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤毒盒类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="filterbox_one" xm-select="selectId3">
                            <option value="" disabled="disabled">请选择滤毒盒</option>
                            @foreach($choose['filterbox'] as $vob)
                                <option value="{{$vob['id']}}" @if(in_array($vob['id'],$data['filterBox_one'])) selected @endif >{{$vob['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤棉盖类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="filtercover_one" xm-select="selectId4">
                            <option value="" disabled="disabled">请选择滤棉盖</option>
                            @foreach($choose['filtercover'] as $voc)
                                <option value="{{$voc['id']}}" @if(in_array($voc['id'],$data['filterCover_one'])) selected @endif>{{$voc['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>





                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤棉类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="cottonfilter_one" xm-select="selectId5">
                            <option value="" disabled="disabled">请选择过滤棉</option>
                            @foreach($choose['cottonfilter'] as $vod)
                                <option value="{{$vod['id']}}" @if(in_array($vod['id'],$data['cottonFilter_one'])) selected @endif>{{$vod['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                {{---------------------------------第二组--------------------------------------------}}

                <blockquote class="layui-elem-quote">
                    型号二
                </blockquote>

                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>面罩类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="facemasktype_two" xm-select="selectId6">
                            <option value="" disabled="disabled">请选择面罩</option>
                            @foreach($choose['facemask'] as $voe)
                                <option value="{{$voe['id']}}" @if(in_array($voe['id'],$data['faceMask_two'])) selected @endif>{{$voe['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤毒盒类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="filterbox_two" xm-select="selectId7">
                            <option value="" disabled="disabled">请选择滤毒盒</option>
                            @foreach($choose['filterbox'] as $vof)
                                <option value="{{$vof['id']}}" @if(in_array($vof['id'],$data['filterBox_two'])) selected @endif>{{$vof['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤棉盖类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="filtercover_two" xm-select="selectId8">
                            <option value="" disabled="disabled">请选择滤棉盖</option>
                            @foreach($choose['filtercover'] as $vog)
                                <option value="{{$vog['id']}}" @if(in_array($vog['id'],$data['filterCover_two'])) selected @endif>{{$vog['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>




                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤棉类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="cottonfilter_two" xm-select="selectId9">
                            <option value="" disabled="disabled">请选择过滤棉</option>
                            @foreach($choose['cottonfilter'] as $voh)
                                <option value="{{$voh['id']}}" @if(in_array($voh['id'],$data['cottonFilter_two'])) selected @endif >{{$voh['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <blockquote class="layui-elem-quote">
                    型号三
                </blockquote>
                {{-- -------------------------------------第三组---------------------------------------------}}


                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>面罩类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="facemasktype_three" xm-select="selectId10">
                            <option value="" disabled="disabled">请选择面罩</option>
                            @foreach($choose['facemask'] as $voi)
                                <option value="{{$voi['id']}}" @if(in_array($voi['id'],$data['faceMask_three'])) selected @endif >{{$voi['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>




                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤毒盒类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="filterbox_three" xm-select="selectId11">
                            <option value="" disabled="disabled">请选择滤毒盒</option>
                            @foreach($choose['filterbox'] as $voj)
                                <option value="{{$voj['id']}}" @if(in_array($voj['id'],$data['filterBox_three'])) selected @endif>{{$voj['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤棉盖类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="filtercover_three" xm-select="selectId12">
                            <option value="" disabled="disabled">请选择滤棉盖</option>
                            @foreach($choose['filtercover'] as $vok)
                                <option value="{{$vok['id']}}" @if(in_array($vok['id'],$data['filterCover_three'])) selected @endif >{{$vok['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>




                <div class="layui-form-item item-label">
                    <label for="recommend" class="layui-form-label">
                        <span class="x-red"></span>滤棉类型
                    </label>
                    <div class="layui-input-inline" style="width: 80%">
                        <select name="cottonfilter_three" xm-select="selectId13">
                            <option value="" disabled="disabled">请选择过滤棉</option>
                            @foreach($choose['cottonfilter'] as $vol)
                                <option value="{{$vol['id']}}" @if(in_array($vol['id'],$data['cottonFilter_three'])) selected @endif  >{{$vol['product_type']}}</option>
                            @endforeach
                        </select>
                    </div>
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



<link rel="stylesheet" type="text/css" href="/admin/lib/dist/formSelects-v4.css"/>
<script src="/admin/lib/dist/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>

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
                        url: 'EditEMatter',
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


            form.on('radio(is_mask)', function(data){
                // alert(data.value); //判断单选框的选中值
                //此处判断
                if(data.value==='true'){
                    $('#vessel').hide();
                    $('#maskbox').show();
                }else{
                    $('#maskbox').hide();
                    $('#vessel').show();
                }
            });


        });
</script>

<script>
    $(function () {
      var is_mask =  $('input[name="is_mask"]:checked').val();
      if(is_mask==='false'){
          $('#maskbox').hide();
          $('#vessel').show();
      }else{
          $('#maskbox').show();
          $('#vessel').hide();
      }
    })
</script>
</body>
</html>
