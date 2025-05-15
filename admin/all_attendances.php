<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
      body {
        font-family: "Arial";
      }
    </style>
    <title>All Attendances</title>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="col-md-12">
        <?php 
        // Get all unique dates from attendance_records
        $datesStmt = $pdo->query("SELECT DISTINCT date_added FROM attendance_records ORDER BY date_added DESC");
        $getAllDates = $datesStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php foreach ($getAllDates as $row) { ?>
        <div class="card shadow mt-4">
          <div class="card-header"><h2><?php echo $row['date_added']; ?></h2></div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Time In</th>
                    <th scope="col">Time Out</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $attStmt = $pdo->prepare("
                    SELECT u.first_name, u.last_name, ar.time_in, ar.time_out
                    FROM attendance_records ar
                    JOIN attendance_system_users u ON ar.user_id = u.user_id
                    WHERE ar.date_added = ?
                    ORDER BY u.last_name, u.first_name
                  ");
                  $attStmt->execute([$row['date_added']]);
                  $getAllAttendancesByDate = $attStmt->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($getAllAttendancesByDate as $innerRow) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($innerRow['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($innerRow['last_name']); ?></td>
                    <td>
                      <?php
                        if ($innerRow['time_in']) {
                          echo "<span class='badge badge-success'>" . htmlspecialchars(date('h:i A', strtotime($innerRow['time_in']))) . "</span>";
                        } else {
                          echo "<span class='badge badge-danger'>No Time In</span>";
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        if ($innerRow['time_out']) {
                          echo "<span class='badge badge-info'>" . htmlspecialchars(date('h:i A', strtotime($innerRow['time_out']))) . "</span>";
                        } else {
                          echo "<span class='badge badge-danger'>No Time Out</span>";
                        }
                      ?>
                    </td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </body>
</html>