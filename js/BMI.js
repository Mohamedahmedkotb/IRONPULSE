
const heightSlider = document.getElementById('heightSlider');
const heightValue = document.getElementById('heightValue');

const weightInput = document.getElementById('weightInput');
const weightLabel = document.getElementById('weightLabel');

const plusWeight = document.getElementById('plusWeight');
const minusWeight = document.getElementById('minusWeight');

const calculateBtn = document.getElementById('calculateBtn');

const bmiResult = document.getElementById('bmiResult');
const bmiStatus = document.getElementById('bmiStatus');

heightSlider.addEventListener('input', () => {
    heightValue.textContent = `${heightSlider.value} cm`;
});


plusWeight.addEventListener('click', () => {
    weightInput.value = parseInt(weightInput.value) + 1;
    updateWeightLabel();
});

minusWeight.addEventListener('click', () => {

    if(weightInput.value > 1) {
        weightInput.value = parseInt(weightInput.value) - 1;
        updateWeightLabel();
    }

});

weightInput.addEventListener('input', updateWeightLabel);

function updateWeightLabel() {
    weightLabel.textContent = `${weightInput.value} kg`;
}

calculateBtn.addEventListener('click', () => {

    const height = heightSlider.value / 100;
    const weight = weightInput.value;

    const bmi = (weight / (height * height)).toFixed(1);

    bmiResult.textContent = bmi;

    let status = '';

    if(bmi < 18.5) {
        status = 'Underweight';
        bmiStatus.style.background = '#dbeafe';
        bmiStatus.style.color = '#2563eb';
    }

    else if(bmi >= 18.5 && bmi <= 24.9) {
        status = 'Normal';
        bmiStatus.style.background = '#dcfce7';
        bmiStatus.style.color = '#16a34a';
    }

    else if(bmi >= 25 && bmi <= 29.9) {
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

});
