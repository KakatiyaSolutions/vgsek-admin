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
    <div class="container">
        <!-- Search Form -->
        <div class="my-3">
            <form id="search-form" onsubmit="handleSearchSubmit(event)" method="GET" action="dep_faculty.php">
                <div class="row">
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search by Name, Qualification, Designation or Department">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
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
    </div>

    <script>
        function editFaculty(id) {
            fetch(`dep_faculty.php?id=${id}`)
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
            window.location.href = `dep_faculty.php?search=${searchQuery}&page=1`;
        }

        function uploadimagefunction(show) {
            const uploadFileDiv = document.getElementById('uploadfile');
            uploadFileDiv.style.display = show ? 'block' : 'none';
        }
    </script>
</body>
</html>
