<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];
		$isAdminStatusFromDB = $loginQuery['userInfoArray']['is_admin'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			$_SESSION['is_admin'] = $isAdminStatusFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['username']);
	header("Location: ../login.php");
}

if (isset($_POST['insertNewAttendanceBtn'])) {
    date_default_timezone_set('Asia/Manila');
    $user_id = $_SESSION['user_id'];
    $attendance_action = $_POST['attendance_type']; // 'time_in' or 'time_out'
    $date_today = $_POST['date_today'];
    $current_time = date('H:i:s');

    if (!empty($user_id) && !empty($attendance_action) && !empty($date_today)) {
        // Check if a record for today exists
        $sql = "SELECT * FROM attendance_records WHERE user_id = ? AND date_added = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $date_today]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($attendance_action == 'time_in') {
            if (!$record) {
                // Insert new row with time_in
                $sql = "INSERT INTO attendance_records (user_id, date_added, time_in) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $success = $stmt->execute([$user_id, $date_today, $current_time]);
            } else {
                $_SESSION['message'] = "You already timed in for today!";
                $_SESSION['status'] = '400';
                $success = false;
            }
        } elseif ($attendance_action == 'time_out') {
            if ($record && empty($record['time_out'])) {
                // Update existing row with time_out
                $sql = "UPDATE attendance_records SET time_out = ? WHERE user_id = ? AND date_added = ?";
                $stmt = $pdo->prepare($sql);
                $success = $stmt->execute([$current_time, $user_id, $date_today]);
            } elseif (!$record) {
                $_SESSION['message'] = "You must time in first!";
                $_SESSION['status'] = '400';
                $success = false;
            } else {
                $_SESSION['message'] = "You already timed out for today!";
                $_SESSION['status'] = '400';
                $success = false;
            }
        } else {
            $success = false;
        }

        if ($success) {
            $_SESSION['message'] = ucfirst(str_replace('_', ' ', $attendance_action)) . " successfully recorded!";
            $_SESSION['status'] = '200';
        } else if (!isset($_SESSION['message'])) {
            $_SESSION['message'] = "An error occurred with the query!";
            $_SESSION['status'] = '400';
        }
    } else {
        $_SESSION['message'] = "Make sure no input fields are empty!";
        $_SESSION['status'] = '400';
    }
    header("Location: ../file_an_attendance.php");
}


if (isset($_POST['editLeaveBtn'])) {
	$description = $_POST['description'];
	$date_start = $_POST['date_start'];
	$date_end = $_POST['date_end'];
	$leave_id = $_POST['leave_id'];
	updateLeaveDescription($pdo, $description, $date_start, $date_end, $leave_id);
}


if (isset($_POST['insertNewLeaveBtn'])) {
	$description = trim($_POST['description']);
	$user_id = $_SESSION['user_id'];
	$date_start = trim($_POST['date_start']);
	$date_end = trim($_POST['date_end']);

	if (insertNewLeave($pdo, $description, $user_id, $date_start, $date_end)) {
		$_SESSION['message'] = "Leave successfully saved!";
		$_SESSION['status'] = '200';

		// Notify all admins
		$admins = $pdo->query("SELECT user_id FROM attendance_system_users WHERE is_admin = 1")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($admins as $admin) {
			$stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
			$stmt->execute([$admin['user_id'], "A new leave has been filed by " . $_SESSION['username'] . "."]);
		}

		header("Location: ../file_a_leave.php");
	}
}
