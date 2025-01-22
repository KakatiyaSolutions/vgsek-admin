<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handling for uploading file
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    // Get the form data
    $name = $_POST['name'];
    $qualification = $_POST['qualification'];
    $designation = $_POST['designation'];
    $department = $_POST['department'];
    $department_id = $_POST['department_id'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    // Check if the image was uploaded without errors
    if ($_FILES['image']['error'] == 0) {
        // Read the image as binary data
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        
        if ($id) {
            // Update query
            $stmt = $con->prepare("UPDATE dep_faculty_profile SET name=?, image=?, qualification=?, designation=?, department=?, department_id=? WHERE id=?");
            $null = NULL; 
            $stmt->bind_param("sbsssii", $name,$null, $qualification, $designation, $department, $department_id, $id);
            $stmt->send_long_data(1, $imageData); 
        }else{
        // Prepare SQL query to insert image and data into the database
        $stmt = $con->prepare("INSERT INTO dep_faculty_profile (name, image, qualification, designation,department, department_id) VALUES (?, ?, ?, ?,?,?)");
        $null = NULL; // Required for binary data
        $stmt->bind_param("sbsssi", $name, $null, $qualification, $designation, $department, $department_id);  // Bind params correctly
        $stmt->send_long_data(1, $imageData);     
    }
        // Use MySQLi's send_long_data for large files (binary data)
       // Sends the image data
        
        // Execute the query
        if ($stmt->execute()) {
            echo "Image uploaded and stored in database successfully!";
            // header("Location: dep_faculty_profile.html");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error uploading file.";
    }
}

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];;
    $id = $_GET['id'];

// Fetch the data for the specific faculty
$query = "SELECT * FROM dep_faculty_profile WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Close the connection
mysqli_close($con);

