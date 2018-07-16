@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('TripActivityPresenter','App\Presenters\TripActivityPresenter')
@extends('layouts.app')
@section('content')
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#my_ticket">我的票券</a></li>
        <li><a data-toggle="tab" href="#incidental_ticket">折價券</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="my_ticket">
            @include('userTicket.myTicket')
        </div>
        <div class="tab-pane fade" id="incidental_ticket">
            @include('userTicket.incidentalTicket')
        </div>
    </div>
@endsection

@section('script')
<script>
    userTicketUsing = function($activateBtn, $ticketActiName,$ticketId,$ticketDetail) {
        //$ticketId = $obj.data('ticketId');
        //$ticketDetail = $obj.find('.ticketDetail').text();
        $activateBtn.click(function () {
            swal({
                    title: $ticketActiName+'票券',
                    text: '內容：'+$ticketDetail,
                    confirmButtonClass: 'btn-success waves-effect waves-light',
                    cancelButtonClass: 'btn-default waves-effect waves-light',
                    confirmButtonText: '使用 !',
                    cancelButtonText: '取消',
                    showCancelButton: true,
                    closeOnConfirm: false,
                },function(){
                    $.post('/api-web/v1/user_activity_ticket/use',{ticket_id: $ticketId}, function (res) {
                        if (res.success) {
                            swal({
                                type: 'success',
                                title: res.data.activity_name,
                                text: res.data.detail+"\n" + res.data.use_date,
                                confirmButtonText: '使用成功'
                            });
                        } else if(!res.success){
                            if(res.code == 1){
                                $title = '尚未到使用日期。';
                                $swalType = 'warning';
                            }else if(res.code == 2){
                                $title = '此票券已過期。';
                                $swalType = 'error';
                            }else if(res.code == 3){
                                $title = '查無此票券。';
                                $swalType = 'error';
                            }else if(res.code == 4){
                                $title = '票券已失效。';
                                $swalType = 'error';
                            }
                            swal({
                                type: $swalType,
                                title: $title,
                                confirmButtonText: '返回'
                            });
                        }else{
                            swal("發生錯誤", ".", "error");
                        }
                    })

                }

            );
        })
    }

    userTicketRefund = function ($refundBtn, $ticketActiName,$ticketId,$ticketDetail) {
        $refundBtn.preventDoubleClick();
        $refundBtn.click(function () {
            swal({
                type: 'warning',
                title: '辦理退票',
                text: $ticketActiName +'：' + $ticketDetail,
                confirmButtonClass: 'btn-danger waves-effect waves-light',
                cancelButtonClass: 'btn-default waves-effect waves-light',
                confirmButtonText: '確認退票 !',
                cancelButtonText: '取消',
                showCancelButton: true,
                closeOnConfirm: true,
            }, function () {
                $.post('/api-web/v1/user_activity_ticket/refund',{ticket_id: $ticketId}, function (res) {
                    if (res.success) {
                        swal({
                            type: 'success',
                            title: '退票成功',
                            confirmButtonText: '確認'
                        },function () {
                            window.location.reload();
                        });
                    } else if(!res.success){
                        swal({
                            type: 'error',
                            title: '退票失敗',
                            text: res.msg,
                            confirmButtonClass: 'btn-primary waves-effect waves-light',
                            cancelButtonClass: 'btn-default waves-effect waves-light',
                            confirmButtonText: '知道',
                            cancelButtonText: '取消',
                            showCancelButton: true,
                            closeOnConfirm: false,
                        },function () {
                            window.location.reload();
                        });
                    }else{
                        swal("發生錯誤，請聯絡客服", ".", "error");
                    }
                });
            })
        });
    }

    $('.ticket-container').find('.ticket-box').each(function () {
        userTicketUsing($(this).find('.activated-btn'),$(this).find('.ticketActiName').text(),$(this).data('ticketId'),$(this).find('.ticketDetail').text());
        userTicketRefund($(this).find('.ticket-refund-btn'), $(this).find('.ticketActiName').text(), $(this).data('ticketId'), $(this).find('.ticketDetail').text());

    })
</script>
@endsection