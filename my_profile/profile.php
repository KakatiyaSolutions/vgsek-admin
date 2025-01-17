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

// Check if we are receiving a GET request (to fetch data)
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve the data sent from JavaScript
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Extract the ID from the input
    $id = $data['id'] ?? null;

    if ($id) {
        // Query with WHERE condition
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
         
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['profile_picture'] = base64_encode($row['profile_picture']); // Encode image to Base64
            $data[] = $row;
            $password = $row['password'];
        }

        // Output JSON data (this should only run for data fetching)
        header('Content-Type: application/json');
        echo json_encode($data);
    // }
}

// Check if we are receiving a POST request (to update profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['fullname'], $_POST['idnumber'])) {
        // Get form data
        $id = (int)$_POST['idnumber']; // Ensure ID is treated as an integer
        $username = $_POST['fullname'];
        // $file_data = file_get_contents($_FILES['userfile']['tmp_name']); // Profile picture
    // echo "user id is ".$id;
    // echo "user name is".$username;
        // Prepare the SQL query
        $sql = "UPDATE users SET name = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
    
        if ($stmt === false) {
            echo "Error in preparing the query: " . $conn->error;
            exit();
        }
    
        $null = null;
        $stmt->bind_param("sbi", $username, $null, $id); // Bind parameters
        // $stmt->send_long_data(1, $file_data); // Bind profile picture as binary
    
        // Execute the query
        if ($stmt->execute()) {
            header("Location: profile.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    
        $stmt->close();
    }

    
    
// if (isset($_POST['fullname'], $_POST['idnumber'])) {
//     // Get form data
//     $id = (int)$_POST['idnumber2']; // Ensure ID is treated as an integer
//     $username = $_POST['fullname'];
//     // $file_data = file_get_contents($_FILES['userfile']['tmp_name']); // Profile picture

//     // Prepare the SQL query
//     $sql = "UPDATE users SET name = ?, profile_picture = ? WHERE id = ?";
//     $stmt = $conn->prepare($sql);

//     if ($stmt === false) {
//         echo "Error in preparing the query: " . $conn->error;
//         exit();
//     }

//     $null = null;
//     $stmt->bind_param("sbi", $username, $null, $id); // Bind parameters
//     // $stmt->send_long_data(1, $file_data); // Bind profile picture as binary

//     // Execute the query
//     if ($stmt->execute()) {
//         header("Location: profile.html");
//         exit();
//     } else {
//         echo "Error: " . $stmt->error;
//     }

//     $stmt->close();
// }



    // new if consition
    // if (isset($_POST['fullname'],$_POST['idnumber'])) {
    //     // $input = file_get_contents('php://input');
    //     // $data = json_decode($input, true);
    
    //     // Extract the ID from the input
    //     $id =$_POST['idnumber'];
    //     // Get form data
    //     $username = $_POST['fullname'];
    
    //     // Get the file data for the profile picture
    //     // $file_data = file_get_contents($_FILES['userfile']['tmp_name']);
    
    //     // Prepare the SQL query to update the user's profile
    //     $sql = "UPDATE users SET name = ? WHERE id = 1"; // Change ID dynamically if needed
    //     $stmt = $conn->prepare($sql);
    
    //     // Check if the prepare statement was successful
    //     if ($stmt === false) {
    //         echo "Error in preparing the query: " . $conn->error;
    //         exit();
    //     }
    
    //     // Bind the parameters (s = string, b = binary/blob)
    //     $stmt->bind_param("s", $username);  // Bind the username and the binary data for the profile picture
    
    //     // Send the binary data (profile picture)
    //     // $stmt->send_long_data(2, $file_data);  // '2' is the index for the second parameter (profile_picture)
    
    //     // Execute the query
    //     if ($stmt->execute()) {
    //         // Successfully updated, redirect to profile page
    //         header("Location: profile.html");
    //         exit;  // Ensure that the script exits after the redirect
    //     } else {
    //         // If there is an error, output it
    //         echo "Error: " . $stmt->error;
    //     }
    
    //     // Close the statement
    //     $stmt->close();
    // }
    // else {
    //     echo "No file uploaded or missing form data.";
    // }






    
}






if (isset($_POST['old_pass'], $_POST['new_pass'], $_POST['idnumber2'], $_POST['conf_pass'])) {
    // global $data;  // Make sure to access global $data

    // Re-fetch $data from the database to ensure it's always available
     $id = (int)$_POST['idnumber2'];  // Ensure ID is treated as an integer
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $conf_pass = $_POST['conf_pass'];
   
                  // Fetch current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        if(password_verify($old_pass  , $row['password'])) {
            if ($new_pass === $conf_pass) {
                // Update password in the database
                $new_pass_hashed = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_pass_hashed, $id);
                $stmt->execute();

                 echo "Password updated successfully";
                header("Location: profile.html");
                exit();

            } else {
                echo "New password and confirm password do not match.";
            }
        } else {
            echo "Old password is incorrect.";
            // echo "password is: ".$row['password'];
        }
    } else {
        echo "User not found.";
    }
                //  echo "actual mpass".$password;
    // Fetch current password from the database for verification
    // $query = "SELECT password FROM users WHERE id = ?";
    // $stmt = $conn->prepare($query);
    // $stmt->bind_param("i", $id);
    // $stmt->execute();
    // $stmt->bind_result($current_password);
    // $stmt->fetch();
    // $result2 = mysqli_query($conn,$query);

    // $query = "SELECT password FROM users WHERE id = $id";  // Directly include $id in the query

