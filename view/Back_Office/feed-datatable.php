<?php
require_once 'C:\xampp\htdocs\projet_webww\config.php';
require_once 'C:\xampp\htdocs\projet_webww\controller\PostController.php';

session_start();
$db = config::getConnexion();
$postC = new PostController($db);
$posts = $postC->getPosts();
?>
<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Posts Management</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet">
  <script src="assets/js/pace.min.js"></script>

  <!--plugins-->
  <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="assets/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="assets/plugins/simplebar/css/simplebar.css">
  <!--bootstrap css-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/semi-dark.css" rel="stylesheet">
  <link href="sass/bordered-theme.css" rel="stylesheet">
  <link href="sass/responsive.css" rel="stylesheet">
</head>

<body>
  <!--start header-->
  <header class="top-header">
    <!-- ... existing header code ... -->
  </header>
  <!--end top header-->

  <!--start sidebar-->
  <aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
      <div class="logo-icon">
        <img src="assets/images/logo-icon.png" class="logo-img" alt="">
      </div>
      <div class="logo-name flex-grow-1">
        <h5 class="mb-0">Maxton</h5>
      </div>
      <div class="sidebar-close">
        <span class="material-icons-outlined">close</span>
      </div>
    </div>
    <div class="sidebar-nav">
      <!--navigation-->
      <ul class="metismenu" id="sidenav">
        <li>
          <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class="material-icons-outlined">home</i>
            </div>
            <div class="menu-title">Dashboard</div>
          </a>
          <ul>
            <li><a href="index.html"><i class="material-icons-outlined">arrow_right</i>Analysis</a>
            </li>
          </ul>
        </li>
        <li>
          <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class="material-icons-outlined">widgets</i>
            </div>
            <div class="menu-title">Widgets</div>
          </a>
          <ul>
            <li><a href="widgets-data.html"><i class="material-icons-outlined">arrow_right</i>Data</a>
            </li>
            <li><a href="widgets-static.html"><i class="material-icons-outlined">arrow_right</i>Static</a>
            </li>
          </ul>
        </li>
        <li>
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon"><i class="material-icons-outlined">apps</i>
            </div>
            <div class="menu-title">Apps</div>
          </a>
          <ul>
            <li><a href="app-emailbox.html"><i class="material-icons-outlined">arrow_right</i>Email Box</a>
            </li>
            <li><a href="app-emailread.html"><i class="material-icons-outlined">arrow_right</i>Email Read</a>
            </li>
            <li><a href="app-chat-box.html"><i class="material-icons-outlined">arrow_right</i>Chat</a>
            </li>
          </ul>
        </li>
        <li class="menu-label">UI Elements</li>
        <li>
          <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class="material-icons-outlined">shopping_bag</i>
            </div>
            <div class="menu-title">Service Management</div>
          </a>
          <ul>
            <li><a href="add-quizz.html"><i class="material-icons-outlined">arrow_right</i>Add Quizz</a>
            </li>
            <li><a href="add-course.html"><i class="material-icons-outlined">arrow_right</i>Add Course</a>
            </li>
            <li><a href="add-event.html"><i class="material-icons-outlined">arrow_right</i>Add Event</a>
            </li>
            <li><a href="add-post.html"><i class="material-icons-outlined">arrow_right</i>Add Post</a>
            </li>
            <li><a href="ecommerce-customers.html"><i class="material-icons-outlined">arrow_right</i>users</a>
            </li>
            <li><a href="ecommerce-orders.html"><i class="material-icons-outlined">arrow_right</i>Orders</a>
            </li>
          </ul>     
        </li>
        <li>
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon"><i class="material-icons-outlined">api</i>
            </div>
            <div class="menu-title">Tables</div>
          </a>
          <ul>
            <li><a href="user-datatable.html"><i class="material-icons-outlined">arrow_right</i>user</a>
            </li>
            <li><a href="event-datatable.html"><i class="material-icons-outlined">arrow_right</i>event</a>
            </li>
            <li><a href="feed-datatable.html"><i class="material-icons-outlined">arrow_right</i>feed</a>
            </li>
            <li><a href="course-datatable.html"><i class="material-icons-outlined">arrow_right</i>course</a>
            </li>
            <li><a href="quizz-datatable.html"><i class="material-icons-outlined">arrow_right</i>quizz</a>
            </li>
            <li><a href="reclamation-datatable.html"><i class="material-icons-outlined">arrow_right</i>reclamation</a>
            </li>
          </ul>
        </li>
        <li class="menu-label">Charts & Maps</li>
        <li>
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon"><i class="material-icons-outlined">fitbit</i>
            </div>
            <div class="menu-title">Charts</div>
          </a>
          <ul>
            <li><a href="charts-apex-chart.html"><i class="material-icons-outlined">arrow_right</i>Apex</a>
            </li>
            <li><a href="charts-chartjs.html"><i class="material-icons-outlined">arrow_right</i>Chartjs</a>
            </li>   
          </ul>
        </li>
        <li>
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon"><i class="material-icons-outlined">sports_football</i>
            </div>
            <div class="menu-title">Maps</div>
          </a>
          <ul>
            <li><a href="map-google-maps.html"><i class="material-icons-outlined">arrow_right</i>Google Maps</a>
            </li>
            <li><a href="map-vector-maps.html"><i class="material-icons-outlined">arrow_right</i>Vector Maps</a>
            </li>
          </ul>
        </li>
      </ul>
      <!--end navigation-->
    </div>
  </aside>
  <!--end sidebar-->

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Posts Management</div>
        <div class="ps-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
              <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
              <li class="breadcrumb-item active" aria-current="page">Posts List</li>
            </ol>
          </nav>
        </div>
        <div class="ms-auto">
          <a href="add-post.php" class="btn btn-primary">Add New Post</a>
        </div>
      </div>
      <!--end breadcrumb-->

      <?php if (isset($_SESSION['delete_message'])): ?>
        <div class="alert alert-info">
          <?php echo $_SESSION['delete_message']; unset($_SESSION['delete_message']); ?>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Description</th>
                  <th>Media</th>
                  <th>Type</th>
                  <th>Visibility</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($posts as $post) { ?>
                <tr>
                  <td><?php echo $post['postid']; ?></td>
                  <td><?php echo htmlspecialchars($post['description']); ?></td>
                  <td>
                    <?php if (!empty($post['media'])): ?>
                      <?php if ($post['posttype'] === 'image'): ?>
                        <img src="data:<?php echo htmlspecialchars($post['media_type']); ?>;base64,<?php echo base64_encode($post['media']); ?>" width="60" alt="Image">
                      <?php elseif ($post['posttype'] === 'video'): ?>
                        <span>🎬 Video</span>
                      <?php else: ?>
                        <span>Has Media</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span>No Media</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($post['posttype']); ?></td>
                  <td><?php echo htmlspecialchars($post['visibility']); ?></td>
                  <td>
                    <form method="POST" action="delete-post.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                      <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['postid']); ?>">
                      <button type="submit" name="delete_post" class="btn btn-danger btn-sm">
                        <i class="material-icons-outlined">delete</i>
                      </button>
                    </form>
                    <a href="edit-post.php?id=<?php echo htmlspecialchars($post['postid']); ?>" class="btn btn-primary btn-sm">
                      <i class="material-icons-outlined">edit</i>
                    </a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
  <!--end main wrapper-->

  <!--start overlay-->
  <div class="overlay btn-toggle"></div>
  <!--end overlay-->

  <!--start footer-->
  <footer class="page-footer">
    <p class="mb-0">Copyright © 2024. All right reserved.</p>
  </footer>
  <!--end footer-->

  <!--bootstrap js-->
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <!--plugins-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="assets/plugins/metismenu/metisMenu.min.js"></script>
  <script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
  <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    $(document).ready(function() {
      $('#example').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'print']
      });
    });
  </script>
</body>
</html>