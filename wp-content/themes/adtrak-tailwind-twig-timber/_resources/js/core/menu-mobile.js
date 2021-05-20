// --------------------------------------------------------------------------------------------------
// Mobile Menu
// --------------------------------------------------------------------------------------------------

// Set some vars for the icons
var iconAngleUp =
  "<svg class='icon icon-angle-up'><use xlink:href='" +
  themeURL.themeURL +
  "/_resources/images/icons-sprite.svg#icon-angle-up'></use></svg>";
var iconAngleDown =
  "<svg class='icon icon-angle-down'><use xlink:href='" +
  themeURL.themeURL +
  "/_resources/images/icons-sprite.svg#icon-angle-down'></use></svg>";

// // Copy primary and secondary menus to .mob-nav element
var mobNav = document.querySelector(".mob-nav .scroll-container");

// // Add Close Icon element
var closeBtn = document.createElement("div");
closeBtn.setAttribute("class", "mob-nav-close");
closeBtn.innerHTML =
  "<svg class='icon icon-times'><use xlink:href='" +
  themeURL.themeURL +
  "/_resources/images/icons-sprite.svg#icon-times'></use></svg>";
mobNav.insertAdjacentElement("beforeend", closeBtn);

// Add dropdown arrow to links with sub-menus
var subNavPosition = document.querySelectorAll(
  ".mob-nav .menu-item-has-children > a"
);
subNavPosition.forEach((element) => {
  var subArrows = document.createElement("span");
  subArrows.setAttribute("class", "sub-arrow");
  subArrows.innerHTML = iconAngleDown + iconAngleUp;
  element.insertAdjacentElement("afterend", subArrows);
});

// Get all the sub nav arrow icons
var allArrows = document.querySelectorAll(".sub-arrow .icon-angle-down");

// Add active class to all sub nav arrow icons
allArrows.forEach((element) => {
  element.classList.add("active");
});

// Get all sub navs
var allSubArrows = document.querySelectorAll(".sub-arrow");

// For each sub nav, let it toggle a class and show/hide the sibling menu
allSubArrows.forEach((subArrow) => {
  subArrow.nextElementSibling.style.display = "none";
  subArrow.addEventListener("click", function () {
    this.classList.toggle("active");
    this.previousElementSibling.classList.toggle("active");
    this.nextElementSibling.style.display =
      this.nextElementSibling.style.display === "none" ? "block" : "none";
    var subArrowChildren = [...this.children];
    subArrowChildren.forEach((child) => {
      child.classList.toggle("active");
    });
  });
});

// Add underlay element after mobile nav
var mobNavUnderlay = document.createElement("div");
mobNavUnderlay.setAttribute("class", "mob-nav-underlay");
document
  .querySelector(".mob-nav")
  .insertAdjacentElement("afterend", mobNavUnderlay);

// Show underlay and fix the body scroll when menu button is clicked
var menuBtn = document.querySelector('[data-mobile-menu-toggle]')

menuBtn.addEventListener("click", function () {
  document.querySelector(".mob-nav-underlay").classList.add("mob-nav--active");
  document.querySelector(".mob-nav").classList.add("mob-nav--active");
  // document.querySelector('body').classList.add('fixed')
});

// Hide menu when close icon or underlay is clicked
var closeMenuBtn = document.querySelector(".mob-nav-close");
var menuOverlay = document.querySelector(".mob-nav-underlay");

closeMenuBtn.addEventListener("click", closeMobileNav);
menuOverlay.addEventListener("click", closeMobileNav);

function closeMobileNav() {
  document
    .querySelector(".mob-nav-underlay")
    .classList.remove("mob-nav--active");
  document.querySelector(".mob-nav").classList.remove("mob-nav--active");
  // document.querySelector('body').classList.remove('fixed')
}
