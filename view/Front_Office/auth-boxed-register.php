<?php
require_once 'C:\xampp\htdocs\projet_web\controller\UserC.php'; // Include the controller
require_once 'C:\xampp\htdocs\projet_web\model\User.php'; // Include the User model

ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve form data
  $prenom = trim($_POST["prenom"]);
  $nom = trim($_POST["nom"]);
  $age = intval($_POST["age"]);
  $phone = trim($_POST["phone"]);
  $email = trim($_POST['inputEmailAddress'] ?? '');
  $password = trim($_POST['inputChoosePassword'] ?? '');
  $country = trim($_POST['inputSelectCountry'] ?? '');
  $termsAccepted = isset($_POST['flexSwitchCheckChecked']);

  // Basic server-side validation
  if (empty($prenom) || empty($nom)  ||  empty($age) || empty($phone) || empty($email) || empty($password) || empty($country)) {
      $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Invalid email format.";
  } elseif (strlen($password) < 6) {
      $error = "Password must be at least 6 characters.";
  } elseif (!$termsAccepted) {
      $error = "You must agree to the Terms & Conditions.";
  } else {
      try {
          // Create a new Utilisateur object
          $utilisateur = new Utilisateur(
              $prenom, // nom
              $nom, // prenom (using username as a placeholder)
              $age, // age (default value)
              $phone, // tel (default value)
              'user', // role (default role)
              $email,
              password_hash($password, PASSWORD_DEFAULT) // Hash the password
          );

          // Instantiate UtilisateurC and call addUser
          $utilisateurC = new UtilisateurC();
          $result = $utilisateurC->addUser($utilisateur);

          if ($result) {
              $success = "Registration successful! You can now <a href='auth-boxed-login.html'>log in</a>.";
              echo $success;
          } else {
              throw new Exception("Registration failed. Please try again.");
          }
      } catch (Exception $e) {
          $error = "An error occurred: " . $e->getMessage();
          echo $error;
      }
  }

  // Display any error message if set
  if (!empty($error)) {
      echo $error;
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
    // Ensure jQuery is loaded
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
    }

    // Password visibility toggle
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

    // Email validation
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showError(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        $input.removeClass('shake');
        void $input[0].offsetWidth;
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

    $('#inputEmailAddress').on('input', function () {
        const email = $(this).val().trim();
        if (email === '') {
            clearError(this);
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
            clearError(this);
        }
    });

    // Password validation
    $('#inputChoosePassword').on('input', function () {
        const password = $(this).val().trim();
        if (password === '') {
            clearError(this);
        } else if (password.length < 6) {
            showError(this, "Minimum 6 characters.");
        } else {
            clearError(this);
        }
    });

    // First Name and Last Name validation
    $('#inputFirstName, #inputLastName').on('input', function () {
        const val = $(this).val().trim();
        if (val.length < 3) {
            showError(this, "3 characters minimum");
        } else {
            clearError(this);
        }
    });

    // Age validation
    $('#inputAge').on('input', function () {
        const age = parseInt($(this).val());
        if (isNaN(age) || age < 18) {
            showError(this, "Enter a valid age (18 or above)");
        } else {
            clearError(this);
        }
    });

    // Phone validation
    $('#inputPhone').on('input', function () {
        const phone = $(this).val().trim();
        if (phone.length !== 8) {
            showError(this, "Phone number must be 8 digits.");
        } else {
            clearError(this);
        }
    });
});
  </script>
</head>

