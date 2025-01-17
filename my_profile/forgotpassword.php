<?php
require '../vendor/autoload.php'; // Adjust the path as necessary

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');

// $conn = mysqli_connect('srv1328.hstgr.io', 'u629694569_carebackups', '8886767534Sr@', 'u629694569_carebackup');
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        $query = 'SELECT * FROM users WHERE username = ?';
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            header('Content-Type: application/json');
            echo json_encode($row);
        } else {
            header('Content-Type: application/json');
            echo json_encode([]); // Return an empty object
        }
        exit;
    }
    if (isset($_POST['new_pass']) && isset($_POST['id'])) {
        // $email = $_POST['email'];
        $new_pass = $_POST['new_pass'];
        $id = $_POST['id'];

        // Check if new password and confirmation password match
        // (we only need to check the password confirmation in the frontend)

        // Hash the new password
        $new_pass_hashed = password_hash($new_pass, PASSWORD_BCRYPT);

        // Update the user's password in the database
        $query = 'UPDATE users SET password = ? WHERE id = ?';
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'si', $new_pass_hashed,  $id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
        }

        mysqli_stmt_close($stmt);
        exit;
    }
    // if (isset($_POST['email']) && isset($_POST['new_pass']) && isset($_POST['conf_pass']) && isset($_POST['id'])) {
    //     $email = $_POST['email'];
    //     $new_pass = $_POST['new_pass'];
    //     $conf_pass = $_POST['conf_pass'];
    //     // echo "it's came";

    //     // Check if new password and confirmation password match
    //     if ($new_pass !== $conf_pass) {
    //         echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    //         exit;
    //     }

    //     // Hash the new password
    //     $hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);

    //     // Update the user's password in the database
    //     $query = 'UPDATE users SET password = ? WHERE username = ?';
    //     $stmt = mysqli_prepare($conn, $query);
    //     mysqli_stmt_bind_param($stmt, 'ss', $hashed_password, $email);

    //     if (mysqli_stmt_execute($stmt)) {
    //         echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);
    //     } else {
    //         echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
    //     }

    //     mysqli_stmt_close($stmt);
    //     exit;
    // }
}
