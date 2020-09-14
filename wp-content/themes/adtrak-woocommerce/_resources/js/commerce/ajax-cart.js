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

export default AjaxCart;