// Return the data as JSON
echo json_encode($data);



}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Prepare the DELETE query
    $query = "DELETE FROM dep_faculty_profile WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);  // "i" for integer
    if ($stmt->execute()) {
        // header("Location: dep_faculty.php");
        exit;
    } else {
        echo 'Error deleting image: ' . mysqli_error($con);
    }
    exit;
}
if(isset($_GET['search'])){
    $search = $_GET['search'];;
    // $id = $_GET['id'];
    $query = "SELECT * FROM dep_faculty_profile WHERE name LIKE '%$search%' OR qualification LIKE '%$search%' OR designation LIKE '%$search%' OR department LIKE '%$search%' OR department_id LIKE '%$search%'";
    $result = mysqli_query($con, $query);    
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // If the image is not NULL, encode it as base64 or provide a URL
            if (!empty($row['image'])) {
                $row['image'] = base64_encode($row['image']);
            } else {
                $row['image'] = null; // No image available
            }
            $data[] = $row;
        }
    } else {
        echo "Error fetching data: " . mysqli_error($con);
    }

}else{
// Fetch data for displaying faculty profiles
$query = "SELECT * FROM dep_faculty_profile";
$result = mysqli_query($con, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // If the image is not NULL, encode it as base64 or provide a URL
        if (!empty($row['image'])) {
            $row['image'] = base64_encode($row['image']);
        } else {
            $row['image'] = null; // No image available
        }
        $data[] = $row;
    }
} else {
    echo "Error fetching data: " . mysqli_error($con);
}
}
// Close the database connection
mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
                            body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f9;
                        margin: 0;
                        padding: 0;
                        color: #333;
                    }

                    h1 {
                        text-align: center;
                        margin-top: 30px;
                        color: #2c3e50;
                    }

                    h2 {
                        color: #34495e;
                        font-size: 1.5em;
                    }

                    /* Upload Form Styling */
                    .upload-form {
                        max-width: 600px;
                        margin: 20px auto;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }

                    .upload-form label {
                        display: block;
                        font-size: 1.1em;
                        margin-bottom: 5px;
                        color: #555;
                    }

                    .upload-form input[type="text"],
                    .upload-form select,
                    .upload-form input[type="file"] {
                        width: 100%;
                        padding: 8px;
                        margin: 10px 0 20px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        box-sizing: border-box;
                        font-size: 1em;
                    }

                    .upload-form button {
                        background-color: #3498db;
                        color: white;
                        padding: 12px 20px;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 1.2em;
                        width: 100%;
                    }

                    .upload-form button:hover {
                        background-color: #2980b9;
                    }

                    /* Table Styling */
                    .table-responsive {
                        max-width: 100%;
                        margin: 30px auto;
                        overflow-x: auto;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                    }

                    table th, table td {
                        padding: 12px;
                        text-align: left;
                        border: 1px solid #ddd;
                    }

                    table th {
                        background-color: #34495e;
                        color: white;
                        font-size: 1.1em;
                    }

                    table td {
                        background-color: #fff;
                    }

                    table tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }

                    table td img {
                        border-radius: 50%;
                    }

                    /* Action Link Styling */
                    a {
                        color: #e74c3c;
                        text-decoration: none;
                        font-weight: bold;
                    }

                    a:hover {
                        text-decoration: underline;
                    }

                    /* Image Styling */
                    .faculty-img-sec {
                        border-radius: 50%;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        margin: 0 auto;
                    }

                    /* Responsive Design */
                    @media (max-width: 768px) {
                        .upload-form {
                            width: 90%;
                        }

                        table th, table td {
                            font-size: 0.9em;
                        }

                        .table-responsive {
                            margin: 10px 0;
                        }
                    }
                    /* Modal Background Overlay */
                    .modal {
                        display: none; /* Hidden by default */
                        position: fixed; /* Stay in place */
                        z-index: 1; /* Sit on top */
                        left: 0;
                        top: 0;
                        width: 100%; /* Full width */
                        height: 100%; /* Full height */
                        background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
                        overflow: auto; /* Enable scroll if needed */
                        padding-top: 60px; /* Position the modal slightly below the top */
                    }

                    /* Modal Content */
                    .modal-content {
                        background-color: #fff;
                        margin: 5% auto; /* Center the modal */
                        padding: 20px;
                        border-radius: 8px;
                        width: 80%; /* Width of the modal */
                        max-width: 600px; /* Max width of the modal */
                    }

                    /* Close Button */
                    .close {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                    }

                    .close:hover,
                    .close:focus {
                        color: black;
                        text-decoration: none;
                        cursor: pointer;
                    }

                    /* Styles for the form */
                    input, select, button {
                        font-size: 1em;
                        width: 100%;
                        padding: 8px;
                        margin: 10px 0;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        box-sizing: border-box;
                    }

                    button {
                        background-color: #3498db;
                        color: white;
                        padding: 12px 20px;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                    }

                    button:hover {
                        background-color: #2980b9;
                    }

    </style>
</head>
<body>
    <!-- <div id="uploadfile" style="display: none;">
<h1>Upload new Profile</h1> -->
<!-- The Modal (popup overlay) -->
<div id="uploadfile" class="modal">
    <div class="modal-content">
       
        <h2>Upload Profile  <span class="close" style="color:red;text-align: right;" onclick="uploadimagefunction(1)">&times;</span></h2>
        <form action="dep_faculty.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id">
            <label for="name">Name of the faculty:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" required><br><br>
            <label for="designation">Designation:</label>
            <input type="text" id="designation" name="designation" required><br><br>
            <label for="department">Department (B.Tech Branches):</label>
            <select id="department" name="department" required>
                <option value="">Select Department</option>
                <option value="CSC">Computer Science</option>
                <option value="MEC">Mechanical Engineering</option>
                <option value="EEE">Electrical Engineering</option>
                <option value="CIV">Civil Engineering</option>
                <option value="ECE">Electronics and Communication</option>
                <option value="AI_ML">Artificial Intelligence & Machine Learning</option>
                <option value="DSE">Data Science Engineering</option>
            </select><br><br>

            <!-- Department ID Dropdown -->
            <label for="department_id">Department ID:</label>
            <select id="department_id" name="department_id" required>
                <option value="">Select Department ID</option>
                <option value="1">CSE</option>
                <option value="2">EEE</option>
                <option value="3">ECE</option>
                <option value="4">MEC</option>
                <option value="5">CIV</option>
                <option value="6">AI & ML</option>
                <option value="7">DS</option>
            </select><br><br>

            <label for="image">Faculty Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required><br><br>

            <button type="submit">Upload Image</button>
        </form>
    </div>
