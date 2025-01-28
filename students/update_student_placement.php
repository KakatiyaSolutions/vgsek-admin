<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$department = isset($_GET['department']) ? $_GET['department'] : 'Default_Department'; // Replace 'Default_Department' with your default value if needed

echo "The department name is: " . htmlspecialchars($department);  
// $department_name = $_POST['department_name'];
// Get updated values from the form
$description = $_POST['description'];
$vision = $_POST['vision'];
$mission = $_POST['mission'];
$peo = $_POST['peo'];
$popso = $_POST['popso'];
$advisory = $_POST['advisory'];
$po = $_POST['po'];
$achivements = $_POST['achivements'];
$board_of_studies = $_POST['board_of_studies'];
// $department_name = $_POST['department_name'];

// Remove <figure> tags from the content
$description = preg_replace('/<figure[^>]*>|<\/figure>/', '', $description);
$vision = preg_replace('/<figure[^>]*>|<\/figure>/', '', $vision);
$mission = preg_replace('/<figure[^>]*>|<\/figure>/', '', $mission);
$peo = preg_replace('/<figure[^>]*>|<\/figure>/', '', $peo);
$po = preg_replace('/<figure[^>]*>|<\/figure>/', '', $po);
$popso = preg_replace('/<figure[^>]*>|<\/figure>/', '', $popso);
$advisory = preg_replace('/<figure[^>]*>|<\/figure>/', '', $advisory);
$achivements = preg_replace('/<figure[^>]*>|<\/figure>/', '', $achivements);
$board_of_studies = preg_replace('/<figure[^>]*>|<\/figure>/', '', $board_of_studies);

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
$po = add_table_classes($po);
$advisory = add_table_classes($advisory);
$achivements = add_table_classes($achivements);
$board_of_studies = add_table_classes($board_of_studies);

// Sanitize data to prevent SQL injection
$description = mysqli_real_escape_string($con, $description);
$vision = mysqli_real_escape_string($con, $vision);
$mission = mysqli_real_escape_string($con, $mission);
$peo = mysqli_real_escape_string($con, $peo);
$po = mysqli_real_escape_string($con, $po);
$popso = mysqli_real_escape_string($con, $popso);
$advisory = mysqli_real_escape_string($con, $advisory);
$achivements = mysqli_real_escape_string($con, $achivements);
$board_of_studies = mysqli_real_escape_string($con, $board_of_studies);
// Update the database
$query = "UPDATE student_placements 
          SET description = '$description', 
              vision = '$vision', 
              mission = '$mission', 
              peo = '$peo', 
              po = '$po', 
              po_pso = '$popso',
              board_of_studies = '$board_of_studies', 
              advisory_board = '$advisory',
              achivements = '$achivements'
          WHERE department_name = '$department'";
        // $query = "SELECT * FROM dep_about WHERE department_name = '$department'";
        // $result = mysqli_query($con, $query);


        // if ($result) {
        //     // Check if rows are returned
        //     if (mysqli_num_rows($result) > 0) {
        //         // Fetch data and echo it
        //         while ($row = mysqli_fetch_assoc($result)) {
        //             echo "Description: " . htmlspecialchars($row['description']) . "<br>";
        //             echo "Vision: " . htmlspecialchars($row['vision']) . "<br>";
        //             echo "Mission: " . htmlspecialchars($row['mission']) . "<br>";
        //             echo "PEO: " . htmlspecialchars($row['peo']) . "<br>";
        //             echo "PO: " . htmlspecialchars($row['po']) . "<br>";
        //             echo "PO PSO: " . htmlspecialchars($row['po_pso']) . "<br>";
        //             echo "Advisory: " . htmlspecialchars($row['advisory_board']) . "<br>";
        //         }
        //     } else {
        //         echo "No records found.";
        //     }
        // } else {
        //     echo "Error executing query: " . mysqli_error($con);
        // }
if (mysqli_query($con, $query)) {
    echo "Record updated successfully.";
} else {
    echo "Error updating record: " . mysqli_error($con);
}

// Close the database connection
mysqli_close($con);
?>