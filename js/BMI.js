const heightSlider = document.getElementById('heightSlider');
const heightValue = document.getElementById('heightValue');

const weightInput = document.getElementById('weightInput');
const weightLabel = document.getElementById('weightLabel');

const plusWeight = document.getElementById('plusWeight');
const minusWeight = document.getElementById('minusWeight');

const calculateBtn = document.getElementById('calculateBtn');

const bmiResult = document.getElementById('bmiResult');
const bmiStatus = document.getElementById('bmiStatus');

const genderButtons = document.querySelectorAll('.gender-btn');

const circle = document.querySelector('.circle');



/* =========================
   GENDER BUTTONS
========================= */

genderButtons.forEach(button => {

    button.addEventListener('click', () => {

        genderButtons.forEach(btn => {
            btn.classList.remove('active-gender');
        });

        button.classList.add('active-gender');

    });

});


const sidebarItems = document.querySelectorAll('.menu li');

sidebarItems.forEach(item => {

    item.addEventListener('click', () => {

        sidebarItems.forEach(li => {
            li.classList.remove('active');
        });

        item.classList.add('active');

    });

});


const dashboardLinks = document.querySelectorAll('.topbar nav a');

dashboardLinks.forEach(link => {

    link.addEventListener('click', () => {

        dashboardLinks.forEach(nav => {
            nav.classList.remove('active-link');
        });

        link.classList.add('active-link');

    });

});



/* =========================
   HEIGHT SLIDER
========================= */

heightSlider.addEventListener('input', () => {

    heightValue.textContent = `${heightSlider.value} cm`;

    updateIdealWeight();

    calculateBMI();

});



/* =========================
   WEIGHT CONTROLS
========================= */

plusWeight.addEventListener('click', () => {

    weightInput.value = parseInt(weightInput.value) + 1;

    updateWeightLabel();

    calculateBMI();

});



minusWeight.addEventListener('click', () => {

    if (parseInt(weightInput.value) > 1) {

        weightInput.value = parseInt(weightInput.value) - 1;

        updateWeightLabel();

        calculateBMI();

    }

});



weightInput.addEventListener('input', () => {

    if (
        weightInput.value === '' ||
        parseInt(weightInput.value) < 1
    ) {
        weightInput.value = 1;
    }

    updateWeightLabel();

    calculateBMI();

});



function updateWeightLabel() {

    weightLabel.textContent = `${weightInput.value} kg`;

}



/* =========================
   BMI CALCULATION
========================= */

calculateBtn.addEventListener('click', calculateBMI);



function calculateBMI() {

    const height = heightSlider.value / 100;

    const weight = parseFloat(weightInput.value);

    if (!height || !weight) return;

    const bmi = (weight / (height * height)).toFixed(1);

    animateBMI(bmi);

    updateCircle(bmi);

    let status = '';



    if (bmi < 18.5) {

        status = 'Underweight';

        bmiStatus.style.background = '#dbeafe';

        bmiStatus.style.color = '#2563eb';

    }

    else if (bmi >= 18.5 && bmi <= 24.9) {

        status = 'Normal';

        bmiStatus.style.background = '#dcfce7';

        bmiStatus.style.color = '#16a34a';

    }

    else if (bmi >= 25 && bmi <= 29.9) {

        status = 'Overweight';

        bmiStatus.style.background = '#fef3c7';

        bmiStatus.style.color = '#d97706';

    }

    else {

        status = 'Obese';

        bmiStatus.style.background = '#fee2e2';

        bmiStatus.style.color = '#dc2626';

    }

    bmiStatus.textContent = status;

    saveBMIHistory(bmi);

}



/* =========================
   BMI NUMBER ANIMATION
========================= */

function animateBMI(target) {

    let current = 0;

    clearInterval(window.bmiAnimation);

    window.bmiAnimation = setInterval(() => {

        current += 0.2;

        if (current >= target) {

            current = target;

            clearInterval(window.bmiAnimation);

        }

        bmiResult.textContent = current.toFixed(1);

    }, 10);

}



/* =========================
   IDEAL WEIGHT
========================= */

function updateIdealWeight() {

    const height = heightSlider.value / 100;

    const min = (18.5 * height * height).toFixed(1);

    const max = (24.9 * height * height).toFixed(1);

    const idealWeightBox = document.querySelector('.small-card h2');

    idealWeightBox.innerHTML = `${min} - ${max} <span>kg</span>`;

}



/* =========================
   BMI CIRCLE ANIMATION
========================= */

function updateCircle(bmi) {

    let degree = Math.min((bmi / 40) * 360, 360);

    circle.style.background = `
        conic-gradient(
            #3b82f6 0deg ${degree}deg,
            #e5e7eb ${degree}deg 360deg
        )
    `;

}



/* =========================
   LOCAL STORAGE
========================= */

function saveBMIHistory(bmi) {

    let history =
        JSON.parse(localStorage.getItem('bmiHistory')) || [];

    history.push({
        bmi: bmi,
        date: new Date().toLocaleDateString()
    });

    localStorage.setItem(
        'bmiHistory',
        JSON.stringify(history)
    );

}



/* =========================
   ENTER KEY SUPPORT
========================= */

document.addEventListener('keydown', (e) => {

    if (e.key === 'Enter') {

        calculateBMI();

    }

});



/* =========================
   INITIAL LOAD
========================= */

updateWeightLabel();

updateIdealWeight();

calculateBMI();