<?php
session_start([
  'cookie_lifetime' => 86400, // 1 day
  'cookie_secure'   => true,  // Only send over HTTPS
  'cookie_httponly' => true,  // Prevent JavaScript access
  'cookie_samesite' => 'Strict' // Strict same-site policy
]);
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if parameters exist before accessing
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Predefined credentials
    $validUsers = [
        '11' => ['password' => '11', 'role' => 'user'],
        '22' => ['password' => '22', 'role' => 'admin']
    ];

    if (!empty($username) && !empty($password)) {
        if (isset($validUsers[$username]) && $password === $validUsers[$username]['password']) {
            session_regenerate_id(true);
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $validUsers[$username]['role'];
            // Cookie setting (Not recommended for production)
            setcookie(
              'Role',
              $validUsers[$username]['role'],
              [
                  'expires' => time() + 86400,
                  'path' => '/',
                  'domain' => $_SERVER['HTTP_HOST']
              ]
          );
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maroon Themed Login Form</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>


  <div class="container">
    <div class="shapes">
    <?php if (!empty($error)): ?>
    <div class="error-message" style="color: red; text-align: center; margin: 20px;"><?php echo $error; ?></div>
  <?php endif; ?>
    </div>
    <div class="form">
      <div class="logo">
        <img class="ULogo" src="https://upload.wikimedia.org/wikipedia/en/thumb/1/18/University_of_Southeastern_Philippines_logo.png/250px-University_of_Southeastern_Philippines_logo.png"/>
      </div>
      <h2>Sign In</h2>
      <!-- Username: USeP_Team-->
      <form method="POST">
        <div class="input-group">
        <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
          <label>Username</label>
        </div>
        
        <div class="input-group">
          <input type="password" name="password" required value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
          <label>Password</label>
        </div>
        
        <button type="submit" class="submit-btn">Log In</button>
        
        <!-- <div class="links">
          <a href="#">Forgot Password?</a>
          <span style="color: rgba(255,255,255,0.5)">|</span>
          <a href="#">Create Account</a>
        </div> -->
        
        <!-- <div class="divider">
          <span>Or continue with</span>
        </div>
        
        <div class="social-login">
          <div class="social-icon">
            <i class="fab fa-google"></i>
          </div>
          <div class="social-icon">
            <i class="fab fa-facebook-f"></i>
          </div>
          <div class="social-icon">
            <i class="fab fa-apple"></i>
          </div> -->
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const shapesContainer = document.querySelector('.shapes');
      const numShapes = 5;
      
      // Create animated shapes
      for (let i = 0; i < numShapes; i++) {
        const shape = document.createElement('div');
        shape.className = 'shape';
        
        // Random position and size
        const size = Math.random() * 300 + 100;
        const left = Math.random() * 100;
        const top = Math.random() * 100;
        const duration = Math.random() * 10 + 10;
        const delay = Math.random() * 5;
        
        shape.style.width = `${size}px`;
        shape.style.height = `${size}px`;
        shape.style.left = `${left}%`;
        shape.style.top = `${top}%`;
        shape.style.animationDuration = `${duration}s`;
        shape.style.animationDelay = `${delay}s`;
        
        shapesContainer.appendChild(shape);
      }
      
      // Floating animation for logo
      const logo = document.querySelector('.logo span');
      
      // Focus label animation enhancement
      const inputs = document.querySelectorAll('.input-group input');
      inputs.forEach(input => {
        // Check initial state for autofilled inputs
        if (input.value.trim() !== '') {
          input.classList.add('has-value');
        }
        
        input.addEventListener('input', function() {
          if (this.value.trim() !== '') {
            this.classList.add('has-value');
          } else {
            this.classList.remove('has-value');
          }
        });
      });
    });
  </script>
</body>
</html>