// Execute the query
// $result2 = mysqli_query($conn, $query);

// if ($result2) {
//     // Fetch the result as an associative array
//     $row = mysqli_fetch_assoc($result2);
    
//     if ($row) {
//         $current_password = $row['password'];  // Store the current password from the database
//         echo "old password: " . $old_pass;  // Displays the value of $old_pass
//         echo "current password: " . $current_password; 
//         // Verify old password
//         if ($old_pass !== $current_password) {
//             echo "Current password is incorrect!";
//             echo "old password: " . $old_pass;  // Displays the value of $old_pass
//             echo "current password: " . $current_password;  // Displays the value of $current_password
//             exit();
//         }
//     } else {
//         echo "No user found with that ID!";
//         exit();
//     }
// } else {
//     echo "Error: " . mysqli_error($conn);  // If the query fails, show the error
//     exit();
// }
    // echo "the result is :".$result2;
    // Verify old password
    // if ($old_pass !== $current_password) {
    //     echo "Current password is incorrect!";
    //     echo "old password: " . $old_pass;  // Displays the value of $old_pass
    //     echo "current password: " . $current_password;  // Displays the value of $current_password
    //     exit();
    // }

    // Check if new password and confirm password match
    // if ($new_pass !== $conf_pass) {
    //     echo "New password and confirm password do not match!";
    //     exit();
    // }

    // Update password
    // $update_sql = "UPDATE users SET password = ? WHERE id = ?";
    // $stmt = $conn->prepare($update_sql);
    // $stmt->bind_param("si", $new_pass, $id);
    // if ($stmt->execute()) {
    //     echo "Password updated successfully!";
    // } else {
    //     echo "Error updating password!";
    // }

    // $stmt->close();
}
// updating password
// if (isset($_POST['old_pass'], $_POST['new_pass'], $_POST['id'], $_POST['conf_pass'])) {
//     // Get form data
//     $id = (int)$_POST['idnumber']; // Ensure ID is treated as an integer
//     $username = $_POST['fullname'];
//     // $file_data = file_get_contents($_FILES['userfile']['tmp_name']); // Profile picture
//     $old_pass =$_POST['old_pass'];
//     $new_pass =$_POST['nwe_pass'] ;
//     $id =$_POST['id'];
//     // Prepare the SQL query
//     $sql = "SELECT password FROM users WHERE id = ?";
//     $stmt = $conn->prepare($sql);

//     if ($stmt === false) {
//         echo "Error in preparing the query: " . $conn->error;
//         exit();
//     }

//     $null = null;
//     $stmt->bind_param("i", $id); // Bind parameters
//     // $stmt->send_long_data(1, $file_data); // Bind profile picture as binary

//     // Execute the query
//     if ($stmt->execute()) {
//         echo "the result is +"+$stmt;
//         // header("Location: profile.html");
//         // exit();
//     } else {
//         echo "Error: " . $stmt->error;
//     }

//     $stmt->close();
// }

// updating th epassword new one

// if (isset($_POST['old_pass'], $_POST['new_pass'], $_POST['id'], $_POST['conf_pass'])) {
//     // Get form data
//     global $data;
//     if (!empty($data)) {
//         echo "<pre>";
//         print_r($data); // Display the $data array
//         echo "</pre>";
//     } else {
//         echo "No data found.";
//     }
//     $id = $_POST['id'];  // Ensure ID is treated as an integer
//     $old_pass = $_POST['old_pass'];
//     $new_pass = $_POST['new_pass'];
//     $conf_pass = $_POST['conf_pass']; 
//     // echo "the data"+$id+" "+$old_pass+" "+$new_pass+" "+$conf_pass; // Confirmed password
//     echo "The data: " . $id . " " . $old_pass . " " . $new_pass . " " . $conf_pass;
//     // echo $data;
//     // Check if new password and confirm password match
//     if ($new_pass !== $conf_pass) {
//         echo "New password and confirm password do not match!";
//         exit();
//     }

//     // Prepare the SQL query to select the user's current password
//     // $sql = "SELECT password FROM users WHERE id = 1";
//     // $stmt = $conn->prepare($sql);

//     // if ($stmt === false) {
//     //     echo "Error in preparing the query: " . $conn->error;
//     //     exit();
//     // }

