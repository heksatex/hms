(function ($) {

    function HMSMenuManager() {

        this.$menu = $("#main-navbar-menu");
        this.$more = this.$menu.find(".more-menu");
        this.$dropdown = this.$more.find(".dropdown-menu");

        this.init();
    }

    HMSMenuManager.prototype.init = function () {

        var self = this;

        self.refresh();

        $(window).on("resize", function () {

            clearTimeout(self.timer);

            self.timer = setTimeout(function () {

                self.refresh();

            }, 100);

        });

    };

   HMSMenuManager.prototype.refresh = function () {

        var self = this;

        // kembalikan semua menu dari dropdown
        self.$dropdown.children().insertBefore(self.$more);

        self.$more.hide();

        var availableWidth = self.getAvailableWidth();

        var totalWidth = 0;

        var reserveWidth = 90; // ruang untuk tombol More

        var items = self.$menu.children("li:not(.more-menu)");

        items.each(function(){

            totalWidth += $(this).outerWidth(true);

            if(totalWidth > (availableWidth - reserveWidth))
            {
                self.$more.show();

                $(this).appendTo(self.$dropdown);
            }

        });

    };

    HMSMenuManager.prototype.getAvailableWidth = function () {

        var navbarWidth = $("#navbar-collapse").innerWidth();

        return navbarWidth - 30;

    };


    var activeItem = null;

    items.each(function(){

        if($(this).hasClass("active"))
        {
            activeItem = $(this);
            return;
        }

        totalWidth += $(this).outerWidth(true);

        if(totalWidth > (availableWidth - reserveWidth))
        {
            self.$more.show();
            $(this).appendTo(self.$dropdown);
            self.$more.find(".dropdown-toggle").dropdown();
        }

    });

    if(activeItem)
    {
        activeItem.insertBefore(self.$more);
    }

    $(window).on("resize", function(){

        clearTimeout(self.timer);

        self.timer = setTimeout(function(){

            self.refresh();

        },150);

    });

    window.HMSMenuManager = HMSMenuManager;

})(jQuery);

$(function () {

    new HMSMenuManager();

});