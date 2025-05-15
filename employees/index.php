<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 1) {
  header("Location: ../admin/index.php");
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
      body {
        font-family: "Arial";
      }
    </style>
    <title>Hello, world!</title>
  </head>
  <body onload="startTime()">
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="col-md-12">
        <h4 class="p-5">Welcome to the attendance system, <?php echo $_SESSION['username']; ?>!</h4>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-body p-5">
              <h4> Local Philippines Time: <span id="txt"></span></h4>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-body p-5">
              <h2>
                Attendance for today (
                <?php 
                $date = date('Y-m-d H:i:s'); 
                echo date('Y-m-d', strtotime($date));
                ?>
                )    
              </h2>
              <?php 
                // Fetch today's attendance record for the user
                $sql = "SELECT time_in, time_out FROM attendance_records WHERE user_id = ? AND date_added = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_SESSION['user_id'], date('Y-m-d', strtotime($date))]);
                $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <h4>
                TIME IN:
                <?php 
                if (!empty($attendance) && !empty($attendance['time_in'])) {
                  echo "<span class='badge badge-success'>" . htmlspecialchars(date('h:i A', strtotime($attendance['time_in']))) . "</span>";
                } else {
                  echo "<span style='color: red;'>No time in for today yet</span>";
                }
                ?>
              </h4>
              <h4>
                TIME OUT:
                <?php 
                if (!empty($attendance) && !empty($attendance['time_out'])) {
                  echo "<span class='badge badge-info'>" . htmlspecialchars(date('h:i A', strtotime($attendance['time_out']))) . "</span>";
                } else {
                  echo "<span style='color: red;'>No time out for today yet</span>";
                }
                ?>
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include 'includes/footer.php'; ?>
  </body>
</html>