//     // $stmt->bind_param("i", $id);  // Bind the user ID
//     // $stmt->execute();
//     // $stmt->store_result();
//     // $stmt->bind_result($stored_password);  // Variable to hold the fetched password

//     // Check if the user was found
//     // if ($stmt->num_rows > 0) {
//     //     $stmt->fetch();  // Fetch the stored password

//         // Verify the old password
//     //     if (password_verify($old_pass, $stored_password)) {
//     //         // Password match, update with new password
//     //         $hashed_new_pass = $new_pass;  // Hash new password

//     //         // Prepare the update query
//     //         $update_sql = "UPDATE users SET password = ? WHERE id = ?";
//     //         $update_stmt = $conn->prepare($update_sql);

//     //         if ($update_stmt === false) {
//     //             echo "Error in preparing the update query: " . $conn->error;
//     //             exit();
//     //         }

//     //         $update_stmt->bind_param("si", $hashed_new_pass, $id);  // Bind new password and user ID
//     //         if ($update_stmt->execute()) {
//     //             echo "Password updated successfully!";
//     //             // Redirect to profile page or wherever needed
//     //             header("Location: profile.html");
//     //             exit();
//     //         } else {
//     //             echo "Error updating password: " . $update_stmt->error;
//     //         }

//     //         $update_stmt->close();
//     //     } else {
//     //         echo "Old password is incorrect!";
//     //     }
//     // }
//     //  else {
//     //     echo "User not found!";
//     // }

//     // $stmt->close();
// }


// if (isset($_POST['old_pass'], $_POST['new_pass'], $_POST['id'], $_POST['conf_pass'])) {
//     // Debugging: Output POST data (Remove in production)
//     echo "<pre>";
//     print_r($_POST);
//     echo "</pre>";

//     // Get form data
//     $id = $_POST['id'];  // Ensure ID is treated as an integer
//     $old_pass = $_POST['old_pass'];
//     $new_pass = $_POST['new_pass'];
//     $conf_pass = $_POST['conf_pass'];  // Confirmed password

//     // Debugging: Validate received data
//     if (empty($id) || empty($old_pass) || empty($new_pass) || empty($conf_pass)) {
//         echo "Error: One or more fields are empty!";
//         exit();
//     }

//     // Check if new password and confirm password match
//     if ($new_pass !== $conf_pass) {
//         echo "Error: New password and confirm password do not match!";
//         exit();
//     }

//     // Debugging: Indicate passwords match
//     echo "Passwords match. Proceeding with verification...<br>";

//     // Prepare the SQL query to select the user's current password
//     $sql = "SELECT password FROM users WHERE id = ?";
//     $stmt = $conn->prepare($sql);

//     if ($stmt === false) {
//         echo "Error in preparing the query: " . $conn->error;
//         exit();
//     }

//     // Debugging: Query prepared successfully
//     echo "Query prepared successfully. Binding parameters...<br>";

//     $stmt->bind_param("i", $id);  // Bind the user ID
//     $stmt->execute();

//     // Debugging: Check execution status
//     if (!$stmt->execute()) {
//         echo "Error executing the query: " . $stmt->error;
//         exit();
//     }

//     $stmt->store_result();
//     $stmt->bind_result($stored_password);  // Variable to hold the fetched password

//     // Debugging: Check if user exists
//     if ($stmt->num_rows > 0) {
//         echo "User found. Fetching stored password...<br>";
//         $stmt->fetch();  // Fetch the stored password

//         // Debugging: Output stored password for verification (DO NOT use in production)
//         echo "Stored Password (hashed): $stored_password<br>";

//         // Verify the old password
//         if (password_verify($old_pass, $stored_password)) {
//             echo "Old password verified. Proceeding to update...<br>";

//             // Hash new password
//             $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

//             // Prepare the update query
//             $update_sql = "UPDATE users SET password = ? WHERE id = ?";
//             $update_stmt = $conn->prepare($update_sql);

//             if ($update_stmt === false) {
//                 echo "Error in preparing the update query: " . $conn->error;
//                 exit();
//             }

//             // Debugging: Update query prepared successfully
//             echo "Update query prepared successfully. Binding new password and user ID...<br>";

//             $update_stmt->bind_param("si", $hashed_new_pass, $id);

//             // Debugging: Execute update query
//             if ($update_stmt->execute()) {
//                 echo "Password updated successfully!<br>";
//                 // Redirect to profile page or wherever needed
//                 header("Location: profile.html");
//                 exit();
//             } else {
//                 echo "Error updating password: " . $update_stmt->error;
//             }

//             $update_stmt->close();
//         } else {
//             echo "Error: Old password is incorrect!";
//         }
//     } else {
//         echo "Error: User not found!";
//     }

//     $stmt->close();
// } else {
//     echo "Error: Required POST data is missing!";
// }



?>
