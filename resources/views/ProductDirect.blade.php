@if(BrowserDetect::isMobile())
    @include('mobiles.product')
@else
    @include('tripActivity/tripActivity')
@endif
