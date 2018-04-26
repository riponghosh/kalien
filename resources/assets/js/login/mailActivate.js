function resendActivateCodeAPI() {
    return $.ajax({
        type: 'POST',
        url: '/activation/resend_activate_code',
        async : false,
        statusCode: {
            401: function () {
                loginModal.show();
                login.afterSuccessToDo($.Callbacks().add(resendActivateCodeAPI));
            }
        }
    }).responseJSON;
}