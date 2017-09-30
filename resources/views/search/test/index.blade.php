@extends('layout')

@section('content')
    <p></p>
    <form class="form-inline">
        <div class="form-group">
            <label for="exampleInputName2">产品ID</label>
            <input type="text" name="id" class="form-control" id="id" placeholder="23">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">CAS号</label>
            <input type="text" name="cas_no" class="form-control" id="cas_no" placeholder="">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">父分类</label>
            <input type="text" name="product_parent_category" class="form-control" id="product_parent_category" placeholder="">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">状态</label>
            <input type="text" name="status" class="form-control" id="status" placeholder="">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">来源</label>
            <input type="text" name="source" class="form-control" id="source" placeholder="">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">创建开始时间</label>
            <div class="input-append date form_datetime">
                <input size="16" type="text" value="" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">创建结束时间</label>
            <div class="input-append date form_datetime">
                <input size="16" type="text" value="" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
        </div>
        <button type="submit" class="btn btn-default">搜索</button>
    </form>

    <p></p>
    <table class="table table-hover">
        <tr>
            <td class="active">...</td>
            <td class="success">...</td>
            <td class="warning">...</td>
            <td class="danger">...</td>
            <td class="info">...</td>
        </tr>
    </table>
    <script type="text/javascript">
        $(".form_datetime").datetimepicker({
            format: "dd MM yyyy - hh:ii",
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-left"
        });
    </script>
@stop