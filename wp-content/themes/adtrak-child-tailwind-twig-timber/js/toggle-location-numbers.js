// Below finds any data of "data-ld-toggle" and matches it to a corresponding div class

var allDataLDToggle = document.querySelectorAll('[data-ld-toggle]')

allDataLDToggle.forEach(element => {
  var dataName = element.getAttribute('data-ld-toggle')
  element.addEventListener("click",function(){
    document.getElementById(dataName).classList.toggle('hidden')
  })
})
