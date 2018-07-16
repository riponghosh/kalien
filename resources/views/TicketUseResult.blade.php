@extends('layouts.app')

@section('content')
    <div class="container" style="background: #FFF;border: 1px #e3e3e3 solid;border-radius: 3px;padding: 25px 15px 25px 15px;">
        <div class="row">
            <div class="rol-xs-12 text-center">
                @if(!$result['success'] || $ticket['is_available']['status'] == 'unavailable')
                    <img class="error" width="30%" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0OTYuMTU4IDQ5Ni4xNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ5Ni4xNTggNDk2LjE1ODsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxwYXRoIHN0eWxlPSJmaWxsOiNFMDRGNUY7IiBkPSJNNDk2LjE1OCwyNDguMDg1YzAtMTM3LjAyMS0xMTEuMDctMjQ4LjA4Mi0yNDguMDc2LTI0OC4wODJDMTExLjA3LDAuMDAzLDAsMTExLjA2MywwLDI0OC4wODUgIGMwLDEzNy4wMDIsMTExLjA3LDI0OC4wNywyNDguMDgyLDI0OC4wN0MzODUuMDg4LDQ5Ni4xNTUsNDk2LjE1OCwzODUuMDg3LDQ5Ni4xNTgsMjQ4LjA4NXoiLz4KPHBhdGggc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIGQ9Ik0yNzcuMDQyLDI0OC4wODJsNzIuNTI4LTg0LjE5NmM3LjkxLTkuMTgyLDYuODc2LTIzLjA0MS0yLjMxLTMwLjk1MSAgYy05LjE3Mi03LjkwNC0yMy4wMzItNi44NzYtMzAuOTQ3LDIuMzA2bC02OC4yMzYsNzkuMjEybC02OC4yMjktNzkuMjEyYy03LjkxLTkuMTg4LTIxLjc3MS0xMC4yMTYtMzAuOTU0LTIuMzA2ICBjLTkuMTg2LDcuOTEtMTAuMjE0LDIxLjc3LTIuMzA0LDMwLjk1MWw3Mi41MjIsODQuMTk2bC03Mi41MjIsODQuMTkyYy03LjkxLDkuMTgyLTYuODgyLDIzLjA0MSwyLjMwNCwzMC45NTEgIGM0LjE0MywzLjU2OSw5LjI0MSw1LjMxOCwxNC4zMTYsNS4zMThjNi4xNjEsMCwxMi4yOTQtMi41ODYsMTYuNjM4LTcuNjI0bDY4LjIyOS03OS4yMTJsNjguMjM2LDc5LjIxMiAgYzQuMzM4LDUuMDQxLDEwLjQ3LDcuNjI0LDE2LjYzNyw3LjYyNGM1LjA2OSwwLDEwLjE2OC0xLjc0OSwxNC4zMTEtNS4zMThjOS4xODYtNy45MSwxMC4yMi0yMS43NywyLjMxLTMwLjk1MUwyNzcuMDQyLDI0OC4wODJ6Ii8+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                @elseif(strtotime(date('Y-m-d H:i:s')) < strtotime($ticket['use_duration']['from']) || strtotime(date('Y-m-d H:i:s')) > strtotime($ticket['use_duration']['to']))
                    <img class="error" width="30%" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0OTYuMTU4IDQ5Ni4xNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ5Ni4xNTggNDk2LjE1ODsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxwYXRoIHN0eWxlPSJmaWxsOiNFMDRGNUY7IiBkPSJNNDk2LjE1OCwyNDguMDg1YzAtMTM3LjAyMS0xMTEuMDctMjQ4LjA4Mi0yNDguMDc2LTI0OC4wODJDMTExLjA3LDAuMDAzLDAsMTExLjA2MywwLDI0OC4wODUgIGMwLDEzNy4wMDIsMTExLjA3LDI0OC4wNywyNDguMDgyLDI0OC4wN0MzODUuMDg4LDQ5Ni4xNTUsNDk2LjE1OCwzODUuMDg3LDQ5Ni4xNTgsMjQ4LjA4NXoiLz4KPHBhdGggc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIGQ9Ik0yNzcuMDQyLDI0OC4wODJsNzIuNTI4LTg0LjE5NmM3LjkxLTkuMTgyLDYuODc2LTIzLjA0MS0yLjMxLTMwLjk1MSAgYy05LjE3Mi03LjkwNC0yMy4wMzItNi44NzYtMzAuOTQ3LDIuMzA2bC02OC4yMzYsNzkuMjEybC02OC4yMjktNzkuMjEyYy03LjkxLTkuMTg4LTIxLjc3MS0xMC4yMTYtMzAuOTU0LTIuMzA2ICBjLTkuMTg2LDcuOTEtMTAuMjE0LDIxLjc3LTIuMzA0LDMwLjk1MWw3Mi41MjIsODQuMTk2bC03Mi41MjIsODQuMTkyYy03LjkxLDkuMTgyLTYuODgyLDIzLjA0MSwyLjMwNCwzMC45NTEgIGM0LjE0MywzLjU2OSw5LjI0MSw1LjMxOCwxNC4zMTYsNS4zMThjNi4xNjEsMCwxMi4yOTQtMi41ODYsMTYuNjM4LTcuNjI0bDY4LjIyOS03OS4yMTJsNjguMjM2LDc5LjIxMiAgYzQuMzM4LDUuMDQxLDEwLjQ3LDcuNjI0LDE2LjYzNyw3LjYyNGM1LjA2OSwwLDEwLjE2OC0xLjc0OSwxNC4zMTEtNS4zMThjOS4xODYtNy45MSwxMC4yMi0yMS43NywyLjMxLTMwLjk1MUwyNzcuMDQyLDI0OC4wODJ6Ii8+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                @elseif(!empty($ticket['used_at']))
                    <img class="warning" width="30%" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ5Ny40NzIgNDk3LjQ3MiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDk3LjQ3MiA0OTcuNDcyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGcgdHJhbnNmb3JtPSJtYXRyaXgoMS4yNSAwIDAgLTEuMjUgMCA0NSkiPgoJPGc+CgkJPGc+CgkJCTxwYXRoIHN0eWxlPSJmaWxsOiNGRkNDNEQ7IiBkPSJNMjQuMzc0LTM1Ny44NTdjLTIwLjk1OCwwLTMwLjE5NywxNS4yMjMtMjAuNTQ4LDMzLjgyNkwxODEuNDIxLDE3LjkyOCAgICAgYzkuNjQ4LDE4LjYwMywyNS40NjMsMTguNjAzLDM1LjEyMywwTDM5NC4xNC0zMjQuMDMxYzkuNjcxLTE4LjYwMywwLjQyMS0zMy44MjYtMjAuNTQ4LTMzLjgyNkgyNC4zNzR6Ii8+CgkJCTxwYXRoIHN0eWxlPSJmaWxsOiMyMzFGMjA7IiBkPSJNMTczLjYwNS04MC45MjJjMCwxNC44MTQsMTAuOTM0LDIzLjk4NCwyNS4zOTUsMjMuOTg0YzE0LjEyLDAsMjUuNDA3LTkuNTEyLDI1LjQwNy0yMy45ODQgICAgIFYtMjE2Ljc1YzAtMTQuNDYxLTExLjI4Ny0yMy45ODQtMjUuNDA3LTIzLjk4NGMtMTQuNDYxLDAtMjUuMzk1LDkuMTgyLTI1LjM5NSwyMy45ODRWLTgwLjkyMnogTTE3MS40ODktMjg5LjA1NiAgICAgYzAsMTUuMTY3LDEyLjM0NSwyNy41MTEsMjcuNTExLDI3LjUxMWMxNS4xNjcsMCwyNy41MjMtMTIuMzQ1LDI3LjUyMy0yNy41MTFjMC0xNS4xNzgtMTIuMzU2LTI3LjUyMy0yNy41MjMtMjcuNTIzICAgICBDMTgzLjgzNC0zMTYuNTc5LDE3MS40ODktMzA0LjIzNCwxNzEuNDg5LTI4OS4wNTYiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
                @else
                    <img class="success" width="30%" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0OTYuMTU4IDQ5Ni4xNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ5Ni4xNTggNDk2LjE1ODsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxwYXRoIHN0eWxlPSJmaWxsOiMzMkJFQTY7IiBkPSJNNDk2LjE1OCwyNDguMDg1YzAtMTM3LjAyMS0xMTEuMDctMjQ4LjA4Mi0yNDguMDc2LTI0OC4wODJDMTExLjA3LDAuMDAzLDAsMTExLjA2MywwLDI0OC4wODUgIGMwLDEzNy4wMDIsMTExLjA3LDI0OC4wNywyNDguMDgyLDI0OC4wN0MzODUuMDg4LDQ5Ni4xNTUsNDk2LjE1OCwzODUuMDg3LDQ5Ni4xNTgsMjQ4LjA4NXoiLz4KPHBhdGggc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIGQ9Ik0zODQuNjczLDE2NC45NjhjLTUuODQtMTUuMDU5LTE3Ljc0LTEyLjY4Mi0zMC42MzUtMTAuMTI3Yy03LjcwMSwxLjYwNS00MS45NTMsMTEuNjMxLTk2LjE0OCw2OC43NzcgIGMtMjIuNDksMjMuNzE3LTM3LjMyNiw0Mi42MjUtNDcuMDk0LDU3LjA0NWMtNS45NjctNy4zMjYtMTIuODAzLTE1LjE2NC0xOS45ODItMjIuMzQ2Yy0yMi4wNzgtMjIuMDcyLTQ2LjY5OS0zNy4yMy00Ny43MzQtMzcuODY3ICBjLTEwLjMzMi02LjMxNi0yMy44Mi0zLjA2Ni0zMC4xNTQsNy4yNThjLTYuMzI2LDEwLjMyNC0zLjA4NiwyMy44MzQsNy4yMywzMC4xNzRjMC4yMTEsMC4xMzMsMjEuMzU0LDEzLjIwNSwzOS42MTksMzEuNDc1ICBjMTguNjI3LDE4LjYyOSwzNS41MDQsNDMuODIyLDM1LjY3LDQ0LjA2NmM0LjEwOSw2LjE3OCwxMS4wMDgsOS43ODMsMTguMjY2LDkuNzgzYzEuMjQ2LDAsMi41MDQtMC4xMDUsMy43NTYtMC4zMjIgIGM4LjU2Ni0xLjQ4OCwxNS40NDctNy44OTMsMTcuNTQ1LTE2LjMzMmMwLjA1My0wLjIwMyw4Ljc1Ni0yNC4yNTYsNTQuNzMtNzIuNzI3YzM3LjAyOS0zOS4wNTMsNjEuNzIzLTUxLjQ2NSw3MC4yNzktNTQuOTA4ICBjMC4wODItMC4wMTQsMC4xNDEtMC4wMiwwLjI1Mi0wLjA0M2MtMC4wNDEsMC4wMSwwLjI3Ny0wLjEzNywwLjc5My0wLjM2OWMxLjQ2OS0wLjU1MSwyLjI1Ni0wLjc2MiwyLjMwMS0wLjc3MyAgYy0wLjQyMiwwLjEwNS0wLjY0MSwwLjEzMS0wLjY0MSwwLjEzMWwtMC4wMTQtMC4wNzZjMy45NTktMS43MjcsMTEuMzcxLTQuOTE2LDExLjUzMy00Ljk4NCAgQzM4NS40MDUsMTg4LjIxOCwzODkuMDM0LDE3Ni4yMTQsMzg0LjY3MywxNjQuOTY4eiIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                @endif
            </div>
        </div>
        <div class="row m-t-15">
            <div class="rol-xs-12 text-center" style="font-size: 24px;">
                @if($result['success'] == false)
                    無效連結
                @elseif($ticket['is_available']['status'] == 'unavailable')
                    @if(in_array('not_achieved', $ticket['is_available']['msg']))
                        未成團暫時無法使用
                    @elseif(in_array('ticket_is_refunded', $ticket['is_available']['msg']))
                        此票券已辦理退票
                    @else
                        票券無法使用
                    @endif
                @elseif(strtotime(date('Y-m-d H:i:s')) < strtotime($ticket['use_duration']['from']))
                    未到使用日期
                @elseif(strtotime(date('Y-m-d H:i:s')) > strtotime($ticket['use_duration']['to']))
                    已過使用期限
                @else
                    使用成功
                @endif
            </div>
        </div>
        <hr>
        <div class="m-b-25">
        <p class="m-b-5">
            <span style="font-size: 16px; font-weight: bold">{{$ticket['sub_title']}}</span><a href="/group_events/{{$ticket['relate_gp_activity_id']}}">(打開相關活動)</a>
        </p>
        <p>
            <span style="font-size: 14px;">{{$ticket['name']}}</span>
        </p>
        </div>
        <ul>
            <li>
                <p class="m-b-5" style="font-size: 12px;font-weight: 300;">票券編號</p>
                <p>{{$ticket['ticket_number']}}</p>
            </li>
            <li class="m-r-10">
                <p class="m-b-5" style="font-size: 12px;font-weight: 300;">可使用日期</p>
                <p>{{$ticket['start_date']}}</p>
            </li>
            <li class="m-r-10">
                <p class="m-b-5" style="font-size: 12px;font-weight: 300;">使用時間</p>
                <p>{{$ticket['gp_event_start_time']}}</p>
            </li>
        </ul>
    </div>
@endsection