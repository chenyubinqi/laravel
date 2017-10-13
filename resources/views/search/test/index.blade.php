@extends('layout')

@section('content')
    <p></p>
    <form class="form-horizontal">
        <div class="form-group">
            <label for="id" class="col-md-1 control-label">产品ID</label>
            <div class="col-md-2">
                <input type="text" name="id" class="form-control" id="id" value="{{$params['id'] or ''}}"
                       placeholder="23">
            </div>

            <label for="product_parent_category" class="col-md-1 control-label">产品名称</label>
            <div class="col-md-2">
                <input type="text" name="product_name" class="form-control" id="product_name"
                       placeholder="" value="{{$params['product_name'] or ''}}">
            </div>

            <label for="cas_no" class="col-md-1 control-label">CAS号</label>
            <div class="col-md-2">
                <input type="text" name="cas_no" class="form-control" id="cas_no" value="{{$params['cas_no'] or ''}}"
                       placeholder="">
            </div>
        </div>

        <div class="form-group">
            <label for="status" class="col-md-1 control-label">状态</label>
            <div class="col-md-2">
                <input type="text" name="status" class="form-control" id="status" placeholder=""
                       value="{{$params['status'] or ''}}">
            </div>

            <label for="source" class="col-md-1 control-label">来源</label>
            <div class="col-md-2">
                <input type="text" name="source" class="form-control" id="source" placeholder=""
                       value="{{$params['source'] or ''}}">
            </div>

            <label for="product_parent_category" class="col-md-1 control-label">父分类</label>
            <div class="col-md-2">
                <input type="text" name="product_parent_category" class="form-control" id="product_parent_category"
                       placeholder="" value="{{$params['product_parent_category'] or ''}}">
            </div>
        </div>

        <div class="form-group">
            <label for="start_create_time" class="col-md-1 col-xs-3 control-label">创建时间</label>
            <div class="col-md-6 col-xs-10">
                <div class="input-append date datetime-picker col-md-3 col-xs-5" data-date-format="yyyy-mm-dd hh:ii:ss">
                    <input size="16" type="text" name="start_create_time" class="form-control"
                           value="{{$params['start_create_time'] or ''}}" readonly>
                    <span class="add-on"><i class="icon-remove"></i></span>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
                <div class="col-md-1 col-xs-1 text-center">~</div>
                <div class="input-append date datetime-picker col-md-3 col-xs-5" data-date-format="yyyy-mm-dd hh:ii:ss">
                    <input size="16" type="text" name="end_create_time" class="form-control"
                           value="{{$params['end_create_time'] or ''}}" readonly>
                    <span class="add-on"><i class="icon-remove"></i></span>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-1 col-md-1">
                <button type="submit" class="btn btn-default">搜索</button>
            </div>
        </div>


    </form>

    <p></p>
    <table class="table table-hover">
        <thead>
        <tr>
            <th class="info">产品ID</th>
            <th class="info">产品名称</th>
            <th class="info">产品英文名称</th>
            <th class="info">来源</th>
            <th class="info">主分类</th>
            <th class="info">分子式</th>
            <th class="info">状态</th>
            <th class="info">CAS号</th>
            <th class="info">产品编码</th>
            <th class="info">中文别名</th>
            <th class="info">英文别名</th>
            <th class="info">产品分类</th>
            <th class="info">产品父分类</th>
            <th class="info">排序</th>
            <th class="info">创建时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($paginator as $id => $data)
            <tr>
                <td class="active" style="width:5%">{{$id}}</td>
                <td class="active" style="width:5%">{{$data['product_name']}}</td>
                <td class="active" style="width:5%">{{$data['product_name_en']}}</td>
                <td class="active" style="width:5%">{{$data['source']}}</td>
                <td class="active" style="width:5%">{{$data['main_category']}}</td>
                <td class="active" style="width:5%">{{$data['formula']}}</td>
                <td class="active" style="width:5%">{{$data['status']}}</td>
                <td class="active" style="width:5%">{{$data['cas_no']}}</td>
                <td class="active" style="width:5%">{{$data['product_code']}}</td>
                <td class="active" style="width:5%">{{$data['zh_synonyms']}}</td>
                <td class="active" style="width:8%">{{$data['en_synonyms']}}</td>
                <td class="active" style="width:5%">{{ implode(',',$data['product_category'])}}</td>
                <td class="active" style="width:5%">{{ implode(',',$data['product_parent_category'])}}</td>
                <td class="active" style="width:5%">{{$data['sort']}}</td>
                <td class="active" style="width:8%">{{$data['create_time']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $paginator->appends(request()->input())->render() !!}

@stop

@section('script')
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script type="text/javascript">
        $(".datetime-picker").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            todayBtn: true,
            todayHighlight: true,
            clearBtn: true,// 自定义属性,true 显示 清空按钮 false 隐藏 默认:true
        });
    </script>
@endsection
