(function () {
  'use strict';

  class AjaxCart {
    constructor() {
      this.slideout = document.querySelector('.cart-slideout');
      this.cartOpenOpeners = document.querySelectorAll('[data-ajax-open-cart]');
      this.cartOpenButtons = document.querySelectorAll('.ajax_add_to_cart');

      if (this.cartOpenOpeners != null && this.slideout != null) {
        this.initOpeners();
      }

      if (this.cartOpenButtons != null && this.slideout != null) {
        this.initButtons();
      }
    }

    initOpeners() {
      Array.prototype.forEach.call(this.cartOpenButtons, (button) => {
        button.addEventListener('click', (e) => {
          e.preventDefault();
          this.openMenu();
        });
      });
    }

    initButtons() {
      Array.prototype.forEach.call(this.cartOpenButtons, (button) => {
        button.addEventListener('click', (e) => {
          e.preventDefault();
          this.openMenu();
        });
      });
    }

    openMenu() {
      this.slideout.classList.add('active');
      this.closeHandler();
    }

    closeHandler() {
      const closeBtn = this.slideout.querySelector('[data-ajax-cart-close]');

      if (closeBtn != null) {
        closeBtn.addEventListener('click', () => this.slideout.classList.remove('active'));
      }
    }

    cartUpdate() {}
  }

  class ProductTabs {
    constructor() {
      this.tabLinks = document.querySelectorAll('.product-tab-link');
      this.tabPanels = document.querySelectorAll('.product-tab-panel');

      if (this.tabLinks != null && this.tabPanels != null) {
        this.init();
      }
    }

    init() {
      Array.prototype.forEach.call(this.tabLinks, (link) => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          this.linkClickHandler(link);
        });
      });
    }

    async linkClickHandler(link) {
      const tab = document.querySelector(`${link.getAttribute('href')}`);

      if (tab != null) {
        this.closeTabs();

        link.classList.add('active');
        tab.classList.remove('hidden');
      }
    }

    closeTabs() {
      Array.prototype.forEach.call(this.tabLinks, (link) => {
        link.classList.remove('active');
      });

      Array.prototype.forEach.call(this.tabPanels, (panel) => {
        panel.classList.add('hidden');
      });
    }
  }

  class ReviewStars {
    constructor() {
      // We have to wait for the DOM to be loaded on this one as the stars are inserted with JS via WooCommerce.
      document.addEventListener('DOMContentLoaded', (e) => {
        this.starHolder = document.querySelector('.comment-form-rating .stars');
        this.stars = document.querySelectorAll('.comment-form-rating .stars a');

        if (this.starHolder != null && this.stars != null) {
          this.init();
        }
      });
    }

    init() {
      this.starHolder.addEventListener('mouseleave', (e) => {
        Array.prototype.forEach.call(this.stars, (star, key) => {
          star.style.color = '';
        });
      });

      Array.prototype.forEach.call(this.stars, (star, key) => {
        star.addEventListener('mouseover', (e) => {
          e.preventDefault();
          this.mouseoverHandler(key);
        });

        /* This is controlled via WooCommerce (adds active to star and sets the select box.)
         If you delete something/remove jquery you may need to add this in here. */

        star.addEventListener('click', async (e) => {
          await this.clickHandler(key);
        });
      });
    }

    /**
     * Loop through each star, and add or remove the `.selected` class
     */
    mouseoverHandler(selectedIndex) {
      Array.prototype.forEach.call(this.stars, (star, key) => {
        if (key <= selectedIndex) {
          star.style.color = 'gold';
        } else {
          star.style.color = '';
        }
      });
    }

    clickHandler(selectedIndex) {
      Array.prototype.forEach.call(this.stars, (star, key) => {
        if (key <= selectedIndex) {
          star.classList.add('selected');
        } else {
          star.classList.remove('selected');
        }
      });
    }
  }

  new AjaxCart();
  new ProductTabs();
  new ReviewStars();

}());
