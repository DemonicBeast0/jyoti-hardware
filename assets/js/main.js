// Navbar Scroll

window.addEventListener("scroll", () => {

    const navbar = document.querySelector(".navbar");

    if(window.scrollY > 80){

        navbar.classList.add("scrolled");

    }else{

        navbar.classList.remove("scrolled");

    }

});

// Hero Swiper

const heroSwiper = new Swiper(".heroSwiper",{

    loop:true,

    speed:1200,

    effect:"fade",

    autoplay:{

        delay:5000,

        disableOnInteraction:false

    },

    pagination:{

        el:".swiper-pagination",

        clickable:true

    },

    navigation:{

        nextEl:".swiper-button-next",

        prevEl:".swiper-button-prev"

    }

});

//testimonialSwiper

const testimonialElement = document.querySelector(".testimonialSwiper");

if (testimonialElement) {
    new Swiper(".testimonialSwiper", {
        loop: true,
        spaceBetween: 30,

        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },

        pagination: {
            el: ".testimonialSwiper .swiper-pagination",
            clickable: true
        },

        breakpoints: {
            320: {
                slidesPerView: 1
            },
            768: {
                slidesPerView: 2
            },
            1200: {
                slidesPerView: 3
            }
        }
    });
}

// AOS

AOS.init({

    duration:1000,

    once:true

});

// =========================
// Back To Top
// =========================

const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", () => {

    if(window.scrollY > 300){

        topBtn.style.display = "flex";

    }else{

        topBtn.style.display = "none";

    }

});

topBtn.addEventListener("click",()=>{

    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

});

// ===============================
// PRODUCT QUICK VIEW
// ===============================

document.querySelectorAll(".quick-view").forEach(button => {

    button.addEventListener("click", () => {

        document.getElementById("modalTitle").innerHTML = button.dataset.name;

        document.getElementById("modalBrand").innerHTML = button.dataset.brand;

        document.getElementById("modalCategory").innerHTML = button.dataset.category;

        document.getElementById("modalDescription").innerHTML = button.dataset.description;

        document.getElementById("modalImage").src = button.dataset.image;

        document.getElementById("modalDetails").href =
            "product-details.php?id=" + button.dataset.id;

    });

});