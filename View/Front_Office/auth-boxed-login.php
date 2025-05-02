<?php
require_once 'C:\xampp\htdocs\projet_web\controller\UserC.php'; 
require_once 'C:\xampp\htdocs\projet_web\model\User.php'; // Include the User model

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start a session to store user login state
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = trim($_POST['inputEmailAddress'] ?? '');
    $password = trim($_POST['inputChoosePassword'] ?? '');
    $rememberMe = isset($_POST['flexSwitchCheckChecked']);

    // Basic server-side validation
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Instantiate UtilisateurC
            $utilisateurC = new UtilisateurC();

            // Check if the user exists by email
            $user = $utilisateurC->getUserByEmail($email);

            if ($user) {
                // Verify the password
                if (password_verify($password, $user['pwd'])) {
                    // Password is correct, set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_nom'] = $user['nom'];


                    // If "Remember Me" is checked, set a cookie (optional)
                    if ($rememberMe) {
                        $token = bin2hex(random_bytes(16)); // Generate a random token
                        setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), "/"); // 30 days
                        // You should also store this token in the database for validation
                    }

                    // Set success message
                    $success = "Login successful! Redirecting...";
                    if ($user['role'] == "Admin") {
                      // Redirect to a dashboard page (adjust the URL as needed)
                      header("Refresh: 2; URL=../Back_Office/index.php");
                    }else{
                      header("Refresh: 2; URL=../Front_Office/index.php");

                    }
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "No user found with this email.";
            }
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maxton | Bootstrap 5 Admin Dashboard Template</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet">
  <script src="assets/js/pace.min.js"></script>

  <!--plugins-->
  <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="assets/plugins/metismenu/mm-vertical.css">
  <!--bootstrap css-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/responsive.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-6px); }
      50% { transform: translateX(6px); }
      75% { transform: translateX(-6px); }
      100% { transform: translateX(0); }
    }
  
    .shake {
      animation: shake 0.3s;
    }
  
    .is-invalid {
      border-color: #dc3545 !important;
      box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
  
    .invalid-feedback {
      display: block !important;
    }
  </style>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <script>
    $(document).ready(function () {
      // Clear the password field on page load
      $('#inputChoosePassword').val('');

      // Toggle password show/hide
      $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        const input = $('#show_hide_password input');
        const icon = $('#show_hide_password i');
        if (input.attr("type") === "text") {
          input.attr('type', 'password');
          icon.addClass("bi-eye-slash-fill").removeClass("bi-eye-fill");
        } else {
          input.attr('type', 'text');
          icon.removeClass("bi-eye-slash-fill").addClass("bi-eye-fill");
        }
      });

      // Validation functions
      function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
      }

      function showError(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        $input.removeClass('shake');
        void $input[0].offsetWidth; // force reflow
        $input.addClass('shake');

        if (!$input.next('.invalid-feedback').length) {
          $input.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
          $input.next('.invalid-feedback').text(message);
        }
      }

      function clearError(input) {
        const $input = $(input);
        $input.removeClass('is-invalid shake');
        $input.next('.invalid-feedback').remove();
      }

      // Live validation on typing for Email
      $('#inputEmailAddress').on('input', function () {
        const email = $(this).val().trim();
        if (email === '') {
          clearError(this);  // Clear error if empty
        } else if (!email.includes('@')) {
          showError(this, "Missing @ symbol.");
        } else if (email.indexOf('@') !== email.lastIndexOf('@')) {
          showError(this, "Only one @ symbol is allowed.");
        } else if (email.split('@')[1].length < 1) {
          showError(this, "Missing characters after @.");
        } else if (email.split('@')[1] && !email.split('@')[1].includes('.')) {
          showError(this, "Missing domain extension (e.g., .com).");
        } else if (!validateEmail(email)) {
          showError(this, "Enter a valid email.");
        } else {
          clearError(this);  // Clear error if valid
        }
      });

      // Live validation on typing for Password
      $('#inputChoosePassword').on('input', function () {
        const password = $(this).val().trim();
        if (password === '') {
          clearError(this);  // Clear error if empty
        } else if (password.length < 6) {
          showError(this, "Minimum 6 characters.");
        } else {
          clearError(this);  // Clear error if valid
        }
      });

      // Initial validation on page load (no errors should be shown)
      $('#inputEmailAddress').trigger('input');
      $('#inputChoosePassword').trigger('input');

      // Show SweetAlert for success or error messages
      <?php if (!empty($success)): ?>
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: '<?php echo $success; ?>',
          showConfirmButton: false,
          timer: 2000
        });
      <?php elseif (!empty($error)): ?>
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: '<?php echo $error; ?>',
          confirmButtonText: 'OK'
        });
      <?php endif; ?>
    });
  </script>
