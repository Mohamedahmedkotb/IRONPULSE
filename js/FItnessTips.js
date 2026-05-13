

const filterButtons = document.querySelectorAll(".filter-btn");
const cards = document.querySelectorAll(".card");
const heroCard = document.querySelector(".hero-card");
const navLinks = document.querySelectorAll(".navbar nav a");



filterButtons.forEach((button) => {

    button.addEventListener("click", () => {

        filterButtons.forEach((btn) => {
            btn.classList.remove("active");
        });

        button.classList.add("active");

        const filterValue = button.textContent.trim().toLowerCase();


        if (filterValue === "all tips") {

            heroCard.style.display = "block";

        }

        else if (heroCard.dataset.category === filterValue) {

            heroCard.style.display = "block";

        }

        else {

            heroCard.style.display = "none";

        }


        cards.forEach((card) => {

            const category = card.dataset.category;

            if (filterValue === "all tips") {

                card.style.display = "block";

            }

            else if (category === filterValue) {

                card.style.display = "block";

            }

            else {

                card.style.display = "none";

            }

        });

    });

});


navLinks.forEach((link) => {

    link.addEventListener("click", () => {

        navLinks.forEach((nav) => {
            nav.classList.remove("active");
        });

        link.classList.add("active");

    });

});


cards.forEach((card) => {

    card.addEventListener("mouseenter", () => {

        card.style.transform = "translateY(-8px)";
        card.style.transition = "0.3s ease";

    });

    card.addEventListener("mouseleave", () => {

        card.style.transform = "translateY(0)";

    });

});



window.addEventListener("load", () => {

    const heroContent = document.querySelector(".hero-content");

    heroContent.style.opacity = "0";
    heroContent.style.transform = "translateY(30px)";

    setTimeout(() => {

        heroContent.style.transition = "all 0.8s ease";
        heroContent.style.opacity = "1";
        heroContent.style.transform = "translateY(0)";

    }, 200);

});



const readTimes = document.querySelectorAll(".read-time");

readTimes.forEach((time) => {

    time.addEventListener("click", () => {

        alert("Opening article...");

    });

});


document.querySelectorAll('a[href^="#"]').forEach((anchor) => {

    anchor.addEventListener("click", function (e) {

        e.preventDefault();

        const target = document.querySelector(this.getAttribute("href"));

        if (target) {

            target.scrollIntoView({
                behavior: "smooth"
            });

        }

    });

});