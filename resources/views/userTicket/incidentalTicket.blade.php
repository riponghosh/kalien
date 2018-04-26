@inject('UserPresenter','App\Presenters\UserPresenter')
<p class="h2">折價券</p>
<div class="incidental-ticket-container container">
    <div class="row">
        <?php $beneficiary_incidental_tickets_group_by_start_date = object_group_by($beneficiary_incidental_tickets, 'User_activity_ticket.start_date')?>
        @foreach($beneficiary_incidental_tickets_group_by_start_date as $start_date => $beneficiary_incidental_ticket_group_by_start_date)
            <?php $beneficiary_incidental_tickets_group_by_trip_activity = object_group_by($beneficiary_incidental_ticket_group_by_start_date, 'User_activity_ticket.Trip_activity_ticket.Trip_activity.id')?>
            @foreach($beneficiary_incidental_tickets_group_by_trip_activity as $key => $beneficiary_incidental_tickets)
                <?php
                    $trip_activity_info = $beneficiary_incidental_tickets[0]['User_activity_ticket']['Trip_activity_ticket']['Trip_activity'];
                    $now = Carbon::now($trip_activity_info['time_zone']);
                ?>
                    <div class="col-xs-12 col-sm-6 m-b-15">
                        <div class="incidental-ticket-box" style="padding:20px; background: #fff" >
                            <div class="row">
                                <div class="col-xs-12">
                                    <p class="h4 ticketActiName">{{$TripActivityPresenter->lan_convert($trip_activity_info, 'title')}}</p>
                                    <p class="h5"><i class="fa fa-calendar"></i> {{$beneficiary_incidental_tickets[0]['User_activity_ticket']['start_date']}}</p>
                                    <?php
                                        $trip_activity_coupon_price = 0;
                                        foreach ($beneficiary_incidental_tickets as $beneficiary_incidental_ticket){
                                            $trip_activity_coupon_price += cur_convert($beneficiary_incidental_ticket['amount'],$beneficiary_incidental_ticket['amount_unit']);
                                        }
                                    ?>
                                    <p class="h5"><span>總值</span><span>{{$trip_activity_coupon_price}}</span></p>
                                </div>
                            </div><!--row-->
                            <hr>
                            <form class="incidental-ticket-expand-section">
                                <div class="row incidental-ticket-list">
                                    <div class="col-xs-12">
                                        @foreach($beneficiary_incidental_tickets as $beneficiary_incidental_ticket)
                                        <div class="row">
                                            <div class="col-xs-12 checkbox">
                                                <label class="control-label"><input name="incidental_coupon_ids[]" type="checkbox" value="{{$beneficiary_incidental_ticket['id']}}" checked/>{{$UserPresenter->cur_units(Cookie::get('currency_unit'), 's')}}{{cur_convert($beneficiary_incidental_ticket['amount'],$beneficiary_incidental_ticket['amount_unit'])}}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                        <div class="row">
                                            <div class="col-xs-12 text-center">
                                                @if(strtotime($beneficiary_incidental_tickets[0]['User_activity_ticket']['start_date']) > strtotime($now))
                                                    <button class="use-incidental-tickets-btn btn btn-warning" style="width: 80%;" disabled>未到使用日期</button>
                                                @elseif(strtotime($beneficiary_incidental_tickets[0]['User_activity_ticket']['start_date'].' '.'23:59') <  strtotime($now))
                                                    <button class="use-incidental-tickets-btn btn btn-danger" style="width: 80%;" disabled>已過期</button>
                                                @else
                                                    <button class="use-incidental-tickets-btn btn btn-success" style="width: 80%;">使用</button>
                                                @endif
                                            </div>
                                        </div><!--Submit Btn Row-->
                                    </div>
                                </div>
                            </form><!--End incidental-ticket-expand-section-->
                        </div>
                    </div>
            @endforeach
        @endforeach
    </div>
    <p class="h2">折價券3天前使用紀錄</p>
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>活動名稱</th>
                        <th>價值</th>
                        <th>使用日期</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    @foreach($beneficiary_incidental_tickets_is_used as $beneficiary_incidental_ticket_is_used)
                        <?php $trip_activity_info = $beneficiary_incidental_ticket_is_used['User_activity_ticket']['Trip_activity_ticket']['Trip_activity']?>
                        <tr>
                            <td>{{$TripActivityPresenter->lan_convert($trip_activity_info, 'title')}}</td>
                            <td>{{$UserPresenter->cur_units($beneficiary_incidental_ticket_is_used['amount_unit'], 's')}}{{$beneficiary_incidental_ticket_is_used['amount']}}</td>
                            <td>{{Carbon::createFromFormat('Y-m-d H:i:s', $beneficiary_incidental_ticket_is_used['used_at'] )->timezone($trip_activity_info['time_zone'])}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('.use-incidental-tickets-btn').click(function (e) {
        e.preventDefault();
        $data = $(this).closest('.incidental-ticket-expand-section').serialize();
        $.ajax({
            url: '/activity_ticket_incidental_coupon/use',
            type: 'POST',
            data: $data,
            success: function (res) {
                if(res.success){
                    swal({
                        title:'使用成功!',
                        text: res.total_amount_unit+' '+res.total_amount,
                        type: "success",
                        confirmButtonClass: 'btn-success',
                        },function () {
                            window.location.reload();
                        }
                    );
                }else{
                    swal({
                            title:'使用失敗!'
                        },function () {
                            window.location.reload();
                        }
                    );
                }
            }
        })
    })
</script>