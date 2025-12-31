<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Selection</title>
<style>
/* ================= GLOBAL ================= */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f6f9fc;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #0f172a;
}

.login-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 40px;
    transition: all 0.5s ease;
}

/* ================= CARDS ================= */
.login-card {
    background: linear-gradient(135deg, #0a2540, #0e3a5d);
    border-radius: 25px;
    padding: 60px 40px;
    width: 300px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    position: relative;
    overflow: hidden;
}

.login-card::before {
    content: '';
    position: absolute;
    width: 300%;
    height: 300%;
    background: rgba(47, 128, 237, 0.1);
    top: -100%;
    left: -100%;
    transform: rotate(45deg);
    transition: all 0.7s ease;
}

.login-card:hover::before {
    top: -50%;
    left: -50%;
}

.login-card:hover {
    transform: translateY(-15px) scale(1.05);
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
}

.login-card h2 {
    font-size: 28px;
    margin-bottom: 15px;
    color: #22c1c3;
    z-index: 1;
    position: relative;
}

.login-card p {
    font-size: 16px;
    color: #e6f1ff;
    margin-bottom: 20px;
    z-index: 1;
    position: relative;
}

/* ================= HIDDEN ================= */
.hidden {
    display: none;
    opacity: 0;
}

.fade-in {
    animation: fadeIn 0.5s forwards;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

/* ================= RESPONSIVE ================= */
@media (max-width: 768px) {
    .login-container {
        gap: 20px;
    }

    .login-card {
        width: 80%;
        padding: 50px 30px;
    }
}
</style>
</head>
<body>

<div class="login-container" id="initial-cards">
    <div class="login-card" onclick="location.href='auth/hospital_login.php'">
        <h2>Hospital</h2>
        <p>Login as hospital staff</p>
    </div>
    <div class="login-card" onclick="showParentOptions()">
        <h2>Parent</h2>
        <p>Login or register as a parent</p>
    </div>
</div>

<div class="login-container hidden" id="parent-options">
    <div class="login-card" style="background: linear-gradient(135deg, #0a2540, #0e3a5d);" 
         onclick="location.href='auth/parent_login.php'">
        <h2>Log into Existing Account</h2>
        <p>Already have an account? Login here.</p>
    </div>
    <div class="login-card" style="background: linear-gradient(135deg, #0a2540, #0e3a5d);" 
         onclick="location.href='parent/register.php'">
        <h2>Register New Account</h2>
        <p>Create a new account to get started.</p>
    </div>
</div>

<script>
function showParentOptions() {
    const initial = document.getElementById('initial-cards');
    const parent = document.getElementById('parent-options');
    initial.classList.add('hidden');
    parent.classList.remove('hidden');
    parent.classList.add('fade-in');
}
</script>

</body>
</html>
