<?php
require_once '../config/config.php';

if (isLoggedIn('hospital')) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospital_name = sanitize($_POST['hospital_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = sanitize($_POST['address'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    
    // Validate
    $data = $_POST;
    $data['name'] = $hospital_name;
    $validation = validateRegistration($data, 'hospital');
    
    if ($validation['valid']) {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT hospital_id FROM hospital WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email already registered';
        } else {
            // Insert new hospital
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO hospital (hospital_name, email, password, address, location, status) VALUES (?, ?, ?, ?, ?, 'Active')");
            
            if ($stmt->execute([$hospital_name, $email, $hashed_password, $address, $location])) {
                setFlashMessage('success', 'Registration successful! Please login.');
                redirect('login.php');
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    } else {
        $errors = $validation['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.5s ease;
        }
        
        .register-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .register-header h1 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .register-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .register-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .error-messages {
            background: #fee2e2;
            color: #dc2626;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        
        .error-messages ul {
            margin-left: 20px;
        }
        
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-link a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>🏥 Hospital Registration</h1>
            <p>Join our vaccination network</p>
        </div>
        
        <div class="register-body">
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="hospital_name">Hospital Name *</label>
                    <input type="text" id="hospital_name" name="hospital_name" value="<?php echo htmlspecialchars($_POST['hospital_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="03001234567" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea id="address" name="address" rows="2" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="location">Location/City *</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                </div>
                
                <button type="submit" class="btn-register">Register Hospital</button>
            </form>
            
            <div class="login-link">
                Already registered? <a href="login.php">Login here</a>
            </div>
            
            <div class="back-link">
                <a href="../index.php">← Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>