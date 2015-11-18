function isRegister(callback) {
    $.get('/api/isregister', function(data) {
        console.log(data);
    });
}

function openLogInPopup() {
    $.get('/api/get_authentification_url', function (data) {
        window.location.href = data.url;
    });
}

$(document).ready(function () {
    $('.open-login-popup').click(function () {
        openLogInPopup();
    })
    isRegister(function (isRegister) {
        if (isRegister) {
            $('.step-2, .step-3').removeClass('panel-disable');
        } else {
            $('.step-2, .step-3').addClass('panel-disable');
        }
    });
});
