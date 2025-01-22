<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? $_GET['id'] : 'Default_lab_name'; // Replace 'Default_lab_name' with your default value if needed

echo "The id name is: " . htmlspecialchars($id);  
// $lab_name_name = $_POST['lab_name_name'];
// Get updated values from the form
$branch_name = $_POST['branch_name'];
$lab_name = $_POST['lab_name'];
$configuration_details = $_POST['configuration_details'];
$course = $_POST['course'];
$number_of_systems = $_POST['number_of_systems'];
$lab_details = $_POST['lab_details'];
$laboratories = $_POST['laboratories'];
$syllabus = $_POST['syllabus'];
$timetable = $_POST['timetable'];
$academic_calendar = $_POST['academic_calendar'];
// $lab_name_name = $_POST['lab_name_name'];

// Remove <figure> tags from the content
$branch_name = preg_replace('/<figure[^>]*>|<\/figure>/', '', $branch_name);
$lab_name = preg_replace('/<figure[^>]*>|<\/figure>/', '', $lab_name);
$configuration_details = preg_replace('/<figure[^>]*>|<\/figure>/', '', $configuration_details);
$course = preg_replace('/<figure[^>]*>|<\/figure>/', '', $course);
$laboratories = preg_replace('/<figure[^>]*>|<\/figure>/', '', $laboratories);
$number_of_systems = preg_replace('/<figure[^>]*>|<\/figure>/', '', $number_of_systems);
$lab_details = preg_replace('/<figure[^>]*>|<\/figure>/', '', $lab_details);
$syllabus = preg_replace('/<figure[^>]*>|<\/figure>/', '', $syllabus);
$timetable = preg_replace('/<figure[^>]*>|<\/figure>/', '', $timetable);
$academic_calendar = preg_replace('/<figure[^>]*>|<\/figure>/', '', $academic_calendar);

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

//  for adding styles to heading and paragraph tags

function add_styles_to_tags($content) {
    // Add inline styles to headings
    $content = preg_replace('/<h1>/i', '<h1 style="font-size: 24px; color: #333;">', $content);
    $content = preg_replace('/<h2>/i', '<h2 style="font-size: 22px; color: #444;">', $content);
    $content = preg_replace('/<h3>/i', '<h3 style="font-size: 25px;
    color: var(--blue);
    margin-bottom: 10px;
    font-weight: 900;
    border-bottom: solid 2px var(--blue);
    padding-bottom: 5px;
    width: fit-content;">', $content);
    $content = preg_replace('/<h4>/i', '<h4 style="font-size: 18px; color: #666;">', $content);
    $content = preg_replace('/<h5>/i', '<h5 style="font-size: 16px; color: #777;">', $content);
    $content = preg_replace('/<h6>/i', '<h6 style="text-decoration: underline;
    font-weight: bold;
    font-size: 18px;
    line-height: 40px;
    color: var(--orange);">', $content);

    // Add inline style to paragraph tags
    $content = preg_replace('/<p>/i', '<p style="font-size: 16px; color: #333;">', $content);

    return $content;
}

// Apply class modification to each content field
$branch_name = add_table_classes($branch_name);
$lab_name = add_table_classes($lab_name);
$configuration_details = add_table_classes($configuration_details);
$course = add_table_classes($course);
$number_of_systems = add_table_classes($number_of_systems);
$laboratories = add_table_classes($laboratories);
$lab_details = add_styles_to_tags($lab_details);
$syllabus = add_table_classes($syllabus);
$timetable = add_table_classes($timetable);
$academic_calendar = add_table_classes($academic_calendar);

// Sanitize data to prevent SQL injection
$branch_name = mysqli_real_escape_string($con, $branch_name);
$lab_name = mysqli_real_escape_string($con, $lab_name);
$configuration_details = mysqli_real_escape_string($con, $configuration_details);
$course = mysqli_real_escape_string($con, $course);
$laboratories = mysqli_real_escape_string($con, $laboratories);
$number_of_systems = mysqli_real_escape_string($con, $number_of_systems);
$lab_details = mysqli_real_escape_string($con, $lab_details);
$syllabus = mysqli_real_escape_string($con, $syllabus);
$timetable = mysqli_real_escape_string($con, $timetable);
$academic_calendar = mysqli_real_escape_string($con, $academic_calendar);


// Update the database
$query = "UPDATE dep_mtech 
          SET configuration_details = '$configuration_details', 
              course = '$course', 
              laboratories = '$laboratories', 
              number_of_systems = '$number_of_systems',
              timetable = '$timetable', 
              lab_details = '$lab_details',
              syllabus = '$syllabus',
              academic_calendar = '$academic_calendar'
        WHERE id ='$id'";
         //   WHERE branch_name = '$department'";
        // $query = "SELECT * FROM dep_about WHERE department_name = '$department'";
        // $result = mysqli_query($con, $query);


        // if ($result) {
        //     // Check if rows are returned
        //     if (mysqli_num_rows($result) > 0) {
        //         // Fetch data and echo it
        //         while ($row = mysqli_fetch_assoc($result)) {
        //             echo "branch_name: " . htmlspecialchars($row['branch_name']) . "<br>";
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
