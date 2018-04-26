@extends('userProfile.userInterface.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')

@section('title')
    @lang('userInterface.UserOrderRequest')
@endsection

@section('interfaceContent')
    <div class="container">
        <div class="row">
            @foreach($guide_service_orders as $schedule_id => $orders)
            <div class="card-box">
                <h4 class="m-t-0 header-title">行程編號： {{$schedule_id}}</h4>
                <table class="tablesaw table m-b-0" data-tablesaw-sortable>
                    <thead>
                    <tr>
                        <th scope="col"  data-tablesaw-sortable-col data-tablesaw-priority="persist">狀態</th>
                        <th scope="col"  data-tablesaw-priority="persist">行程名稱</th>
                        <th scope="col"  data-tablesaw-priority="persist">用戶</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-sortable-default-col data-tablesaw-priority="3">日期(Y-M-D)</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="2">時間</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="1">時數</th>
                        <th scope="col" data-tablesaw-priority="4">服務</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4">定價 <span style="color: #ff8acc">{{$currency_unit}}</span> </th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4">編輯</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                    <?php
                        $start = explode(":", $order['start_time']);$end = explode(":", $order['end_time']);
                        $diff_H = $end[0] - $start[0]; $diff_M = $end[1] - $start[1];
                        if($diff_M < 0){$diff_M = abs($diff_M); $diff_H--; }
					    $diff_H = $diff_H == 0 ?  '' : $diff_H.'H';
					    $diff_M = $diff_M == 0 ?  '' : $diff_M.'M';
                    ?>
                    <tr data-request-id="{{$order['id']}}">
                        <td>
                            @if($order['guide_status'] == 0)
                                <span class="label label-pink">未處理</span>
                            @elseif($order['guide_status'] == 1)
                                <span class="label label-warning">等待付款</span>
                            @endif
                        </td>
                        <td>{{$order['product_name']}}</td>
                        <td>{{$order['tourist']['name']}}</td>
                        <td>{{$order['start_date']}}</td>
                        <td>{{$order['start_time']}}-{{$order['end_time']}}</td>
                        <td>{{$diff_H}}{{$diff_M}}</td>
                        <td>{{trans('userInterface.'.$UserPresenter->get_user_service_names($order['service_type']))}}</td>
                        <td>
                            @if($order['guide_status'] == 0)
                                <input name="seller_set_price" class="form-control input-sm" value="{{cur_convert($order['price_by_seller'],$order['price_unit_by_seller'])}}" style="margin-left: -5px;margin-top: -3px; width: 70%;max-width: 90px;" placeholder="$0.00"/></td>
                            @elseif($order['guide_status'] == 1)
                                $ {{cur_convert($order['price_by_seller'],$order['price_unit_by_seller'])}}
                            @endif
                        <td>
                            @if($order['guide_status'] == 0)
                            <i class="accept_request_btn fa fa-check m-r-10" aria-hidden="true" style="color:#10c469"></i>
                            @elseif($order['guide_status'] == 1)
                            <i class="edit_request_btn fa fa-pencil m-r-10" aria-hidden="true" style="color:#10c469"></i>
                            @endif
                            <i class="delete_request_btn fa fa-trash-o" style="color:#ff5b5b"></i>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
@endsection
@section('script')
<script>
$('[name="seller_set_price"]').change(function(){
    $.post(
        '/user_service_ticket_order/seller/set_price',
        {price: $(this).val(), request_id: $(this).closest('tr').data('requestId'), price_unit: '{{$currency_unit}}'}
    );
})
$('.accept_request_btn').click(function () {
    $parent = $(this).closest('tr');
    $id = $parent.data('requestId');
    $price_input = $parent.find('[name="seller_set_price"]');
    if($price_input.val() == undefined || $price_input.val() == ''){
        toastr['warning']('請先設定價格');
        return;
    }

    $.post('/user_service_ticket_order/seller/order_response',{req_response: 'accept',request_id: $id },function (res) {
        if(res.success == true){
            window.location.reload();
        }else{
            alert('失敗');
        }
    })

})
$('.edit_request_btn').click(function () {
    $parent = $(this).closest('tr');
    $id = $parent.data('requestId');
    $.post('/user_service_ticket_order/seller/order_response',{req_response: 'pending',request_id: $id },function (res) {
        if(res.success == true){
            window.location.reload();
        }else{
            toastr['error']('失敗');
        }
    })
})
$('.delete_request_btn').click(function () {
    $parent = $(this).closest('tr');
    $id = $parent.data('requestId');
    $.post('/user_service_ticket_order/seller/order_response',{req_response: 'reject',request_id: $id },function (res) {
        if(res.success == true){
            window.location.reload();
        }else{
            toastr['error']('刪除失敗');
        }
    })
})
</script>
@endsection