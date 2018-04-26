<?php
$select_guide_services = [];
foreach ($data['guide_service'] as $guide_service){
	$select_guide_services[$guide_service] = trans('userInterface.'.$guide_service);
}
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
    </button>
    <p class="h4 modal-title">行程: {{$data['schedule_id']}}</p>
</div>
<div class="modal-body">
    <p>服務人員： {{$data['guide']['info']['name']}}</p>
    <div class="table-responsive">
    <table id="preOrderTable" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>預訂</th>
                <th>行程名稱</th>
                <th>時間</th>
                <th>服務</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['orders'] as $order)
            <tr data-event-id = "{{$order['id']}}">
                <td>{{ Form::checkbox('check','',true) }}</td>
                <td>{{$order['topic']}}</td>
                <td>{{$order['time_start']}} - {{$order['time_end']}}</td>
                <td>{{ Form::select('user_service', $select_guide_services) }}</td>
            </tr>
             @endforeach
        </tbody>
    </table>
    </div>
</div>
<div class="modal-footer">
    <button class="preOrderTable-submit-btn btn btn-success">進行預訂</button>
    <button class=" btn btn-danger" data-dismiss="modal">取消</button>
</div>
<script>
    (function($){
        $('.preOrderTable-submit-btn').click(function () {
            params = [];
            $preOderTbody = $('#preOrderTable').find('tbody > tr').each(function(){
                if($(this).find('[name="check"]').is(':checked') == true){
                    order = {event_block_id: $(this).data('eventId'), user_service: $(this).find('[name="user_service"]').val()};
                    params.push(order);
                }
            });
            data = {orders: params, servicer_id: {{$data['guide']['id']}}, schedule_id: '{{$data['schedule_id']}}' };
            $.post('/create_guide_ticket_order',data,function (res) {
                if(res.success == true){
                    alert('發送請求成功');
                    $('#ModalForm').modal('hide');
                }else{
                    alert('fail')
                }
            });
        })
    })(jQuery);
</script>