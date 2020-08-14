// --------------------------------------------------------------------------------------------------
// Back to top
// -- Uses IntersectionObserver API to determine if the window has
// -- scrolled past the pixel-to-watch (in footer.twig)
// -- Not supported in IE11 & < Safari 12.0 only
// --------------------------------------------------------------------------------------------------

if ("IntersectionObserver" in window) {
// // Set a variable to identify the back to top button
  var backToTopBtn = document.getElementById("back-top");

  var observer = new IntersectionObserver((entries) => {
    if (entries[0].boundingClientRect.y < 0) {
      // Add the opacity class to bring in the
      backToTopBtn.classList.add("opacity-25");
      backToTopBtn.classList.remove("opacity-0");
      // Bring it into the viewport (removing from the viewport means it isn't clickable)
      backToTopBtn.classList.remove("-mb-20");
    } else {
      backToTopBtn.classList.remove("opacity-25");
      backToTopBtn.classList.add("opacity-0");
      // Move it back out of the viewport
      backToTopBtn.classList.add("-mb-20");
    }
  });
  observer.observe(document.querySelector("#pixel-to-watch"));
  backToTopBtn.onclick = function () {
    window.scroll({
      top: 0,
      left: 0,
      behaviour: "smooth",
    });
  };
}
