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

export default ProductTabs;
