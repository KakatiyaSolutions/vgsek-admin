<?php
session_start();

// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Prepare the DELETE query
    $query = "DELETE FROM carousel_banners WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);  // "i" for integer
    if ($stmt->execute()) {
        header("Location: carousel-banners.html");
        exit;
    } else {
        echo 'Error deleting image: ' . mysqli_error($con);
    }
    exit;
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $title = $_POST['title'];  // Get the title from the form
    echo '<pre>';
    var_dump($_FILES['image']);  // This will show details of the uploaded file.
    echo '</pre>';
    
    // Read the image as binary data
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    
    // Prepare SQL query to insert image and title into the database
    $stmt = $con->prepare("INSERT INTO carousel_banners (description, image) VALUES (?, ?)");
    $null = NULL; // Required for binary data
    $stmt->bind_param("sb", $title, $null);  // "s" for string, "b" for blob
    
    // Use MySQLi's send_long_data for large files (binary data)
    $stmt->send_long_data(1, $imageData);  // Sends the image data
    
    // Execute the query
    if ($stmt->execute()) {
        echo "Image uploaded and stored in database successfully!";
         header("Location: carousel-banners.html");
         exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch data for displaying JSON (only if no delete request is made)
$query = "SELECT id, description, image FROM carousel_banners";
$result = mysqli_query($con, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['image'] = base64_encode($row['image']); // Encode image to Base64
    $data[] = $row;
}

// Output JSON data (this should only run for data fetching)
header('Content-Type: application/json');
echo json_encode($data);

// Close the database connection
mysqli_close($con);
?>
