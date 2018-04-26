@extends('userProfile.userInterface.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')

@section('title')
    @lang('userInterface.BookingRequestSent')
@endsection

@section('interfaceContent')
<div class="container">
    <div class="row">
        @foreach($guide_service_orders as $schedule_id => $orders)
        <div class="card-box">
            <h4 class="m-t-0 header-title">行程編號： {{$schedule_id}}</h4>
            @if($orders->where('guide_status',0)->count() > 0)
            <table class="tablesaw table m-b-15 table-striped" id="mainTable" data-tablesaw-sortable >
                <thead>
                <tr>
                    <th scope="col"  data-tablesaw-priority="persist">行程名稱</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-sortable-default-col data-tablesaw-priority="3">日期(D-M-Y)</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="2">時間</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="1">服務人員</th>
                    <th scope="col"  data-tablesaw-priority="4">服務</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4">編輯</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders->where('guide_status',0) as $order)
                @if($order['guide_status'] == 0)
                <tr data-request-id="{{$order['id']}}">
                    <td>{{$order['product_name']}}</td>
                    <td>{{$order['start_date']}}</td>
                    <td>{{$order['start_time']}}-{{$order['end_time']}}</td>
                    <td>{{$order['seller']['name']}}</td>
                    <td>
                        <?php
						$seller_services = array();
                        foreach($UserPresenter->get_user_service_names($order['seller_services']) as $seller_service){
							$seller_services[$seller_service] = trans('userInterface.'.$seller_service);
                        }
                        ?>
                        {{ Form::select('user_service', $seller_services,$UserPresenter->get_user_service_names($order['service_type']) ,['class' => 'form-control input-sm','style' => 'width: 70%;max-width:120px']) }}
                    </td>
                    <td><i class="delete_request_btn fa fa-trash-o" style="color:#ff5b5b"></i></td>
                </tr>
                @endif
                @endforeach
                </tbody>
            </table>
            @endif
            @if($orders->where('guide_status',2)->count() > 0)
            <table class="tablesaw table m-b-0 table-striped" data-tablesaw-sortable >
                <thead>
                <tr>
                    <th scope="col"  data-tablesaw-priority="persist">行程名稱</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-sortable-default-col data-tablesaw-priority="3">日期(D-M-Y)</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="2">時間</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="1">服務人員</th>
                    <th scope="col"  data-tablesaw-priority="4">服務</th>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4">狀態</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders->where('guide_status',2) as $order)
                        <td>{{$order['product_name']}}</td>
                        <td>{{$order['start_date']}}</td>
                        <td>{{$order['start_time']}}-{{$order['end_time']}}</td>
                        <td>{{$order['seller']['name']}}</td>
                        <td>
                            @lang('userInterface.'.$UserPresenter->get_user_service_names($order['service_type']))
                        </td>
                        <td><label class="label label-danger">被拒絕</label></td>
                @endforeach
                </tbody>
            </table>
                @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
@section('script')
<script>
$('[name="user_service"]').change(function(){
    $parent = $(this).closest('tr');
    $.post('/user_service_ticket_order/buyer/update',{request_id: $parent.data('requestId'), user_service: $(this).val()},function (res) {
        if(res.success == true){
            toastr['success']('更改成功');

        }else{
            toastr['error']('更改失敗');
            setTimeout(function(){window.location.reload()}, 2000);

        }
    });
})
$('.delete_request_btn').click(function(){
    $parent = $(this).closest('tr');
    $id = $parent.data('requestId');
    $.post('/user_service_ticket_order/buyer/delete',{request_id: $id },function (res) {
        if(res.success == true){
            window.location.reload();
        }else{
            toastr['error']('刪除失敗');
            //setTimeout(function(){window.location.reload()}, 3000);
        }
    })
})
</script>
@endsection