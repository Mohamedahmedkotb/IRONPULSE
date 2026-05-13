document.addEventListener("DOMContentLoaded", function () {


  const ctx1 = document.getElementById('lineChart');
  const ctx2 = document.getElementById('barChart');

  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
      datasets: [{
        data: [3000, 6000, 8000, 9500, 14000],
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.15)',
        tension: 0.4,
        fill: true,
        pointRadius: 6,
        pointHoverRadius: 9,
        pointBackgroundColor: '#3b82f6'
      }]
    },

    options: {
      responsive: true,
      maintainAspectRatio: false,

      plugins: {
        legend: {
          display: false
        },

        tooltip: {
          backgroundColor: '#07152d',
          padding: 12,
          titleColor: '#fff',
          bodyColor: '#fff'
        }
      },

      interaction: {
        intersect: false,
        mode: 'index'
      },

      scales: {
        x: {
          grid: {
            display: false
          }
        },

        y: {
          beginAtZero: true,

          ticks: {
            callback: v => v >= 1000 ? v / 1000 + 'k' : v
          }
        }
      }
    }
  });

  new Chart(ctx2, {
    type: 'bar',

    data: {
      labels: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],

      datasets: [{
        data: [4, 7, 3, 9, 5, 2, 1],
        backgroundColor: '#3b82f6',
        hoverBackgroundColor: '#2563eb',
        borderRadius: 8
      }]
    },

    options: {
      responsive: true,
      maintainAspectRatio: false,

      plugins: {
        legend: {
          display: false
        }
      },

      scales: {
        y: {
          display: false
        },

        x: {
          grid: {
            display: false
          }
        }
      }
    }
  });


  const sidebarLinks = document.querySelectorAll(".nav-links a");

  sidebarLinks.forEach(link => {

    link.addEventListener("click", function () {

      // Remove active class
      sidebarLinks.forEach(item => {
        item.classList.remove("active");
      });

      // Add active class
      this.classList.add("active");

    });

  });


  const topLinks = document.querySelectorAll(".top-links a");

  topLinks.forEach(link => {

    link.addEventListener("click", function () {

      topLinks.forEach(item => {
        item.classList.remove("active-link");
      });

      this.classList.add("active-link");

    });

  });



  const tabs = document.querySelectorAll(".tabs button");

  tabs.forEach(tab => {

    tab.addEventListener("click", function () {

      tabs.forEach(btn => {
        btn.classList.remove("active");
      });

      this.classList.add("active");

    });

  });


  const cards = document.querySelectorAll(".card");

  cards.forEach(card => {

    card.addEventListener("mouseenter", () => {
      card.style.transform = "translateY(-6px)";
      card.style.transition = "0.3s ease";
      card.style.boxShadow = "0 15px 30px rgba(0,0,0,0.08)";
    });

    card.addEventListener("mouseleave", () => {
      card.style.transform = "translateY(0px)";
      card.style.boxShadow = "none";
    });

  });



  const items = document.querySelectorAll(".item");

  items.forEach(item => {

    item.addEventListener("mouseenter", () => {
      item.style.transform = "scale(1.01)";
      item.style.transition = "0.3s";
      item.style.boxShadow = "0 8px 20px rgba(0,0,0,0.06)";
    });

    item.addEventListener("mouseleave", () => {
      item.style.transform = "scale(1)";
      item.style.boxShadow = "none";
    });

  });



  // CREATE BUTTON
  const sidebar = document.querySelector(".sidebar");

  const toggleBtn = document.createElement("button");

  toggleBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';

  toggleBtn.classList.add("sidebar-toggle");

  document.body.appendChild(toggleBtn);

  toggleBtn.addEventListener("click", () => {

    sidebar.classList.toggle("collapsed");

  });


  const darkBtn = document.createElement("button");

  darkBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';

  darkBtn.classList.add("dark-mode-btn");

  document.body.appendChild(darkBtn);

  darkBtn.addEventListener("click", () => {

    document.body.classList.toggle("dark-mode");

  });

});