</div>
<!-- </div> -->
<!-- Display Faculty Profiles -->
 <button class="btn btn-primary" onclick="uploadimagefunction(1)"> Upload New Faculty</button>  


 <form action="dep_faculty.php"  method="GET" enctype="multipart/form-data">
 <label for="search">Faculty search:</label>
            <input type="text" id="search" name="search">
            <button type="submit">search</button>
 </form>

<div class="committee_table_inn">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
        <!-- <form action="dep_faculty.php"  method="GET" enctype="multipart/form-data">
 <label for="search">Faculty search:</label>
            <input type="text" id="search" name="search"><br><br>
            <button type="submit">search</button>
 </form> -->
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name of the Faculty</th>
                    <th>Qualification</th>
                    <th>Designation</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['qualification']); ?></td>
                            <td><?php echo htmlspecialchars($row['designation']); ?></td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <!-- If image exists (encoded as base64) -->
                                    <img src="data:image/jpeg;base64,<?php echo $row['image']; ?>" alt="faculty_img" width="100" class="img-fluid faculty-img-sec">
                                <?php else: ?>
                                    <!-- Default image if no image is available -->
                                    <img src="https://kakatiyasolutions.in/vageshwari_clg/assets/images/department/user-img.png" alt="faculty_img" width="100" class="img-fluid faculty-img-sec">
                                <?php endif; ?>
                            </td>
                            <td>
                            <button onclick="editFaculty(<?php echo $row['id']; ?>)">Update</button>
                                <a href="dep_faculty.php?delete=<?php echo $row['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    // Function to open the modal and populate form for update


// function uploadimagefunction(){
//     if (document.getElementById('uploadfile').style.display === 'block') {
//         document.getElementById('uploadfile').style.display = 'none';
//     } else {
//         document.getElementById('uploadfile').style.display = 'block';
//     }
// }
// Get the modal and the close button
var modal = document.getElementById("uploadfile");
var closeBtn = document.getElementsByClassName("close")[0];

// Open the modal
function uploadimagefunction(id) {
    if (modal.style.display === "block") {
        modal.style.display = "none";  // If it's already open, close it
    } else {
        modal.style.display = "block";  // If it's closed, open it
    }
    if(id!==2){
        document.getElementById('id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('qualification').value ='';
            document.getElementById('designation').value = '';
            document.getElementById('department').value ='';
            document.getElementById('department_id').value ='';
    }
}

// Close the modal when clicking the "X" button
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// Close the modal if the user clicks anywhere outside the modal content
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
function editFaculty(id) {
    // uploadimagefunction()
    let data = {};
    // Send AJAX request to get the faculty data
    fetch(`update_dp_faculty.php?id=${id}`)
        .then(response => response.json())
        .then(responseData => {
            // uploadimagefunction()
            data = responseData;
            // Populate the form with the data
            document.getElementById('id').value = data.id?data.id:'';
            document.getElementById('name').value = data.name?data.name:'';
            document.getElementById('qualification').value = data.qualification?data.qualification:'';
            document.getElementById('designation').value = data.designation?data.designation:'';
            document.getElementById('department').value = data.department?data.department:'';
            document.getElementById('department_id').value = data.department_id?data.department_id:'';
            
            // Open the modal
            uploadimagefunction(2);
        })
        .catch(error => console.error('Error:', error));
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
