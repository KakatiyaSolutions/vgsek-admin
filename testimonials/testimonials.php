<?php
// Database connection
$conn = mysqli_connect('srv1328.hstgr.io', 'u629694569_carehospital', 'Kakatiya1234$', 'u629694569_carediabetesce');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// for deleting
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prepare the DELETE query
    $query = "DELETE FROM patient_testimonials WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);  // "i" for integer
    if ($stmt->execute()) {
        // Redirect after successfully deleting the testimonial
        header("Location: testimonials.html");
        exit;  // Ensure that the script exits to prevent further output
    } else {
        echo 'Error deleting image: ' . mysqli_error($conn);
    }
}

// Handle GET request for fetching testimonials
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch testimonials
    $query = "SELECT * FROM patient_testimonials";
    $result = mysqli_query($conn, $query);

    $data = [];
    // if ($result) {
    //     while ($row = mysqli_fetch_assoc($result)) {
    //         if (!empty($row['image'])) {
    //             $imagePath = "chmod 644 uploads/" . $row['image'];
    //             $row['image'] = file_exists($imagePath) ? base64_encode(file_get_contents($imagePath)) : null;
    //         }
    //         $data[] = $row;
    //     }
    //     echo json_encode($data);
    // } else {
    //     echo json_encode(["error" => "Error fetching data: " . mysqli_error($conn)]);
    // }
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // If the image is not NULL, encode it as base64
            if (!empty($row['image'])) {
                $row['image'] = base64_encode($row['image']);
            } else {
                $row['image'] = null; // No image available
            }
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Error fetching data: " . mysqli_error($conn)]);
    }
}

// Handle POST request for adding or updating testimonial
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Handle image upload
    $imageData = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // var_dump($_FILES['image']);
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }
    //  else {
    //     echo json_encode(["error" => "No image uploaded or error in upload."]);
    // }

    if ($id) {
        // Update testimonial
        // $query = "UPDATE patient_testimonials SET description = ?, testimonial_name = ?, testimonial_address = ?";
        // if ($imageData) {
        //     $query .= ", image = ?";
        // }
        // $query .= " WHERE id = ?";

        // if ($stmt = $conn->prepare($query)) {
        //     if ($imageData) {
        //         $stmt->bind_param("ssssb", $description, $name, $address, $imageData, $id);
        //         $stmt->send_long_data(3, $imageData);  // Send binary data for image
        //     } else {
        //         $stmt->bind_param("ssss", $description, $name, $address, $id);
        //     }
        //     if ($stmt->execute()) {
        //         echo json_encode(["message" => "Testimonial updated successfully."]);
        //     } else {
        //         echo json_encode(["error" => "Error updating testimonial: " . mysqli_error($conn)]);
        //     }
        // }
        if ($id) {
            // Update testimonial
            $query = "UPDATE patient_testimonials SET description = ?, testimonial_name = ?, testimonial_address = ?";
            if ($imageData) {
                $query .= ", image = ?";  // If image data is provided, include it in the query
            }
            $query .= " WHERE id = ?";
        
            if ($stmt = $conn->prepare($query)) {
                if ($imageData) {
                    // Prepare the statement with the image data
                    $stmt->bind_param("ssssb", $description, $name, $address, $imageData, $id);
                    $stmt->send_long_data(3, $imageData);  // Send binary image data to MySQL
                } else {
                    // If no image is uploaded, bind parameters without the image
                    $stmt->bind_param("ssss", $description, $name, $address, $id);
                }
                
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Testimonial updated successfully."]);
                } else {
                    echo json_encode(["error" => "Error updating testimonial: " . $stmt->error]);
                }
            } else {
                echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
            }
        }
        
    }
     else {
        // Add new testimonial
        $query = "INSERT INTO patient_testimonials (description, image, testimonial_name, testimonial_address) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($query)) {
            if ($imageData) {
                $stmt->bind_param("sbss", $description, $imageData, $name, $address);
                $stmt->send_long_data(1, $imageData);  // Send binary data for image
            } else {
                $stmt->bind_param("ssss", $description, $null, $name, $address);
                $null = NULL;
            }
            if ($stmt->execute()) {
                echo json_encode(["message" => "Testimonial added successfully."]);
            } else {
                echo json_encode(["error" => "Error adding testimonial: " . mysqli_error($conn)]);
            }
        }
    }
}

// Handle DELETE request for removing a testimonial
// elseif (isset($_GET['delete'])) {
//     $id = intval($_GET['delete']);
//     $query = "DELETE FROM patient_testimonials WHERE id = ?";
//     if ($stmt = $conn->prepare($query)) {
//         $stmt->bind_param("i", $id);  // "i" for integer
//         if ($stmt->execute()) {
//             echo json_encode(["message" => "Testimonial deleted successfully."]);
//             header("Location: testimonials.html");
//             exit;
//         } else {
//             echo json_encode(["error" => "Error deleting testimonial: " . mysqli_error($conn)]);
//         }
//     }
// } else {
//     echo json_encode(["error" => "Invalid request method."]);
// }
// elseif (isset($_GET['delete'])) {
//     $id = intval($_GET['delete']);
//     $query = "DELETE FROM patient_testimonials WHERE id = ?";
    
//     if ($stmt = $conn->prepare($query)) {
//         $stmt->bind_param("i", $id);  // "i" for integer
//         if ($stmt->execute()) {
//             // Return a success response after deletion
//             echo json_encode(["message" => "Testimonial deleted successfully."]);
//             // Make sure the script ends after the redirect, preventing unnecessary data from being sent
//             header("Location: testimonials.html");
//             exit;  // Exit immediately after redirect
//         } else {
//             echo json_encode(["error" => "Error deleting testimonial: " . mysqli_error($conn)]);
//         }
//     }
// }
 else {
    echo json_encode(["error" => "Invalid request method."]);
}

// if (isset($_GET['delete'])) {
//     $id = (int)$_GET['delete'];

//     // Prepare the DELETE query
//     $query = "DELETE FROM patient_testimonials WHERE id = ?";
//     $stmt = $conn->prepare(query: $query);
//     $stmt->bind_param("i", $id);  // "i" for integer
//     if ($stmt->execute()) {
//         header("Location: testimonials.html");
//         exit;
//     } else {
//         echo 'Error deleting image: ' . mysqli_error($conn);
//     }
//     exit;
// }

// Close the database connection
mysqli_close($conn);
?>
