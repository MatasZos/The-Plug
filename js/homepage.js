document.addEventListener("DOMContentLoaded", function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll(".slides");
    const dots = document.querySelectorAll(".dot");

    function showSlide(index) {
        if (index >= slides.length) currentSlide = 0;
        else if (index < 0) currentSlide = slides.length - 1;
        else currentSlide = index;

        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = i === currentSlide ? "block" : "none";
            dots[i].classList.toggle("active", i === currentSlide);
        }
    }

    function autoAdvance() {
        showSlide(currentSlide + 1);
    }

    if (slides.length > 0) {
        showSlide(currentSlide);
        setInterval(autoAdvance, 4000);
        for (let i = 0; i < dots.length; i++) {
            dots[i].addEventListener("click", function() {
                showSlide(i);
            });
        }
    }
});
