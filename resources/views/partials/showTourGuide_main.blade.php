@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
<div class="grid">
<h4>
    Tour Guide
</h4>
        @if (count($searchResult) > 0)
        @foreach ($searchResult as $guide)
            @if(count($guide->trip) > 0)
                @include('userProfile.userCard')
            @endif
        @endforeach
        @endif
</div>
<script>
if ($.fn.masonry){
  $('.grid').masonry({
    // options...
    itemSelector: '.grid-item',
    columnWidth: 295
  });
}
/*
** modal
*/
$('body').on('hidden.bs.modal', '.modal', function () {
  $(this).removeData('bs.modal');
});
/*show guide modal*/
$('.open-modal.showGuideProfile').on('click',function(e){
    openUserProfileModal( $(this).data('userName'));
})
</script>
<script>
$(document).ready(function(){
    $.getScript( "https://www.youtube.com/iframe_api", function(){
        $('.cardVideo').each(function(){
            var $playerId = $(this).attr('id'),
                $videoId = getYoutubeId($(this).data('video-url'));
            new YT.Player($playerId, {
                playerVars: {autoplay: 1, controls: 0,autohide:1,wmode:'opaque', loop:1, iv_load_policy: 3,showinfo : 0},
                videoId: $videoId,
                loop : 1,
                events: {
                    'onReady': onPlayerReady,
                    onStateChange: function(e){
                        if (e.data === YT.PlayerState.ENDED) {
                            e.target.playVideo();
                        }
                    }
                }
            });
        })
    });
})



</script>
