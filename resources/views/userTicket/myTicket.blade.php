<p class="h2">我的票券</p>
<div class="ticket-container container">
    <div class="row">
        @foreach($user_activity_tickets as $user_activity_ticket)
            {{--顯示未過期資訊 TODO 時區--}}
            @if(strtotime($user_activity_ticket['end_date'].' '.'23:59') >= strtotime(Carbon::now('Asia/Taipei')))
            <div class="col-xs-12 col-sm-6 m-b-15">
                <div class="ticket-box" style="padding:20px; background: #fff" data-ticket-id="{{$user_activity_ticket->ticket_id}}">
                    <div class="main-section">
                        <div class="row">
                            <div class="col-xs-8">
                                <p class="h3 ticketDetail">{{$user_activity_ticket->sub_title}}</p>
                            </div>
                        </div><!--row-->
                        <div class="row">
                            <div class="col-xs-12">
                                <p class="h4 ticketActiName">{{$user_activity_ticket->name}}</p>
                                <p class="h5"><i class="fa fa-calendar"></i> {{$user_activity_ticket->start_date}}</p>
                                <p class="h5"><span>{{$UserPresenter->cur_units($user_activity_ticket->currency_unit,'s')}}</span><span>{{$user_activity_ticket->amount}}</span></p>
                            </div>
                        </div><!--row-->
                        <hr>
                        <div class="row">
                            <div class="col-xs-3">
                                <button class="btn btn-default" data-toggle="collapse" data-target="#collapse-section-{{$user_activity_ticket->ticket_id}}">更多...</button>
                            </div>
                            <div class="col-xs-9 form-group text-right">
                            @if(isset($user_activity_ticket['is_available']['status']))
                                @if($user_activity_ticket['is_available']['status'] == 'unavailable')
                                    @if(in_array('expired', $user_activity_ticket['is_available']['msg']))
                                            <button class="btn btn-default ticket-refund-btn" disabled>已過使用期限</button>
                                    @elseif(in_array('not_achieved', $user_activity_ticket['is_available']['msg']))
                                            <button class="btn btn-default ticket-refund-btn" disabled>未成團暫時無法使用</button>
                                    @elseif(in_array('ticket_is_refunded', $user_activity_ticket['is_available']['msg']))
                                        <button class="btn btn-default ticket-refund-btn" disabled>已辦理退票</button>
                                    @else
                                        <button class="btn btn-default ticket-refund-btn" disabled>無法使用</button>
                                    @endif
                                @else
                                    <button class="btn btn-primary activated-btn">使用</button>
                                @endif
                                    <button class="btn btn-default ticket-refund-btn">退票</button>
                            @endif
                            </div>
                        </div>
                    </div>
                    <div id="collapse-section-{{$user_activity_ticket->ticket_id}}" class="collapse-section collapse">
                        <hr>
                        @if(isset($user_activity_ticket['relate_gp_activity']))
                            <div class="row m-b-10">
                                <div class="col-xs-12">
                                    <a href="{{url('/group_events/'.$user_activity_ticket['relate_gp_activity']['user_gp_activity_id'])}}" class="btn btn-primary" target="_blank">打開相關活動</a>
                                </div>
                            </div>
                        @endif
                        <div class="row m-b-10">
                            <div class="col-xs-12">
                                <button class="btn btn-default retrieve-incidental-coupon-btn">取回附屬券</button>
                            </div>
                        </div>
                    </div>
                </div><!-- End ticket-box-->
            </div>
            @endif
        @endforeach
    </div>
</div>
<script>
    $('.retrieve-incidental-coupon-btn').click(function (e) {
        e.preventDefault();
        var $ticket_id = $(this).closest('.ticket-box').data('ticketId');
        swal({
            title: '取回附屬券？',
            confirmButtonClass: 'btn-success waves-effect waves-light',
            cancelButtonClass: 'btn-default waves-effect waves-light',
            confirmButtonText: '取回!',
            cancelButtonText: '取消',
            showCancelButton: true,
            closeOnConfirm: false,
        },function(){
            $.post('/activity_ticket_incidental_coupon/retrieve_by_self',{ticket_id: $ticket_id},function (res) {
                if(res.success){
                    swal({
                      title: '取回成功',
                      type: "success",
                      confirmButtonClass: 'btn-success'
                    },function () {
                        window.location.reload();
                    })
                }else{
                    swal({
                        title:'取回失敗!',
                        text: res.msg,
                        type: 'error',
                        },function () {
                            window.location.reload();
                        }
                    );
                }
            });

        });
    })
</script>