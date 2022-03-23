document.querySelector(".B").addEventListener("click", function () {
    reset();
    document.querySelector(".Poulet").classList.add("invisible");
    document.querySelector(".Poisson").classList.add("invisible");
    document.querySelector(".Veggie").classList.add("invisible");
    document.querySelector(".Boeuf").classList.toggle("lighter");

})


document.querySelector(".V").addEventListener("click", function () {
    reset();
    document.querySelector(".Poulet").classList.add("invisible");
    document.querySelector(".Poisson").classList.add("invisible");
    document.querySelector(".Boeuf").classList.add("invisible");
    document.querySelector(".Veggie").classList.toggle("lighter");
})

document.querySelector(".PT").addEventListener("click", function () {
    reset();
    document.querySelector(".Veggie").classList.add("invisible");
    document.querySelector(".Poisson").classList.add("invisible");
    document.querySelector(".Boeuf").classList.add("invisible");
    document.querySelector(".Poulet").classList.toggle("lighter");
})

document.querySelector(".P").addEventListener("click", function () {
    reset();
    document.querySelector(".Veggie").classList.add("invisible");
    document.querySelector(".Poulet").classList.add("invisible");
    document.querySelector(".Boeuf").classList.add("invisible");
    document.querySelector(".Poisson").classList.toggle("lighter");

})



function reset () {
    document.querySelector(".Poulet").classList.remove("invisible");
    document.querySelector(".Poisson").classList.remove("invisible");;
    document.querySelector(".Veggie").classList.remove("invisible");
  }