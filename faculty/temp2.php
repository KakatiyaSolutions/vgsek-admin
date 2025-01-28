<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 10; // Default to 10 items per page

// Handling file upload
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
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        if ($id) {
            $stmt = $con->prepare("UPDATE dep_faculty_profile SET name=?, image=?, qualification=?, designation=?, department=?, department_id=? WHERE id=?");
            $null = NULL;
            $stmt->bind_param("sbsssii", $name, $null, $qualification, $designation, $department, $department_id, $id);
            $stmt->send_long_data(1, $imageData);
        } else {
            $stmt = $con->prepare("INSERT INTO dep_faculty_profile (name, image, qualification, designation, department, department_id) VALUES (?, ?, ?, ?,?,?)");
            $null = NULL;
            $stmt->bind_param("sbsssi", $name, $null, $qualification, $designation, $department, $department_id);
            $stmt->send_long_data(1, $imageData);
        }

        if ($stmt->execute()) {
            echo "Image uploaded and stored in database successfully!";
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
    $query = "SELECT * FROM dep_faculty_profile WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    mysqli_close($con);
    echo json_encode($data);
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $query = "DELETE FROM dep_faculty_profile WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        exit;
    } else {
        echo 'Error deleting image: ' . mysqli_error($con);
    }
    exit;
}

$searchQuery = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $searchQuery = "WHERE name LIKE '%$search%' OR qualification LIKE '%$search%' OR designation LIKE '%$search%' OR department LIKE '%$search%' OR department_id LIKE '%$search%'";
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$query = "SELECT * FROM dep_faculty_profile $searchQuery LIMIT $itemsPerPage OFFSET $offset";
$result = mysqli_query($con, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['image'])) {
            $row['image'] = base64_encode($row['image']);
        } else {
            $row['image'] = null;
        }
        $data[] = $row;
    }
}

$totalQuery = "SELECT COUNT(*) as total FROM dep_faculty_profile $searchQuery";
$totalResult = mysqli_query($con, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $itemsPerPage);

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

            <!-- <div id="uploadfile" class="modal" style="display: none !important;">
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

                            Department ID Dropdown
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
                </div> -->

    <di+v class="container">
        <!-- Search Form -->
        <div class="my-3">
            <form id="search-form" onsubmit="handleSearchSubmit(event)" method="GET" action="temp2.php">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search by Name, Qualification, Designation or Department">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" onclick="uploadimagefunction(2)"><span>+</span>Add</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Faculty Table -->
        <div id="content">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Qualification</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $faculty): ?>
                        <tr>
                            <td><img src="data:image/jpeg;base64,<?= $faculty['image'] ?>" alt="Faculty Image" width="50"></td>
                            <td><?= $faculty['name'] ?></td>
                            <td><?= $faculty['qualification'] ?></td>
                            <td><?= $faculty['designation'] ?></td>
                            <td><?= $faculty['department'] ?></td>
                            <td>
                                <button class="btn btn-warning" onclick="editFaculty(<?= $faculty['id'] ?>)">Edit</button>
                                <a href="?delete=<?= $faculty['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this profile?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= $_GET['search'] ?>&items_per_page=<?= $itemsPerPage ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

        <!-- Modal for Uploading Image -->
        <div id="uploadfile" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="uploadimagefunction(1)">&times;</span>
                <form id="facultyForm" method="POST" enctype="multipart/form-data" onsubmit="return handleFormSubmit(event)">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="qualification">Qualification:</label>
                        <input type="text" class="form-control" id="qualification" name="qualification" required>
                    </div>
                    <div class="form-group">
                        <label for="designation">Designation:</label>
                        <input type="text" class="form-control" id="designation" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department:</label>
                        <input type="text" class="form-control" id="department" name="department" required>
                    </div>
                    <div class="form-group">
                        <label for="department_id">Department ID:</label>
                        <input type="text" class="form-control" id="department_id" name="department_id" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </di+v>

    <script>
        function editFaculty(id) {
            fetch(`update_dp_faculty.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('id').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('qualification').value = data.qualification;
                    document.getElementById('designation').value = data.designation;
                    document.getElementById('department').value = data.department;
                    document.getElementById('department_id').value = data.department_id;
                    uploadimagefunction(0);
                });
        }

        function handleFormSubmit(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('facultyForm'));
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }

        function handleSearchSubmit(event) {
            event.preventDefault();
            const searchQuery = document.getElementById('search').value;
            window.location.href = `temp2.php?search=${searchQuery}&page=1`;
        }

        function uploadimagefunction(show) {
            const uploadFileDiv = document.getElementById('uploadfile');
            uploadFileDiv.style.display = show ? 'block' : 'none';
        }

        // function uploadimagefunction(id) {
        //     if (modal.style.display === "block") {
        //         modal.style.display = "none"; // If it's already open, close it
        //     } else {
        //         modal.style.display = "block"; // If it's closed, open it
        //     }
        //     if (id !== 2) {
        //         document.getElementById('id').value = '';
        //         document.getElementById('name').value = '';
        //         document.getElementById('qualification').value = '';
        //         document.getElementById('designation').value = '';
        //         document.getElementById('department').value = '';
        //         document.getElementById('department_id').value = '';
        //     }
        // }
        // Close the modal when clicking the "X" button
        // closeBtn.onclick = function () {
        //     modal.style.display = "none";
        // }
    </script>
    
</body>
</html>
