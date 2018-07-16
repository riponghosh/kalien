@if(BrowserDetect::isMobile())
    @include('mobiles/groupActivity')
@else
    @include('groupActivity/groupActivity')
@endif
