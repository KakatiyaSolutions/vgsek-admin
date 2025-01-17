<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get updated values from the form
$description = $_POST['description'];
$vision = $_POST['vision'];
$mission = $_POST['mission'];
$peo = $_POST['peo'];
$popso = $_POST['popso'];
$advisory = $_POST['advisory'];

// Remove <figure> tags from the content
$description = preg_replace('/<figure[^>]*>|<\/figure>/', '', $description);
$vision = preg_replace('/<figure[^>]*>|<\/figure>/', '', $vision);
$mission = preg_replace('/<figure[^>]*>|<\/figure>/', '', $mission);
$peo = preg_replace('/<figure[^>]*>|<\/figure>/', '', $peo);
$popso = preg_replace('/<figure[^>]*>|<\/figure>/', '', $popso);
$advisory = preg_replace('/<figure[^>]*>|<\/figure>/', '', $advisory);

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
$vision = add_table_classes($vision);
$mission = add_table_classes($mission);
$peo = add_table_classes($peo);
$popso = add_table_classes($popso);
$advisory = add_table_classes($advisory);

// Sanitize data to prevent SQL injection
$description = mysqli_real_escape_string($con, $description);
$vision = mysqli_real_escape_string($con, $vision);
$mission = mysqli_real_escape_string($con, $mission);
$peo = mysqli_real_escape_string($con, $peo);
$popso = mysqli_real_escape_string($con, $popso);
$advisory = mysqli_real_escape_string($con, $advisory);

// Update the database
$query = "UPDATE dep_about 
          SET description = '$description', 
              vision = '$vision', 
              mission = '$mission', 
              peo = '$peo', 
              po_pso = '$popso', 
              advisory_board = '$advisory' 
          WHERE department_name = 'CSE'";

if (mysqli_query($con, $query)) {
    echo "Record updated successfully.";
} else {
    echo "Error updating record: " . mysqli_error($con);
}

// Close the database connection
mysqli_close($con);
?>
