<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QUALITEES | Log In</title>
  <link rel="icon" href="./media/icon.png" type="image/png">

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    @import url('./media/stardom.css');

    body {
      background-color: #ffffff;
      font-family: "Poppins", sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Login form */
    .login-form {
      width: 100%;
      max-width: 420px;
      margin-top: 5rem;
    }

    .form-control {
      border-radius: 8px;
      padding: 0.75rem;
      font-size: 1rem;
    }

    .form-control:focus {
      border-color: #b33939;
      box-shadow: 0 0 0 0.2rem rgba(179, 57, 57, 0.25);
    }



    .btn-login {
      background-color: #b33939 !important;
      color: #fff !important;
      border-radius: 8px;
      padding: 0.75rem;
      width: 100%;
      transition: background 0.3s;
      margin-top: 0.5rem;
    }

    .btn-login:hover {
      background-color: #8e2929 !important;
    }

    .extra-links {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .extra-links a {
      color: #b33939;
      text-decoration: none;
    }

    .extra-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <?php include './headerLR.php'; ?>

  <main class="login-form">
    <h2 style="font-family: 'Stardom-Regular'; font-size: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
      Log in to your account
    </h2>
    <form id="loginForm" method="POST" action="login_process.php">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="mb-3 password-wrapper">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
          <span class="input-group-text bg-transparent border-start-0">
            <i class="bi bi-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
          </span>
        </div>
      </div>

      <button type="submit" class="btn btn-login">Continue</button>
    </form>

    <div class="extra-links">
      <p>Don’t have an account? <a href="./register.php">Sign up</a></p>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Password toggle
    $('#togglePassword').on('click', function() {
      const passwordInput = $('#password');
      const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
      passwordInput.attr('type', type);
      $(this).toggleClass('bi-eye-slash bi-eye');
    });
  </script>
</body>

</html>