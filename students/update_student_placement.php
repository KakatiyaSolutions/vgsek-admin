<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($con, 'utf8mb4');
if (!mysqli_set_charset($con, 'utf8mb4')) {
    die("Error setting charset: " . mysqli_error($con));
}
$department = isset($_GET['department']) ? $_GET['department'] : 'Default_Department'; // Replace with your default value

echo "The department name is: " . htmlspecialchars($department);  

// Function to add classes to <table> tags
function add_table_classes($content) {
    // $doc = new DOMDocument();
    // libxml_use_internal_errors(true);
    // $doc->loadHTML('<div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // Wrap content to avoid errors
    
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    // Enforce UTF-8
    $content = '<?xml encoding="UTF-8"><div>' . $content . '</div>';
    $doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);


    $tables = $doc->getElementsByTagName('table');

    foreach ($tables as $table) {
        $classAttribute = $table->getAttribute('class');
        $classAttribute .= ' table table-bordered table-striped'; // Add the required classes
        $table->setAttribute('class', $classAttribute);
    }
    // Add target="_blank" to all <a> links in the content
    $links = $doc->getElementsByTagName('a');
    foreach ($links as $link) {
        $link->setAttribute('target', '_blank');
    }
    return $doc->saveHTML($doc->documentElement);
}

// Check if sno (ID) is provided for updating an existing record
if (isset($_POST['sno'])) {
    $sno = $_POST['sno'];
    $description = $_POST['description'];
    $title = $_POST['title'];

    // Remove <figure> tags from the content
    $description = preg_replace('/<figure[^>]*>|<\/figure>/', '', $description);
    $title = preg_replace('/<figure[^>]*>|<\/figure>/', '', $title);

    // Apply table classes to description and title
    $description = add_table_classes($description);
    $title = add_table_classes($title);

    // Sanitize data to prevent SQL injection
    $description = mysqli_real_escape_string($con, $description);
    $title = mysqli_real_escape_string($con, $title);

    $description = str_replace(['&lt;', '&gt;'], ['<', '>'], $description);
    // Update the database for the existing record
    $query = "UPDATE student_placements2 
              SET description = '$description', 
                  title = '$title'
              WHERE sno = '$sno'";

    if (mysqli_query($con, $query)) {
        echo "Record updated successfully.";
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
} 
// If sno is not provided, insert a new record
else if (isset($_POST['title']) && isset($_POST['description'])) {
    $description = $_POST['description'];
    $title = $_POST['title'];

    // Apply table classes to description and title
    $description = add_table_classes($description);
    $title = add_table_classes($title);

    // Sanitize and prepare data
    $title = mysqli_real_escape_string($con, $title);
    $description = mysqli_real_escape_string($con, $description);


    // $description = REPLACE(REPLACE(description, '&lt;', '<'), '&gt;', '>');
    $description = str_replace(['&lt;', '&gt;'], ['<', '>'], $description);
    // Insert a new record into the database
    $query = "INSERT INTO student_placements2 (department, department_name, status, title, description) 
              VALUES ('$department', '$department', '1', '$title', '$description')";

    if (mysqli_query($con, $query)) {
        echo "New record added successfully.";
    } else {
        echo "Error adding new record: " . mysqli_error($con);
    }
} else {
    echo "No data received for update or insert.";
}

// Close the database connection
mysqli_close($con);
?>
