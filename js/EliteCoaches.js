const filterButtons = document.querySelectorAll(".filters button");
const cards = document.querySelectorAll(".card");
const searchInput = document.querySelector(".search-box input");


filterButtons.forEach(button => {

    button.addEventListener("click", () => {

        filterButtons.forEach(btn => {
            btn.classList.remove("active");
        });

        button.classList.add("active");

        const category = button.textContent.toLowerCase();

        cards.forEach(card => {

            const trainerRole =
                card.querySelector("p").textContent.toLowerCase();

            if(category === "all specialties"){
                card.style.display = "block";
            }

            else if(trainerRole.includes(category)){
                card.style.display = "block";
            }

            else{
                card.style.display = "none";
            }

        });

    });

});


searchInput.addEventListener("keyup", () => {

    const searchValue = searchInput.value.toLowerCase();

    cards.forEach(card => {

        const trainerName =
            card.querySelector("h3").textContent.toLowerCase();

        if(trainerName.includes(searchValue)){
            card.style.display = "block";
        }

        else{
            card.style.display = "none";
        }

    });

});