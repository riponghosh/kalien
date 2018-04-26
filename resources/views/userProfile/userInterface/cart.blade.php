@extends('userProfile.userInterface.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('TripActivityPresenter','App\Presenters\TripActivityPresenter')
@section('title')
    @lang('userInterface.Cart')
@endsection
@section('interfaceContent')
    <?php $total_price = 0;?>
    <div class="container">
        <div class="row">
            <div class="card-box user_cart">
                <h4>票券</h4>
                <table class="activity_ticket_table tablesaw table m-b-10 table-striped" id="activity_ticket_table" >
                    <thead>
                    <tr>
                        <th scope="col" >選取</th>
                        <th scope="col"  data-tablesaw-priority="persist">名稱</th>
                        <th scope="col"  data-tablesaw-priority="4">內容</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-sortable-default-col data-tablesaw-priority="3">使用日期(D-M-Y)</th>
                        <th scope="col"  data-tablesaw-priority="4">單價({{Cookie::get('currency_unit')}})</th>
                        <th scope="col"  data-tablesaw-priority="4">數量</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4">移除</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($carts as $cart)
                            <tr class="product_row" data-cart-id="{{$cart['id']}}">
                                <td class="request_check"><input name="request_check" type="checkbox" checked/></td>
                                <td>{{$TripActivityPresenter->lan_convert($cart->trip_activity, 'title')}}-{{$TripActivityPresenter->lan_convert($cart->trip_activity, 'sub_title')}}</td>
                                <td>{{$TripActivityPresenter->lan_convert($cart->trip_activity_ticket, 'name')}}</td>
                                <td>{{$cart->start_date}}</td>
                                <td><span class="tic_price" data-price={{cur_convert($cart->trip_activity_ticket->amount, $cart->trip_activity_ticket->currency_unit)}}>${{cur_convert(number_format($cart->trip_activity_ticket->amount,1,'.',''),$cart->trip_activity_ticket->currency_unit)}}</span></td>
                                <td><span class="tic_qty" data-qty={{$cart->qty}}>{{$cart->qty}}</span></td>
                                <td><i class="delete_request_btn fa fa-trash-o" style="color:#ff5b5b"></i></td>
                            </tr>
                            <?php $total_price = $total_price + $cart->trip_activity_ticket->amount * $cart->qty;?>
                        @endforeach
                    </tbody>
                </table>
                <h4>Pneker</h4>
                <table class="user_services_order_table tablesaw table m-b-10 table-striped" id="user_services_order_table" >
                    <thead>
                    <tr>
                        <th scope="col" >選取</th>
                        <th scope="col"  data-tablesaw-priority="persist">行程名稱</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-sortable-default-col data-tablesaw-priority="3">使用日期(D-M-Y)</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="2">時間</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="1">服務人員</th>
                        <th scope="col"  data-tablesaw-priority="4">服務</th>
                        <th scope="col"  data-tablesaw-priority="4">價格({{Cookie::get('currency_unit')}})</th>
                        <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4">移除</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($guide_service_orders as  $order)
                        <tr class="product_row" data-request-id="{{$order['id']}}">
                            <td class="request_check"><input name="request_check" type="checkbox" checked/></td>
                            <td>{{$order['product_name']}}</td>
                            <td>{{$order['start_date']}}</td>
                            <td>{{$order['start_time']}}-{{$order['end_time']}}</td>
                            <td>{{$order['seller']['name']}}</td>
                            <td>{{trans('UserInterface.'.$UserPresenter->get_user_service_names($order['service_type']))}}</td>
                            <td><span class="tic_price" data-price={{cur_convert(number_format($order['price_by_seller'],1,'.',''),$order['price_unit_by_seller'])}}>${{cur_convert(number_format($order['price_by_seller'],1,'.',''),$order['price_unit_by_seller'])}}</span></td>
                            <td><i class="delete_request_btn fa fa-trash-o" style="color:#ff5b5b"></i></td>
                        </tr>
                        <?php $total_price = $total_price + $order['price_by_seller'];?>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <div class="m-r-15" style="display: inline-block ;vertical-align: bottom">
                        合計： <span id="total_tic_price_unit" class="text-pink" style="font-size: 22px; ">{{Cookie::get('currency_unit')}}$ </span><span id="total_tic_price" class="text-pink" style="font-size: 22px; "></span>
                    </div>
                    <div  style="display: inline-block">
                    <button class="submit_to_pay_btn btn btn-primary waves-effect waves-light">結算</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>

    $user_carts = $('.user_cart');
    $activity_ticket_cart = $('.activity_ticket_table');
    $('.user_cart').find('.submit_to_pay_btn').click(function () {
        params = {user_service: [],activity_ticket: []};
        $user_carts.find('.user_services_order_table').find('.product_row').each(function () {
            if($(this).find('[name="request_check"]').is(':checked')){
                params.user_service.push({product_id: $(this).data('requestId')})
            }
        })
        $user_carts.find('.activity_ticket_table').find('.product_row').each(function () {
            if($(this).find('[name="request_check"]').is(':checked')){
                cart_id = $(this).data('cartId');
                p_qty = $(this).find('.tic_qty').data('qty');
                params.activity_ticket.push({cart_id: cart_id, qty: p_qty})
            }
        })
        data = {cmds: params};
        $.post('/transaction/create_reciept_by_cart',data,function (res) {
            if(res.success == true){
                window.location.href = '{{url('/payment')}}';
            }
        });
    });
    $('#activity_ticket_table').find('.delete_request_btn').click(function(){
        $parent = $(this).closest('tr');
        $id = $parent.data('cartId');

        $.post('/transaction/cart/del_items',{cart_item_id: $id },function (res) {
            if(res.success == true){
                console.log('ok');
                window.location.reload();
            }else{
                toastr['error']('刪除失敗');
                setTimeout(function(){window.location.reload()}, 3000);
            }
        })
    });
    $('#user_services_order_table').find('.delete_request_btn').click(function(){
        $parent = $(this).closest('tr');
        $id = $parent.data('requestId');
        $.post('/user_service_ticket_order/buyer/delete',{request_id: $id },function (res) {
            if(res.success == true){
                window.location.reload();
            }else{
                toastr['error']('刪除失敗');
                setTimeout(function(){window.location.reload()}, 3000);
            }
        })
    });


var Cart = {
    $total_price_field: $('#total_tic_price'),
    calc: function () {
        var $total_price = 0.00;
        $('.activity_ticket_table').find('.product_row').each(function () {
            if($(this).find('[name="request_check"]').is(':checked')){
                $single_price = parseFloat($(this).find('.tic_price').data('price'));
                $qty = $(this).find('.tic_qty').data('qty');
                $total_price = $total_price + $single_price * $qty;
            }
        })
        $('.user_services_order_table').find('.product_row').each(function () {
            if($(this).find('[name="request_check"]').is(':checked')){
                $single_price = parseFloat($(this).find('.tic_price').data('price'));
                $total_price = $total_price + $single_price;
                //User service is no qty
            }
        })
        return $total_price.toFixed(1);

    },
    trigger: function () {
        var self = this;
        $('.activity_ticket_table').find('[name="request_check"]').click(function () {
            self.init();
        })
        $('.user_services_order_table').find('[name="request_check"]').click(function () {
            self.init();
        })
    },
    init: function () {
        this.trigger();
        var total = 0.00;
        total = this.calc;
        this.$total_price_field.text(total);
        this.$total_price_field.data('price',total);
    }
}
$(function() {
    Cart.init();
});
</script>
@endsection

