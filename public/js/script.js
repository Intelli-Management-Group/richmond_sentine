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

    $(".video-inner").each(function () {
        var video_tag = $("video", this);
        $(video_tag).attr(
            "height",
            Math.floor($(video_tag).width()) / 2 + "px"
        );
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