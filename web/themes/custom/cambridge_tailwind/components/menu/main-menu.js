(function (Drupal, $) {
  Drupal.behaviors.mainMenu = {
    attach: function (context, settings) {
      this.$dropdownButtons = $('.js-dropbtn');
      this.$allMenus = $('.js-dropdown-content');
      this.$overlay = $('#overlay');
      const o = this;

      once('mainMenu', 'html').forEach(function (element) {
        // Close the dropdown if the user clicks outside of it
        document.addEventListener('click', function(event) {
          const o = this;
          var isClickInside = event.target.closest('.js-dropdown');

          if (!isClickInside) {
            o.hideAllMenus();
            o.$overlay.addClass('hidden');
          }
        });

        // Add event listener to the dropdown buttons
        o.$dropdownButtons.on('click', function(event) {
          const $button = $(this);
          event.stopPropagation(); // Prevent the click event from propagating to the document
          o.toggleDropdown($button);
        });
      })
    },
    getRelatedDropDown: function($button) {
      const o = this;
      const menuItemCount = $button.data('menu-item-count');
      return o.$allMenus.filter("[data-menu-item-count=" + menuItemCount + "]");
    },
    toggleDropdown: function($button) {
      const o = this;
      const $menu = o.getRelatedDropDown($button);
      o.hideAllMenus($menu);

      // Toggle the current menu
      if ($menu.attr("aria-hidden") == "false") {
        o.hideMenu($menu, $button);
      } else {
        o.showMenu($menu, $button);
      }
    },
    showMenu: function($menu, $button) {
      $menu.attr("aria-hidden", "false");
      $menu.removeClass('hidden');
      $button.attr("aria-expanded", "true");
      $menu.find("a").first().focus(); // Focus on the first link in the submenu
    },
    hideMenu: function($menu, $button) {
      $menu.attr("aria-hidden", "true");
      $menu.addClass('hidden');
      $button.attr("aria-expanded", "false");
    },
    hideAllMenus: function($activeMenu) {
      const o = this;
      o.$dropdownButtons.each(function (index, button) {
        const $button = $(button);
        const $menu = o.getRelatedDropDown($button);
        if ($menu.data('menu-item-count') !== $activeMenu.data('menu-item-count')) {
          o.hideMenu($menu, $button);
        }
      });
    }
  }
})(Drupal, jQuery);
