$(document).ready(function () {
    let result = $('.result');

    $('.get-prize').click(function () {
        $.post({
            url: '/lottery/try',
            success: function (data) {
                result.html(data);
            }
        });
    });

    window.accept = function (id, delivery) {
        $.post({
            url: '/lottery/accept',
            data: {
                id: id,
                delivery: delivery
            },
            success: function (data) {
                result.html(data);
            }
        });
    };

    window.decline = function (id) {
        $.post({
            url: '/lottery/decline',
            data: {
                id: id
            },
            success: function (data) {
                result.html(data);
            }
        });
    };
});
