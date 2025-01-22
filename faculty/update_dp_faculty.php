<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');

// Get the faculty ID from the GET request
$id = $_GET['id'];

// Fetch the data for the specific faculty


// id, qualification, name, designation, department, department_id
$query = "SELECT id, qualification, name, designation, department, department_id FROM dep_faculty_profile WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Close the connection


// Return the data as JSON
echo json_encode($data);
mysqli_close($con);
?>
