// <?php
// department_dropdown.php

//function generateDepartmentDropdown($selected_value = null) {
    // Database connection (replace with your own connection code)
   // $conn = new mysqli('host', 'username', 'password', 'database');
   // if ($conn->connect_error) {
   //     die("Connection failed: " . $conn->connect_error);
   // }

    // Query to get departments
   // $query = "SELECT d_name, department FROM departments WHERE d_value IS NOT NULL AND d_value != ''";
    //$result = $conn->query($query);

   // if ($result->num_rows > 0) {
        // Start the dropdown
       // echo '<select name="department" id="department">';
        
        // Default empty option
       // echo '<option value="">Select a Department</option>';

        // Loop through departments and create option tags
     //   while ($row = $result->fetch_assoc()) {
          //  $selected = ($row['department'] == $selected_value) ? ' selected' : '';
           // echo '<option value="' . $row['department'] . '" ' . $selected . '>' . $row['d_name'] . '</option>';
       // }
   //     echo '</select>';
   // } else {
        echo "No departments available.";
   // }

 //   $conn->close();
//}
//?> -->


//<?php
//session_start();
//$department= "";
// Check if a department was selected
//if (isset($_GET['department'])) {
   // $department = $_GET['department'];
   // echo "Selected Department: " . htmlspecialchars($department);
    // header("Location: index.html?department=" . urlencode($department));
    // exit();
    // Now you can use this $department variable to fetch or display department-specific data
//} else {
   // echo "No department selected.";
//}
////?>




<?php
session_start();

if (isset($_GET['department'])) {
    // Store the selected department in the session
    $_SESSION['department'] = $_GET['department'];

    // Optionally, send a response back to indicate success
    echo "Department set to: " . htmlspecialchars($_GET['department']);
}
?>
