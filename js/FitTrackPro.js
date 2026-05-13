const navLinks = document.querySelectorAll(".topnav a");

navLinks.forEach(link => {

    link.addEventListener("click", function () {

        navLinks.forEach(item => {
            item.classList.remove("active");
        });

        this.classList.add("active");

    });

});



const menuItems = document.querySelectorAll(".menu li");

menuItems.forEach(item => {

    item.addEventListener("click", function () {

        menuItems.forEach(menu => {
            menu.classList.remove("active");
        });

        this.classList.add("active");

    });

});



const workoutBtn = document.querySelector(".btn");

workoutBtn.addEventListener("click", () => {

    alert("Workout Session Started 💪");

    workoutBtn.innerHTML = "Workout Active";

    workoutBtn.style.background = "#16a34a";

});



const historyBtn = document.querySelector(".history");

historyBtn.addEventListener("click", () => {

    alert("Opening Full Workout History...");

});


const progressBars = document.querySelectorAll(".progress div");

window.addEventListener("load", () => {

    progressBars.forEach(bar => {

        let finalWidth = bar.style.width;

        bar.style.width = "0";

        setTimeout(() => {

            bar.style.transition = "1.5s";

            bar.style.width = finalWidth;

        }, 300);

    });

});



const cards = document.querySelectorAll(".card, .program, .activity");

cards.forEach(card => {

    card.addEventListener("mouseenter", () => {

        card.style.transform = "translateY(-5px)";
        card.style.transition = "0.3s";

    });

    card.addEventListener("mouseleave", () => {

        card.style.transform = "translateY(0)";

    });

});



const totalWorkouts = document.querySelector(".card h2");

let count = 142;

setInterval(() => {

    count++;

    totalWorkouts.innerHTML = count;

}, 10000);




const activityItems = document.querySelectorAll(".activity-item");

activityItems.forEach(item => {

    item.addEventListener("click", () => {

        const title = item.querySelector("h4").innerText;

        alert(`Opening details for: ${title}`);

    });

});