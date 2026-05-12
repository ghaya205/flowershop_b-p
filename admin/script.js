let menuBtn = document.querySelector("#menu-btn");
let userBtn = document.querySelector("#user-btn");

let navbar = document.querySelector(".navbar");
let userBox = document.querySelector(".user-box");

menuBtn.addEventListener("click", () => {
  navbar.classList.toggle("active");
});

userBtn.addEventListener("click", () => {
  userBox.classList.toggle("active");
});

//close edit
const closeBtn = document.querySelector("#close-edit");
closeBtn.addEventListener("click", () => {
  document.querySelector(".update-container").style.display = "none";
});
