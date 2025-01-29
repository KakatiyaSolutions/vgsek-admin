<?php
        $con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
        if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $department = isset($_GET['department']) ? $_GET['department'] : 'Default_Department'; // Replace 'Default_Department' with your default value if needed

    echo "The department name is: " . htmlspecialchars($department);  

    if (isset($_POST['sno'])) {
        $sno = $_POST['sno'];
        $description = $_POST['description'];
        $title = $_POST['title'];

        // Remove <figure> tags from the content
        $description = preg_replace('/<figure[^>]*>|<\/figure>/', '', $description);
        $title = preg_replace('/<figure[^>]*>|<\/figure>/', '', $title);

        // Add the classes "table table-bordered table-striped" to all <table> tags in the content
        function add_table_classes($content) {
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML('<div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // Wrap content to avoid errors

            $tables = $doc->getElementsByTagName('table');

            foreach ($tables as $table) {
                $classAttribute = $table->getAttribute('class');
                $classAttribute .= ' table table-bordered table-striped'; // Add the required classes
                $table->setAttribute('class', $classAttribute);
            }

            return $doc->saveHTML($doc->documentElement);
        }

        // Apply class modification to each content field
        $description = add_table_classes($description);
        $title = add_table_classes($title);

        // Sanitize data to prevent SQL injection
        $description = mysqli_real_escape_string($con, $description);
        $title = mysqli_real_escape_string($con, $title);

        // Update the database
        $query = "UPDATE faculty_event2 
                SET description = '$description', 
                    title = '$title'
                WHERE sno = '$sno'";

        if (mysqli_query($con, $query)) {
            echo "Record updated successfully.";
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    } else {
        echo "No ID received.";
     

    }
    if (!isset($_POST['sno']) && isset($_POST['title']) && isset($_POST['description'])) {
        $department = isset($_GET['department']) ? $_GET['department'] : 'Default_Department';
        $description = $_POST['description'];
        $title = $_POST['title'];
        echo
        // Sanitize and prepare data
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
// INSERT INTO faculty_event2 (department,department_name,status,title, description) VALUES ("1","DS","1","tatkaltikets","you will find before 24 hours of train starts near station");
        // Insert the new row into the database
        // $query = "INSERT INTO faculty_event2 (department,department_name,status,title, description) VALUES ('3','$department','1','$title', '$description')";
        // if (mysqli_query($con, $query)) {
        //     echo "New row added successfully.";
        // } else {
        //     echo "Error adding new row: " . mysqli_error($con);
        // }
        $query = "INSERT INTO faculty_event2 (department, department_name, status, title, description) 
        VALUES (?, ?, ?, ?, ?)";

// Prepare the statement
$stmt = mysqli_prepare($con, $query);

// Check if the preparation was successful
if ($stmt === false) {
  die("Error preparing statement: " . mysqli_error($con));
}

// Bind parameters to the prepared statement
// 's' for string, 'i' for integer
mysqli_stmt_bind_param($stmt, "ssiss", $department, $department_namet, $status, $title, $description);

// Set values for the placeholders
$department = '3';  // Example: replace with actual dynamic value
$status = 1;        // Example: replace with actual dynamic value (e.g., '1' for active status)

// Execute the prepared statement
if (mysqli_stmt_execute($stmt)) {
  echo "New row added successfully.";
} else {
  echo "Error adding new row: " . mysqli_stmt_error($stmt);
}



    }
    // Close the database connection
    mysqli_close($con);
?>