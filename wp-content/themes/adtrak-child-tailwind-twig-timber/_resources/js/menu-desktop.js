// Add angle down to all nav items with children
var allArrowDown = document.querySelectorAll(".menu > ul > .menu-item-has-children > a")
allArrowDown.forEach(element => {
  var iconDown = document.createElementNS("http://www.w3.org/2000/svg","svg")
  iconDown.setAttribute("class", "icon icon-angle-down")
  iconDown.innerHTML = "<use xlink:href='" + themeURL.themeURL + "/_resources/images/icons-sprite.svg#icon-angle-down'></use>"
  element.insertAdjacentElement("beforeend", iconDown);
});
