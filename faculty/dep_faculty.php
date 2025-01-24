<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 10; // Default to 10 items per page
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
            $stmt->bind_param("sbsssii", $name, $null, $qualification, $designation, $department, $department_id, $id);
            $stmt->send_long_data(1, $imageData);
        } else {
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

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    ;
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
    $id = (int) $_GET['delete'];

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
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $itemsPerPage;
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
    $totalQuery = "SELECT COUNT(*) as total FROM dep_faculty_profile";
    $totalResult = mysqli_query($con, $totalQuery);
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalRecords = $totalRow['total'];
    $totalPages = ceil($totalRecords / $itemsPerPage);
} else {
    // Pagination logic
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $itemsPerPage;

    // Fetch data for displaying faculty profiles with LIMIT and OFFSET for pagination
    $query = "SELECT * FROM dep_faculty_profile LIMIT $itemsPerPage OFFSET $offset";
    $result = mysqli_query($con, $query);

    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // If the image is not NULL, encode it as base64 or provide a URL
            if (!empty($row['image'])) {
                $row['image'] = base64_encode($row['image']);
            } else {
                $row['image'] = null;
            }
            $data[] = $row;
        }
    }

    // Get total number of records for pagination
    $totalQuery = "SELECT COUNT(*) as total FROM dep_faculty_profile";
    $totalResult = mysqli_query($con, $totalQuery);
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalRecords = $totalRow['total'];
    $totalPages = ceil($totalRecords / $itemsPerPage);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="dep_faculty_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.2/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <!-- <div id="uploadfile" style="display: none;">
            <h1>Upload new Profile</h1> -->
    <!-- The Modal (popup overlay) -->
    <div id="uploadfile" class="modal" style="display: none !important;">
        <div class="modal-content">

            <h2>Upload Profile <span class="close" style="color:red;text-align: right;"
                    onclick="uploadimagefunction(1)">&times;</span></h2>
            <form action="dep_faculty.php" method="POST" enctype="multipart/form-data" class="upload-form">
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
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-sm-6">


                <button class="btn btn-primary" onclick="uploadimagefunction(1)"> <span
                        style="font-weight: bold;font-size:large">+</span> Upload New Faculty</button>


                <!-- <form action="dep_faculty.php"  method="GET" enctype="multipart/form-data" class="searchform" >

            <input type="text" id="search" name="search" placeholder="Search by name, qualification, designation, department, department_id"><br><br>
            &nbsp;
            <button type="submit" class="submit-button">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>
 </form> -->
                <!-- <form action="dep_faculty.php" method="GET" enctype="multipart/form-data" class="searchform" >
   
    <input type="text" id="search" name="search" placeholder="Search by name, qualification, designation, department, department_id" 
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"><br><br>

    <button type="submit" class="submit-button">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>
    </form> -->
                <form action="dep_faculty.php" method="GET" enctype="multipart/form-data" class="searchform"
                    onsubmit="return validateSearch()">
                    <!-- <label for="search">Faculty search:</label> -->
                    <input type="text" id="search" name="search"
                        placeholder="Search by name, qualification, designation, department, department_id"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"><br><br>
                    <button type="submit" class="submit-button">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <br>
                    <!-- This is where the error message will be displayed if search input is empty -->
                    <p id="error-message" style="color: red; display: none;"></p>
                </form>


                <a href="dep_faculty.php"><i class="fa-solid fa-rotate-right" style="
    font-size: xx-large;
    margin-top: 2px; color: black;"></i></a>


            </div>
        </div>
    </div>






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
                                        <img src="data:image/jpeg;base64,<?php echo $row['image']; ?>" alt="faculty_img" width="100"
                                            class="img-fluid faculty-img-sec">
                                    <?php else: ?>
                                        <!-- Default image if no image is available -->
                                        <img src="https://kakatiyasolutions.in/vageshwari_clg/assets/images/department/user-img.png"
                                            alt="faculty_img" width="100" class="img-fluid faculty-img-sec">
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
    <form action="dep_faculty.php" method="GET" class="items-per-page-form">
        <label for="items_per_page">Items per page:</label>
        <select name="items_per_page" id="items_per_page" onchange="this.form.submit()">
            <option value="5" <?php echo $itemsPerPage == 5 ? 'selected' : ''; ?>>5</option>
            <option value="10" <?php echo $itemsPerPage == 10 ? 'selected' : ''; ?>>10</option>
            <option value="15" <?php echo $itemsPerPage == 15 ? 'selected' : ''; ?>>15</option>
            <option value="20" <?php echo $itemsPerPage == 20 ? 'selected' : ''; ?>>20</option>
        </select>
    </form>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="dep_faculty.php?page=<?php echo $page - 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="dep_faculty.php?page=<?php echo $i; ?>&items_per_page=<?php echo $itemsPerPage; ?>" <?php if ($i == $page)
                      echo 'class="active"'; ?>>
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="dep_faculty.php?page=<?php echo $page + 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>">Next</a>
        <?php endif; ?>
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
        var closeBtn = document.getElementsByClassName("close");
        console.log(modal.style.value);
        // Open the modal
        function uploadimagefunction(id) {
            if (modal.style.display === "block") {
                modal.style.display = "none"; // If it's already open, close it
            } else {
                modal.style.display = "block"; // If it's closed, open it
            }
            if (id !== 2) {
                document.getElementById('id').value = '';
                document.getElementById('name').value = '';
                document.getElementById('qualification').value = '';
                document.getElementById('designation').value = '';
                document.getElementById('department').value = '';
                document.getElementById('department_id').value = '';
            }
        }
        // Close the modal when clicking the "X" button
        closeBtn.onclick = function () {
            modal.style.display = "none";
        }
        // Close the modal when clicking the "X" button
        //  function closeBtn() {
        //     modal.style.display = "none";
        // }

        // Close the modal if the user clicks anywhere outside the modal content
        // window.onclick = function(event) {
        //     if (event.target === modal) {
        //         modal.style.display = "block";
        //     }
        // }
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
                    document.getElementById('id').value = data.id ? data.id : '';
                    document.getElementById('name').value = data.name ? data.name : '';
                    document.getElementById('qualification').value = data.qualification ? data.qualification : '';
                    document.getElementById('designation').value = data.designation ? data.designation : '';
                    document.getElementById('department').value = data.department ? data.department : '';
                    document.getElementById('department_id').value = data.department_id ? data.department_id : '';

                    // Open the modal
                    uploadimagefunction(2);
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
    <script>
        // JavaScript function to validate input before form submission
        function validateSearch() {
            var searchInput = document.getElementById('search').value.trim(); // Get the value of the search input
            var errorMessage = document.getElementById('error-message');

            if (searchInput === "") {
                errorMessage.style.display = "block"; // Show error message
                // document.getElementById('error-message').innerHTML = "Please give input to search.";
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please give input to search!',
                    showConfirmButton: true
                });
                return false; // Prevent form submission
            } else {
                errorMessage.style.display = "none"; // Hide error message
                return true; // Allow form submission
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>

