@extends('layouts.app')

@section('content')
<form action="/merchant/commit_merchant_act_code" method="POST">
<div class="panel panel-default" style="max-width:400px;position: absolute;margin: auto;left:0; right: 0;">
    <div class="panel-heading">
        輸入店家代碼
    </div>
    <div class="panel-body">
            {{csrf_field()}}
            <div class="form-group">
            <label class="control-label" for="merchant_act_code">驗證碼</label>
            <input id="merchant_act_code" class="form-control" name="merchant_act_code">
                @if($errors->any())
                    <h4 class="text-danger">{{$errors->first()}}</h4>
                @endif
            </div>
    </div>
    <div class="panel-footer text-right">
        <button class="btn btn-primary">提交</button>
    </div>
</div>
</form>
@endsection