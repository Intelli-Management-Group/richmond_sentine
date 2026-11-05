$(function () {
    $(".dropdown-menu a.dropdown-item").on("click", function (e) {
        if (
            !$(this)
            .next()
            .hasClass("show")
        ) {
            $(this)
                .parents(".dropdown-menu")
                .first()
                .find(".show")
                .removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass("show");

        $(this)
            .parents("li.nav-item.dropdown.show")
            .on("hidden.bs.dropdown", function (e) {
                $(".dropdown-submenu .show").removeClass("show");
            });
        return false;
    });

    $(".video-play-pause")
        .find("i")
        .click(function (e) {
            $(".video-inner")
                .find("video")
                .each(function () {
                    $(this)
                        .get(0)
                        .pause();
                });
            e.preventDefault();
            if ($(this).hasClass("fa-play-circle-o")) {
                $(this)
                    .parents("a")
                    .find(".video-throbber")
                    .removeClass("hidden");
                $(this)
                    .parents("a")
                    .find("video")
                    .get(0)
                    .play();
                $(this)
                    .removeClass("fa-play-circle-o")
                    .addClass("fa-pause-circle-o");
            } else if ($(this).hasClass("fa-pause-circle-o")) {
                $(this)
                    .parents("a")
                    .find("video")
                    .get(0)
                    .pause();
                $(this)
                    .removeClass("fa-pause-circle-o")
                    .addClass("fa-play-circle-o");
            }
        });
    $(".video-inner").each(function () {
        var video_tag = $("video", this);
        $(video_tag).attr(
            "height",
            Math.floor($(video_tag).width()) / 2 + "px"
        );
        $(video_tag).on("canplay", function () {
            $(this)
                .next(".video-throbber")
                .addClass("hidden");
        });
    });     

    var height = $("#detail").height();
    var treheight = $(".trendingnews").outerHeight();
    var totaltrending = Math.floor(height / treheight);
    $(".trendingnews:gt(" + totaltrending + ")").remove();
    lazyLoadArticles();
});
function lazyLoadArticles(){
     $(".article-inner .throbber").find("img:not(.video-list-play-button)").each(function () {
         if ($(this).parents(".throbber").find(".throbber_after").length > 0) {
             $(this).attr("src", $(this).data("src"));
             $(this).on('load', function () {
                 $(this).parents(".throbber").find(".throbber_after").remove();
                 $(this).removeAttr("data-src");
                 $(this).off('load');
             });
         }
     });
}