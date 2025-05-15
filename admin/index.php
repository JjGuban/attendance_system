<?php 
require_once 'core/dbConfig.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 0) {
  header("Location: ../employees/index.php");
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
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="col-md-12">
        <h1 class="p-5">Welcome to the Admin Center!, <?php echo $_SESSION['username']; ?>!</h1>
      </div>
      <!-- Attendance Table Start -->
      <div class="card mb-5">
        <div class="card-header">
          <h4>Employee Attendance Records</h4>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead class="thead-dark">
              <tr>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Fetch attendance records using the new time_in and time_out columns
              $sql = "
                SELECT 
                  u.first_name, 
                  u.last_name, 
                  ar.date_added AS date,
                  ar.time_in,
                  ar.time_out
                FROM attendance_records ar
                JOIN attendance_system_users u ON ar.user_id = u.user_id
                ORDER BY ar.date_added DESC, u.last_name ASC, u.first_name ASC
              ";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();
              $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

              if ($rows && count($rows) > 0) {
                foreach($rows as $row) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                  echo "<td>" . ($row['time_in'] ? "<span class='badge badge-success'>" . htmlspecialchars(date('h:i A', strtotime($row['time_in']))) . "</span>" : "<span class='badge badge-danger'>No Time In</span>") . "</td>";
                  echo "<td>" . ($row['time_out'] ? "<span class='badge badge-info'>" . htmlspecialchars(date('h:i A', strtotime($row['time_out']))) . "</span>" : "<span class='badge badge-danger'>No Time Out</span>") . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='4' class='text-center'>No attendance records found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Attendance Table End -->
    </div>
    <?php include 'includes/footer.php'; ?>
  </body>
</html>