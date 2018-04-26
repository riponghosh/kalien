<script>
$('#cur_selection').change(function(){
    window.location.href = '/changeCurrencyUnit/'+$(this).val();
})
/*
**  hopscotch setUp
*/

hopscotchs = {
    1: {
        id: "my-intro",
        steps: [
            {
                target: "open-my-plans-modal-btn",
                title: " 打開看看你的活動",
                content: "你新增的活動在這𥚃看到",
                placement: "left",
                xOffset: 25,
            },
        ]
    }
}
$(document).ready(function () {
    initURLParameterAction(window.location.href);
})
</script>