<body>
  <!--authentication-->
  <div class="mx-3 mx-lg-0">
    <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden border-3 p-4">
      <div class="row g-4">
        <div class="col-lg-6 d-flex">
          <div class="card-body">
            <img src="assets/images/logo1.png" class="mb-4" width="145" alt="">
            <h4 class="fw-bold">Get Started Now</h4>
            <p class="mb-0">Enter your credentials to login your account</p>
            <div class="row gy-2 gx-0 my-4">
              <div class="col-12 col-lg-12">
                <button class="btn btn-filter py-2 px-4 font-text1 fw-bold d-flex align-items-center justify-content-center w-100">
                  <span class=""><img src="assets/images/apps/05.png" width="20" class="me-2" alt="">Sign up with Google</span>
                </button>
              </div>
              <div class="col-12 col-lg-12">
                <button class="btn btn-filter py-2 px-4 font-text1 fw-bold d-flex align-items-center justify-content-center w-100">
                  <span class=""><img src="assets/images/apps/17.png" width="20" class="me-2" alt="">Sign up with Facebook</span>
                </button>
              </div>
              <div class="col-12 col-lg-12">
                <button class="btn btn-filter py-2 px-4 font-text1 fw-bold d-flex align-items-center justify-content-center w-100">
                  <span class=""><img src="assets/images/apps/18.png" width="20" class="me-2" alt="">Sign up with LinkedIn</span>
                </button>
              </div>
            </div>
            <div class="separator">
              <div class="line"></div>
              <p class="mb-0 fw-bold">OR SIGN UP WITH</p>
              <div class="line"></div>
            </div>
            <div class="form-body mt-4">
              <!-- Display success or error messages -->
              <div class="col-12">
                <?php if ($error): ?>
                  <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                  <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
              </div>
              <!-- Update the form to use POST method -->
              <form class="row g-3" method="post" action="">
              <div class="col-12">
                <label for="inputFirstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="inputFirstName" name="prenom" placeholder="John">
              </div>

               
                <div class="col-12">
                  <label for="inputLastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="inputLastName" name="nom" placeholder="Doe">
                </div>

                <div class="col-12">
                  <label for="inputAge" class="form-label">Age</label>
                  <input type="number" class="form-control" id="inputAge" name="age" placeholder="18">
                </div>

                
                <div class="col-12">
                  <label for="inputPhone" class="form-label">Phone Number</label>
                  <input type="text" class="form-control" id="inputPhone" name="phone" placeholder="+21612345678">
                </div>

                <div class="col-12">
                  <label for="inputEmailAddress" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="inputEmailAddress" name="inputEmailAddress" placeholder="example@user.com">
                </div>
                <div class="col-12">
                  <label for="inputChoosePassword" class="form-label">Password</label>
                  <div class="input-group" id="show_hide_password">
                    <input type="password" class="form-control border-end-0" id="inputChoosePassword" name="inputChoosePassword" placeholder="Enter Password"> 
                    <a href="javascript:;" class="input-group-text bg-transparent"><i class="bi bi-eye-slash-fill"></i></a>
                  </div>
                </div>

                <div class="col-12">
                  <label for="inputSelectCountry" class="form-label">Country</label>
                  <select class="form-select" id="inputSelectCountry" name="inputSelectCountry" aria-label="Default select example">
                    <option value="India">India</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="America">America</option>
                    <option value="Dubai">Dubai</option>
                  </select>
                </div>

                <div class="col-12">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="flexSwitchCheckChecked">
                    <label class="form-check-label" for="flexSwitchCheckChecked">I read and agree to Terms & Conditions</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-grd-info">Register</button>
                  </div>
                </div>
                <div class="col-12">
                  <div class="text-start">
                    <p class="mb-0">Already have an account? <a href="auth-boxed-login.php">Sign in here</a></p>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-6 d-lg-flex d-none">
          <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-grd-info">
            <img src="assets/images/auth/register1.png" class="img-fluid" alt="">
          </div>
        </div>
      </div><!--end row-->
    </div>
  </div>
  <!--authentication-->

  <!--plugins-->
  <script src="assets/js/jquery.min.js"></script>
  

  <script>
    $(document).ready(function () {
      $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        if ($('#show_hide_password input').attr("type") == "text") {
          $('#show_hide_password input').attr('type', 'password');
          $('#show_hide_password i').addClass("bi-eye-slash-fill");
          $('#show_hide_password i').removeClass("bi-eye-fill");
        } else if ($('#show_hide_password input').attr("type") == "password") {
          $('#show_hide_password input').attr('type', 'text');
          $('#show_hide_password i').removeClass("bi-eye-slash-fill");
          $('#show_hide_password i').addClass("bi-eye-fill");
        }
      });
    });
  </script>
</body>
</html>