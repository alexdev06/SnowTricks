jQuery(document).ready(function () {
    
    $(window).scroll(function () {
        var height = $(window).scrollTop();
        if (height > 100) {
            $("#to-top").fadeIn();
            $("#to-list").fadeOut();
        } else {
            $("#to-top").fadeOut();
            $("#to-list").fadeIn();
        }
    });

    $("#to-top").click(function (event) {
        event.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });

    $("#to-list").click(function (event) {
        event.preventDefault();
        $("html, body").stop().animate({ scrollTop: $("#list").offset().top }, "slow");
        return false;
    });
});