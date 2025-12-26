<?php include("includes/header.php"); ?>

<section class="hero">
    <!-- LEFT TEXT -->
    <div class="hero-text">
        <h1>
            Protecting Every Child,<br>
            <span>Digitally & Safely</span>
        </h1>

        <p>
            VaxPoint is a modern e-Vaccination Management System designed to
            prevent missed vaccinations through smart scheduling, hospital
            coordination, and transparent digital records.
        </p>
    </div>

    <!-- RIGHT MINI CAROUSEL -->
    <div class="side-carousel">
        <div class="carousel-track" id="track">
            <div class="carousel-item">
                <img src="assets/images/1.jpg">
                <p>Trusted Healthcare System</p>
            </div>
            <div class="carousel-item">
                <img src="assets/images/2.jpg">
                <p>Secure Child Records</p>
            </div>
            <div class="carousel-item">
                <img src="assets/images/3.jpg">
                <p>Smart Vaccination Alerts</p>
            </div>
            <div class="carousel-item">
                <img src="assets/images/4.jpg">
                <p>Hospital Verified Data</p>
            </div>
            <div class="carousel-item">
                <img src="assets/images/5.jpg">
                <p>Improved Public Health</p>
            </div>
            <div class="carousel-item">
                <img src="assets/images/6.jpg">
                <p>Healthy Future Generations</p>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <h2>Powerful Features</h2>

    <div class="feature-grid">
        <div class="feature-card">
            <h3>Smart Scheduling</h3>
            <p>Automatic vaccination schedules with real-time reminders.</p>
        </div>

        <div class="feature-card">
            <h3>Secure Records</h3>
            <p>Encrypted child vaccination records accessible anytime.</p>
        </div>

        <div class="feature-card">
            <h3>Hospital Integration</h3>
            <p>Direct coordination between hospitals and parents.</p>
        </div>

        <div class="feature-card">
            <h3>Admin Dashboard</h3>
            <p>Centralized monitoring with analytics and reports.</p>
        </div>
    </div>
</section>
<section class="stats">
    <div class="stat">
        <h3 class="count" data-target="1200">0</h3>
        <p>Children Registered</p>
    </div>
    <div class="stat">
        <h3 class="count" data-target="48">0</h3>
        <p>Hospitals Connected</p>
    </div>
    <div class="stat">
        <h3 class="count" data-target="96">0</h3>
        <p>Vaccination Coverage %</p>
    </div>
</section>



<script>
let i = 0;
setInterval(() => {
    const track = document.getElementById("track");
    i = (i + 1) % 6;
    track.style.transform = `translateX(-${i * 100}%)`;
}, 3000);
</script>

<?php include("includes/footer.php"); ?>
<script>
/* COUNTER ANIMATION */
const counters = document.querySelectorAll('.count');

counters.forEach(counter => {
    const update = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const speed = 100;

        if (count < target) {
            counter.innerText = count + Math.ceil(target / speed);
            setTimeout(update, 20);
        } else {
            counter.innerText = target;
        }
    };
    update();
});
</script>
<script>
const revealElements = document.querySelectorAll('.feature-card, .stat');

window.addEventListener('scroll', () => {
    revealElements.forEach(el => {
        const pos = el.getBoundingClientRect().top;
        if (pos < window.innerHeight - 100) {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        }
    });
});
</script>

