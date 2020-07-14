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

export default ReviewStars;
