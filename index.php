<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VaxPoint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS ONLY -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- YOUR EXISTING NAVBAR IS HERE -->
    <!-- DO NOT CHANGE IT -->
    <?php include("includes/header.php"); ?>

    <main>

        <section class="hero">
            <div class="hero-content">
                <h1>
                    Modern Vaccination<br>
                    <span>Management System</span>
                </h1>

                <p>
                    VaxPoint ensures children never miss critical vaccinations
                    by combining smart scheduling, verified hospitals, and
                    secure digital health records.
                </p>

                <div class="hero-actions">
                    <button class="btn-primary">Get Started</button>
                    <button class="btn-secondary">Learn More</button>
                </div>
            </div>
        </section>

     <section class="features">
    <h2>What VaxPoint Does</h2>

    <div class="features-grid">
        <div class="feature">
            <img src="assets/images/smart-schedule.png" alt="Smart Scheduling">
            <p>Smart Scheduling</p>
        </div>
        <div class="feature">
            <img src="assets/images/secure-records.png" alt="Secure Records">
            <p>Secure Child Records</p>
        </div>
        <div class="feature">
            <img src="assets/images/hospital-verification.png" alt="Hospital Verification">
            <p>Hospital Verification</p>
        </div>
        <div class="feature">
            <img src="assets/images/automated-alerts.png" alt="Automated Alerts">
            <p>Automated Alerts</p>
        </div>
        <div class="feature">
            <img src="assets/images/admin-monitoring.png" alt="Admin Monitoring">
            <p>Admin Monitoring</p>
        </div>
        <div class="feature">
            <img src="assets/images/digital-certificates.png" alt="Digital Certificates">
            <p>Digital Certificates</p>
        </div>
    </div>
</section>


        <section class="stats">
            <div class="stat">
                <h3 class="count" data-target="18000">0</h3>
                <p>Children Registered</p>
            </div>
            <div class="stat">
                <h3 class="count" data-target="500">0</h3>
                <p>Hospitals Connected</p>
            </div>
            <div class="stat">
                <h3 class="count" data-target="99">0</h3>
                <p>Coverage (%)</p>
            </div>
        </section>

    </main>

    <!-- YOUR EXISTING FOOTER IS HERE -->
    <!-- DO NOT CHANGE IT -->
    <?php include("includes/footer.php"); ?>

    <!-- JS INSIDE HTML (AS REQUESTED) -->
    <script>
        const counters = document.querySelectorAll('.count');

        counters.forEach(counter => {
            const update = () => {
                const target = +counter.dataset.target;
                const value = +counter.innerText;

                if (value < target) {
                    counter.innerText = value + Math.ceil(target / 80);
                    setTimeout(update, 25);
                } else {
                    counter.innerText = target;
                }
            };
            update();
        });

        function toggleMenu() {
    const nav = document.getElementById("navLinks");
    nav.classList.toggle("show");   // use class instead of inline display
}

    </script>

</body>
</html>
