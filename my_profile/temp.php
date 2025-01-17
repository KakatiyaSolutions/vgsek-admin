<?php
$data = [];
$password ="";
// Database connection
// $conn = mysqli_connect('srv1328.hstgr.io', 'u629694569_carebackups', '8886767534Sr@', 'u629694569_carebackup');
$conn = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$id = 3;
$new_pass = '1q2w3e4r5tSr@';
$new_pass_hashed = password_hash($new_pass, algo: PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $new_pass_hashed, $id);
$stmt->execute();

  echo "Password updated successfully";
// Check if we are receiving a GET request (to fetch data)
//
?>