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
    $query = "DELETE FROM expert_videos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);  // "i" for integer
    if ($stmt->execute()) {
        header("Location: expert_videos.html");
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
    $stmt = $con->prepare("INSERT INTO expert_videos (description, video_data) VALUES (?, ?)");
    $null = NULL; // Required for binary data
    $stmt->bind_param("sb", $title, $null);  // "s" for string, "b" for blob
    
    // Use MySQLi's send_long_data for large files (binary data)
    $stmt->send_long_data(1, $imageData);  // Sends the image data
    
    // Execute the query
    if ($stmt->execute()) {
        echo "Image uploaded and stored in database successfully!";
         header("Location: expert_videos.html");
         exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch data for displaying JSON (only if no delete request is made)
// $query = "SELECT * FROM expert_videos";
// $result = mysqli_query($con, $query);

// $data = [];
// // while ($row = mysqli_fetch_assoc($result)) {
// //    // $row['image'] = base64_encode($row['image']); // Encode image to Base64
// //     $data[] = $row;
// // }
// if ($result) {
//     while ($row = mysqli_fetch_assoc($result)) {
//         // If the image is not NULL, encode it as base64
//         if (!empty($row['video_data'])) {
//             $row['video_data'] = base64_encode($row['video_data']);
//         } else {
//             $row['video_data'] = null; // No image available
//         }
//         $data[] = $row;
//     }
//     echo json_encode($data);
// }

// // Output JSON data (this should only run for data fetching)
// header('Content-Type: application/json');
// echo json_encode($data);
$query = "SELECT * FROM expert_videos";
$result = mysqli_query($con, $query);

$data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // If the video path is not NULL, keep it as is
        if (!empty($row['video_data'])) {
            // Here we are assuming that video_data stores the file path
            $row['video_data'] =base64_encode($row['video_data']);  // No base64 encoding needed for file path
        } else {
            $row['video_data'] = null; // No video available
        }
        $data[] = $row;
    }
    // echo json_encode($data);
}

header('Content-Type: video/mp4');
echo json_encode($data);
// // // Close the database connection
// if ($result) {
//     // Fetch the row that contains the video
//     $row = mysqli_fetch_assoc($result);

//     // Check if the video data exists
//     if (!empty($row['video_data'])) {
//         // Set headers to serve the video file
//         header('Content-Type: video/mp4');
//         header('Content-Disposition: inline; filename="video.mp4"');
//         header('Content-Length: ' . strlen($row['video_data']));
        
//         // Output the video binary data
//         echo $row['video_data'];
//     } else {
//         // No video available
//         echo json_encode(['message' => 'No video available']);
//     }
// } else {
//     // Error with the query
//     echo json_encode(['error' => 'Error fetching video data']);
// }
mysqli_close($con);
?>
