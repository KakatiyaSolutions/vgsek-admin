<?php
// Database connection settings
$servername = "srv1328.hstgr.io";  // Database server
$username = "u629694569_carehospital";         // Database username
$password = "Kakatiya1234$";             // Database password
$dbname = "u629694569_carediabetesce";  // Your database name // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user input from the form
$user = $_POST['username'];
$pass = $_POST['password'];

// Query to check the credentials
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists and the password matches
$response = array();

// if ($result->num_rows > 0) {
//     $userData = $result->fetch_assoc();
    
//     // If the password matches, return success
//     if (password_verify($pass, $userData['password'])) {
//         $response['success'] = true;
//     } else {
//         $response['success'] = false;
//     }
// } else {
//     $response['success'] = false;
// }
if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();

    // Direct password comparison (no hashing)
    // if ($pass === $userData['password']) {
        if(password_verify($pass , $userData['password'])){

        // If the password matches, return success
        $response['success'] = true;
        $response['id'] = $userData['id'];
    } else {
        // If the password doesn't match, return failure
        $response['success'] = false;
    }
} else {
    // If the username doesn't exist in the database
    $response['success'] = false;
}


// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>