</head>

<body>
  <!--authentication-->
  <div class="mx-3 mx-lg-0">
    <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
      <div class="row g-4">
        <div class="col-lg-6 d-flex">
          <div class="card-body">
            <img src="assets/images/logo1.png" class="mb-4" width="145" alt="">
            <h4 class="fw-bold">Get Started Now</h4>
            <p class="mb-0">Enter your credentials to login your account</p>
            <div class="row gy-2 gx-0 my-4">
              <div class="col-12 col-lg-12">
                <button class="btn btn-filter py-2 px-4 font-text1 fw-bold d-flex align-items-center justify-content-center w-100">
                  <span class=""><img src="assets/images/apps/05.png" width="20" class="me-2" alt="">Sign in with Google</span>
                </button>
              </div>
              <div class="col-12 col-lg-12">
                <button class="btn btn-filter py-2 px-4 font-text1 fw-bold d-flex align-items-center justify-content-center w-100">
                  <span class=""><img src="assets/images/apps/17.png" width="20" class="me-2" alt="">Sign in with Facebook</span>
                </button>
              </div>
              <div class="col-12 col-lg-12">
                <button class="btn btn-filter py-2 px-4 font-text1 fw-bold d-flex align-items-center justify-content-center w-100">
                  <span class=""><img src="assets/images/apps/18.png" width="20" class="me-2" alt="">Sign in with LinkedIn</span>
                </button>
              </div>
            </div>

            <div class="separator">
              <div class="line"></div>
              <p class="mb-0 fw-bold">OR SIGN IN WITH</p>
              <div class="line"></div>
            </div>
            <div class="form-body mt-4">
              <form class="row g-3" method="post" action="">
                <div class="col-12">
                  <label for="inputEmailAddress" class="form-label">Email</label>
                  <input type="email" class="form-control" id="inputEmailAddress" name="inputEmailAddress" placeholder="jhon@example.com">
                </div>
                <div class="col-12">
                  <label for="inputChoosePassword" class="form-label">Password</label>
                  <div class="input-group" id="show_hide_password">
                    <input type="password" class="form-control border-end-0" id="inputChoosePassword" name="inputChoosePassword" placeholder="Enter Password">
                    <a href="javascript:;" class="input-group-text bg-transparent"><i class="bi bi-eye-slash-fill"></i></a>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="flexSwitchCheckChecked" checked>
                    <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                  </div>
                </div>
                <div class="col-md-6 text-end">
                  <a href="auth-boxed-forgot-password.html">Forgot Password ?</a>
                </div>
                <div class="col-12">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-grd-primary">Login</button>
                  </div>
                </div>
                <div class="col-12">
                  <div class="text-start">
                    <p class="mb-0">Don't have an account yet? <a href="auth-boxed-register.php">Sign up here</a></p>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-6 d-lg-flex d-none">
          <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-grd-primary">
            <img src="assets/images/auth/login1.png" class="img-fluid" alt="">
          </div>
        </div>
      </div><!--end row-->
    </div>
  </div>
  <!--authentication-->
</body>
</html>