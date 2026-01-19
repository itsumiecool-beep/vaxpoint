<?php
require_once 'config/config.php';

// If already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    switch ($_SESSION['role']) {
        case 'admin':
            redirect('admin-secure-portal/dashboard.php');
            break;
        case 'parent':
            redirect('parent/dashboard.php');
            break;
        case 'hospital':
            redirect('hospital/dashboard.php');
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .landing-container {
            max-width: 1200px;
            width: 100%;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease;
        }
        
        .header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 1.3rem;
            font-weight: 300;
            opacity: 0.95;
        }
        
        .role-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .role-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            cursor: pointer;
            animation: fadeInUp 0.8s ease;
            position: relative;
            overflow: hidden;
        }
        
        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        }
        
        .role-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .role-card h2 {
            color: #1e293b;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .role-card p {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        
        .features {
            margin-top: 60px;
            text-align: center;
            color: white;
        }
        
        .features h3 {
            font-size: 2rem;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .feature-item {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .feature-item h4 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .feature-item p {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .header p {
                font-size: 1.1rem;
            }
            
            .role-cards {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="header">
            <h1>💉 <?php echo SITE_NAME; ?></h1>
            <p><?php echo SITE_TAGLINE; ?></p>
        </div>
        
        <div class="role-cards">
            <!-- Parent Card -->
            <div class="role-card" style="animation-delay: 0.1s;">
                <div class="role-icon">👨‍👩‍👧‍👦</div>
                <h2>Parent Portal</h2>
                <p>Manage your children's vaccination schedules, book appointments, and track vaccination history.</p>
                <div class="btn-group">
                    <a href="parent/login.php" class="btn btn-primary">Login</a>
                    <a href="parent/register.php" class="btn btn-secondary">Register</a>
                </div>
            </div>
            
            <!-- Hospital Card -->
            <div class="role-card" style="animation-delay: 0.2s;">
                <div class="role-icon">🏥</div>
                <h2>Hospital Portal</h2>
                <p>Manage appointments, update vaccination status, and maintain vaccine inventory.</p>
                <div class="btn-group">
                    <a href="hospital/login.php" class="btn btn-primary">Login</a>
                    <a href="hospital/register.php" class="btn btn-secondary">Register</a>
                </div>
            </div>
            
        
        </div>
        
        <div class="features">
            <h3>Why Choose VaxPoint?</h3>
            <div class="feature-grid">
                <div class="feature-item">
                    <h4>📅 Smart Scheduling</h4>
                    <p>Automated vaccination reminders and easy appointment booking</p>
                </div>
                <div class="feature-item">
                    <h4>📊 Digital Records</h4>
                    <p>Complete vaccination history at your fingertips</p>
                </div>
                <div class="feature-item">
                    <h4>🔔 Real-time Notifications</h4>
                    <p>Get instant updates on appointments and vaccine availability</p>
                </div>
                <div class="feature-item">
                    <h4>🏆 Trusted Platform</h4>
                    <p>Secure, reliable, and user-friendly vaccination management</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>