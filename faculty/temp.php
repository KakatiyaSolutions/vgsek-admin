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
<!-- <script src="my-ckeditor-project/ckeditor/ckeditor.js"></script> -->
<!-- <script src="js/ckeditor.js"></script> -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>VAAGESWARI COLLEG OF ENGINEERING</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- General CSS Files -->
<link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- CSS Libraries -->
<link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
<link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">
<link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
<link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">

<!-- Template CSS -->
<link rel="stylesheet" href="../assets/css/style.min.css">
<link rel="stylesheet" href="../assets/css/components.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="dep_faculty_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.2/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            /* margin-top: 20px; */
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
   
    a{
        text-decoration: none;
        color: black;
        font-size: large;
    }
    .active a {
    font-weight: bold; /* Example: Make the text bold */
    color:rgb(0, 0, 0) !important;   /* Example: Change the text color */
    background-color: #f8f9fa; /* Example: Add a background color */
}

    /* .sidebar-menu .submenu {
    display: none; 
    list-style-type: none;
    padding-left: 20px;
}

.sidebar-menu .dropdown > a .arrow {
    margin-left: auto; 
    transition: transform 0.3s ease; 
}

.sidebar-menu .dropdown.open > a .arrow {
    transform: rotate(180deg); 
} */

/* Show submenu on hover (or you can toggle it with JS) */
/* .sidebar-menu .dropdown:hover .submenu {
    display: block; 
} */

.sidebar-menu .submenu {
    display: none; /* Initially hide submenus */
    list-style-type: none;
    padding-left: 20px; /* Optional: add indentation for subitems */
    max-height: 0; /* Start with 0 height for smooth transition */
    overflow: hidden; /* Hide content that exceeds the max height */
    transition: max-height 0.3s ease-out; /* Smooth transition for height */
}

.sidebar-menu .dropdown.open .submenu {
    display: block; /* Make submenu visible when it's open */
    max-height: 500px; /* Allow submenu to expand up to a certain height */
}

.sidebar-menu .dropdown > a .arrow {
    margin-left: auto; /* Push the arrow to the right */
    transition: transform 0.3s ease; /* Smooth transition for arrow rotation */
}

.sidebar-menu .dropdown.open > a .arrow {
    transform: rotate(180deg); /* Rotate arrow when dropdown is open */
}


</style>
    <style>
    /* General styles for the body and layout */
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    padding: 0;
    background-color: #f4f4f4;
}

h1 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 20px;
}

form {
    background-color: #F0EFF1 !important;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 80%;
    margin: 0 auto;
}

label {
    font-size: 1.1rem;
    margin-bottom: 5px;
    display: block;
}

textarea {
    width: 100%;
    height: 150px;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.1rem;
}

button:hover {
    background-color: #45a049;
}

/* Styling for the form layout */
form input[type="text"],
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

textarea:focus,
input:focus {
    border-color: #4CAF50;
    outline: none;
}

label {
    display: block;
    font-size: 1rem;
    text-align: center;
    background-color: orange;
    margin-bottom: 2px;
    font-weight: bold;
    font-size: 35px;
    line-height: 43px;
   
    
}

button[type="submit"] {
    background-color: #4CAF50;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 1rem;
    border-radius: 5px;
    text-align: center;
}

button[type="submit"]:hover {
    background-color: #45a049;
}

/* Style for the form container */
form {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 20px;
    width: 60%;
    margin: 0 auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
h1{
    text-align: center;
}
.navbar {
  overflow: hidden;
  /* background-color: #ffffff;*/
  position: fixed; 
  top: 0;
  width: 100%;
  left : 0px !important;
}
.section{
    z-index: 0.9 !important;
}

</style>
</head>
<body class="layout-4">
<!-- Page Loader -->
<div class="page-loader-wrapper">
    <span class="loader"><span class="loader-inner"></span></span>
</div>

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        
        <!-- Start app top navbar -->
        <nav class="navbar navbar-expand-lg main-navbar">
            <form class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                    <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
                </ul>
                <div class="search-element">
                    <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="250">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                    <div class="search-backdrop"></div>
                </div>
            </form>
            <ul class="navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                  
                    <div class="d-sm-none d-lg-inline-block">Admin</div></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="profile.html" class="dropdown-item has-icon text-primary" ><i class="fa-solid fa-user"></i> My Profile</a>
                        <a href="#" class="dropdown-item has-icon text-danger" id="logoutButton"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Start main left sidebar menu -->
        <div class="main-sidebar sidebar-style-3">
            <aside id="sidebar-wrapper">
                <div class="sidebar-brand">
                    <a href="index.html"><img src="https://kakatiyasolutions.in/vageshwari_clg/assets/images/logo/LOGO.png" class="img-fluid" alt="logo" loading="lazy" decoding="async"></a>
                </div>
                <div class="sidebar-brand sidebar-brand-sm">
                    <a href="index.html"><span style="font-size: small;">VGSEK</span></a>
                </div>
                <ul class="sidebar-menu">
                    <li class="dropdown active">
                        <a href="index.html"><i class="fa-solid fa-border-all"></i><span>Dashboard</span></a>
                        
                    </li>
                    <!-- <li class="dropdown">
                        <a href="patient-education.html"><i class="fa-regular fa-id-card"></i><span>Patient Education</span></a>
                        
                    </li> -->
                    <!-- <li class="dropdown"><a class="nav-link" href="testimonials.html"><i class="fa-solid fa-comment"></i><span>Patient Testimonials</span></a></li> -->
                <!-- <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
                    <a href="https://getcodiepie.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split"><i class="fas fa-rocket"></i> Documentation</a>
                </div> -->
                <!-- <li class="dropdown"><a class="nav-link" href="carousel-banner.html"><i class="fa-solid fa-image"></i> <span>Carousel Banners</span></a></li> -->
                <!-- <li class="dropdown disabled"><a class="nav-link" href="experts_suggestion.html"><i class="fa-solid fa-video"></i> <span>Expert Videos</span></a></li> -->
                <!-- <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-eye-low-vision"></i> <span>Program Overview</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu" >
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-binoculars" ></i><span>Overview</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-bullseye" ></i><span>Vision-Mission</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-brands fa-nfc-symbol"></i><span>PEO’s</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-brands fa-nfc-directional"></i><span>PO’s & PSO’s</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-building-columns"></i><span>Board of Studies</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-people-roof"></i><span>Advisory Board</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-trophy"></i><span>Achievements</span></a></li>

                    </ul>
                </li> -->

                <!-- <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-eye-low-vision"></i> <span>Program Overview</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu" >
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-binoculars" ></i><span>EEE</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-bullseye" ></i><span>ECE-</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-brands fa-nfc-symbol"></i><span>CVI</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-brands fa-nfc-directional"></i><span>MEC</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-building-columns"></i><span>CSE</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-people-roof"></i><span>AI&ML</span></a></li>
                        <li><a href="about.php" style="padding-left: 0px;"><i class="fa-solid fa-trophy"></i><span>IT</span></a></li>

                    </ul>
                </li> -->
                <!-- dynamic displaying the branches -->
               
                <!-- <li class="dropdown">
                    <a class="nav-link"><i class="fa-solid fa-eye-low-vision"></i> <span>Program Overview</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li><a href="about.php?department=EEE" style="padding-left: 0px;"><i class="fa-solid fa-binoculars"></i><span>EEE</span></a></li>
                        <li><a href="about.php?department=ECE" style="padding-left: 0px;"><i class="fa-solid fa-bullseye"></i><span>ECE</span></a></li>
                        <li><a href="about.php?department=CIV" style="padding-left: 0px;"><i class="fa-brands fa-nfc-symbol"></i><span>CIV</span></a></li>
                        <li><a href="about.php?department=MEC" style="padding-left: 0px;"><i class="fa-brands fa-nfc-directional"></i><span>MEC</span></a></li>
                        <li><a href="about.php?department=CSE" style="padding-left: 0px;"><i class="fa-solid fa-building-columns"></i><span>CSE</span></a></li>
                        <li><a href="about.php?department=AI_ML" style="padding-left: 0px;"><i class="fa-solid fa-people-roof"></i><span>AI&ML</span></a></li>
                        <li><a href="about.php?department=IT" style="padding-left: 0px;"><i class="fa-solid fa-trophy"></i><span>IT</span></a></li>
                    </ul>
                </li> -->
                <li class="dropdown">
                    <a class="nav-link"><i class="fa-solid fa-eye-low-vision"></i> <span>Program Overview</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li class="<?php echo ($active_department === 'EEE') ? 'active' : ''; ?>">
                            <a href="about.php?department=EEE" style="padding-left: 0px;">
                                <i class="fa-solid fa-binoculars"></i><span>EEE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'ECE') ? 'active' : ''; ?>">
                            <a href="about.php?department=ECE" style="padding-left: 0px;">
                                <i class="fa-solid fa-bullseye"></i><span>ECE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'CIV') ? 'active' : ''; ?>">
                            <a href="about.php?department=CIV" style="padding-left: 0px;">
                                <i class="fa-brands fa-nfc-symbol"></i><span>CIV</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'MEC') ? 'active' : ''; ?>">
                            <a href="about.php?department=MEC" style="padding-left: 0px;">
                                <i class="fa-brands fa-nfc-directional"></i><span>MEC</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'CSE') ? 'active' : ''; ?>">
                            <a href="about.php?department=CSE" style="padding-left: 0px;">
                                <i class="fa-solid fa-building-columns"></i><span>CSE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'AI_ML') ? 'active' : ''; ?>">
                            <a href="about.php?department=AI_ML" style="padding-left: 0px;">
                                <i class="fa-solid fa-people-roof"></i><span>AI&ML</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'DS') ? 'active' : ''; ?>">
                            <a href="about.php?department=DS" style="padding-left: 0px;">
                                <i class="fa-solid fa-trophy"></i><span>DS</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!--  droopdown for B.Tech course -->
                <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-graduation-cap"></i> <span>B.Tech Courses</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li class="<?php echo ($active_department === 'EEE') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=EEE" style="padding-left: 0px;">
                                <i class="fa-solid fa-binoculars"></i><span>EEE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'ECE') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=ECE" style="padding-left: 0px;">
                                <i class="fa-solid fa-bullseye"></i><span>ECE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'CIV') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=CIV" style="padding-left: 0px;">
                                <i class="fa-brands fa-nfc-symbol"></i><span>CIV</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'MEC') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=MEC" style="padding-left: 0px;">
                                <i class="fa-brands fa-nfc-directional"></i><span>MEC</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'CSE') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=CSE" style="padding-left: 0px;">
                                <i class="fa-solid fa-building-columns"></i><span>CSE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'AI_ML') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=AI_ML" style="padding-left: 0px;">
                                <i class="fa-solid fa-people-roof"></i><span>AI&ML</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'DS') ? 'active' : ''; ?>">
                            <a href="dep_btech.php?department=DS" style="padding-left: 0px;">
                                <i class="fa-solid fa-trophy"></i><span>DS</span>
                            </a>
                        </li>
                    </ul>
                    <!-- <ul class="submenu">
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-book"></i><span>Syllabus</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fas fa-calendar-check"></i><span>Timetables</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-calendar-days"></i><span>Academic Calendar</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-flask-vial"></i><span>Laboratories</span></a></li>
                    </ul> -->
                </li>
                 <!--  droopdown for M.Tech course -->
                 <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-graduation-cap"></i> <span>M.Tech Courses</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li class="<?php echo ($active_department === 'EEE') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=EEE" style="padding-left: 0px;">
                                <i class="fa-solid fa-binoculars"></i><span>EEE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'ECE') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=ECE" style="padding-left: 0px;">
                                <i class="fa-solid fa-bullseye"></i><span>ECE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'CIV') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=CIV" style="padding-left: 0px;">
                                <i class="fa-brands fa-nfc-symbol"></i><span>CIV</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'MEC') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=MEC" style="padding-left: 0px;">
                                <i class="fa-brands fa-nfc-directional"></i><span>MEC</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'CSE') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=CSE" style="padding-left: 0px;">
                                <i class="fa-solid fa-building-columns"></i><span>CSE</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'AI_ML') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=AI_ML" style="padding-left: 0px;">
                                <i class="fa-solid fa-people-roof"></i><span>AI&ML</span>
                            </a>
                        </li>
                        <li class="<?php echo ($active_department === 'DS') ? 'active' : ''; ?>">
                            <a href="dep_mtech.php?department=DS" style="padding-left: 0px;">
                                <i class="fa-solid fa-trophy"></i><span>DS</span>
                            </a>
                        </li>
                    </ul>
                    <!-- <ul class="submenu">
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-book"></i><span>Syllabus</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fas fa-calendar-check"></i><span>Timetables</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-calendar-days"></i><span>Academic Calendar</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-flask-vial"></i><span>Laboratories</span></a></li>
                    </ul> -->
                </li>

                <!-- Faculty profile -->
                  <!--  droopdown for faculty -->
                  <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-chalkboard-user"></i> <span>Faculty</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-address-card"></i><span>Faculty Profile</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fas fa-calendar-check"></i><span>Faculty Events</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-globe"></i><span>Publications</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-newspaper"></i></i><span>Newsletter</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-lightbulb"></i></i><span>Faculty Innovations</span></a></li>
                    </ul>
                </li>
                <!--  dropdown for students -->
                <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-user-graduate"></i> <span>Students</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li><a href="#" style="padding-left: 0px;"><i class="fas fa-calendar-check"></i><span>Student Events</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-square-poll-vertical"></i><span>Results</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-light fa-briefcase"></i><span>Placements</span></a></li>
                    </ul>
                </li>
                 <!--  dropdown for contact Information -->
                 <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-address-book"></i><span>Contact Info</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-phone"></i><span>Contact HOD</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-tty"></i><span>CO’s and PO’s</span></a></li>
                    </ul>
                </li>
            </ul>
            </aside>
        </div>  

        <!-- Start app main Content -->
        <div class="main-content">
           <!-- <div id="uploadfile" style="display: none;">
            <h1>Upload new Profile</h1> -->
    <!-- The Modal (popup overlay) -->
    <div id="uploadfile" class="modal" style="display: none !important;">
        <div class="modal-content">

            <h2>Upload Profile <span class="close" style="color:red;text-align: right;"
                    onclick="uploadimagefunction(1)">&times;</span></h2>
            <form action="temp.php" method="POST" enctype="multipart/form-data" class="upload-form">
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
                <form action="temp.php" method="GET" enctype="multipart/form-data" class="searchform"
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


                <a href="temp.php"><i class="fa-solid fa-rotate-right" style="
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
                                    <a href="temp.php?delete=<?php echo $row['id']; ?>">Delete</a>
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
    <!-- <form action="temp.php" method="GET" class="items-per-page-form">
        <label1 for="items_per_page">Items per page:</label1>
        <select name="items_per_page" id="items_per_page" onchange="this.form.submit()">
            <option value="5" <?php echo $itemsPerPage == 5 ? 'selected' : ''; ?>>5</option>
            <option value="10" <?php echo $itemsPerPage == 10 ? 'selected' : ''; ?>>10</option>
            <option value="15" <?php echo $itemsPerPage == 15 ? 'selected' : ''; ?>>15</option>
            <option value="20" <?php echo $itemsPerPage == 20 ? 'selected' : ''; ?>>20</option>
        </select>
        <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="temp.php?page=<?php echo $page - 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="temp.php?page=<?php echo $i; ?>&items_per_page=<?php echo $itemsPerPage; ?>" <?php if ($i == $page)
                      echo 'class="active"'; ?>>
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="temp.php?page=<?php echo $page + 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>">Next</a>
        <?php endif; ?>
    </div>
    </form> -->
    <form action="temp.php" method="GET" class="items-per-page-form" style="display: flex; align-items: center; gap: 10px;">
    <label for="items_per_page" style="margin-right: 5px;">Items per page:</label>
    <select name="items_per_page" id="items_per_page" onchange="this.form.submit()" style="padding: 5px;">
        <option value="5" <?php echo $itemsPerPage == 5 ? 'selected' : ''; ?>>5</option>
        <option value="10" <?php echo $itemsPerPage == 10 ? 'selected' : ''; ?>>10</option>
        <option value="15" <?php echo $itemsPerPage == 15 ? 'selected' : ''; ?>>15</option>
        <option value="20" <?php echo $itemsPerPage == 20 ? 'selected' : ''; ?>>20</option>
    </select>

    <div class="pagination" style="display: flex; align-items: center; gap: 10px;">
        <?php if ($page > 1): ?>
            <a href="temp.php?page=<?php echo $page - 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>" style="text-decoration: none; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px;">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="temp.php?page=<?php echo $i; ?>&items_per_page=<?php echo $itemsPerPage; ?>" 
               <?php if ($i == $page) echo 'class="active"'; ?>
               style="text-decoration: none; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; <?php if ($i == $page) echo 'background-color: #007bff; color: white;'; ?> ">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="temp.php?page=<?php echo $page + 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>" style="text-decoration: none; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px;">Next</a>
        <?php endif; ?>
    </div>
</form>

    <!-- <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="temp.php?page=<?php echo $page - 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="temp.php?page=<?php echo $i; ?>&items_per_page=<?php echo $itemsPerPage; ?>" <?php if ($i == $page)
                      echo 'class="active"'; ?>>
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="temp.php?page=<?php echo $page + 1; ?>&items_per_page=<?php echo $itemsPerPage; ?>">Next</a>
        <?php endif; ?>
    </div> -->
    </div>
                </div>
        <!-- Start app Footer part -->
        <footer class="main-footer">
            <div class="footer-left">
                 <div class="bullet"></div>  <a href="https://kakatiyasolutions.in/vageshwari_clg/" target="_blank">VAAGESWARI COLLEGE OF ENGINEERING</a>
            </div>
            <div class="footer-right">
                <div class="bullet"></div>Design & Developed By  <a href="https://kakatiyasolutions.com/" target="_blank">Kakatiya Slutions</a>
            </div>
        </footer>
    
    </div>
</div>
    <!-- <h1>Update Department Details</h1> -->
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

    <script>
//     document.querySelectorAll('.sidebar-menu .dropdown > a').forEach(function(item) {
//     item.addEventListener('click', function(e) {
//         var submenu = this.nextElementSibling;
//         if (submenu && submenu.classList.contains('submenu')) {
//             submenu.style.display = (submenu.style.display === 'block' ? 'none' : 'block');
//         }
//     });
// });
document.querySelectorAll('.sidebar-menu .dropdown > a').forEach(function(item) {
    item.addEventListener('click', function(e) {
        // Toggle the 'open' class for the dropdown
        var parentDropdown = this.parentElement;
        parentDropdown.classList.toggle('open');
        
        // Toggle the display of the submenu
        var submenu = this.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) {
            submenu.style.display = (submenu.style.display === 'block' ? 'none' : 'block');
        }
    });
});


</script>
    <script src="../assets/bundles/lib.vendor.bundle.js"></script>
<script src="../js/CodiePie.js"></script>

<!-- JS Libraies -->
<script src="../assets/modules/jquery.sparkline.min.js"></script>
<script src="../assets/modules/chart.min.js"></script>
<script src="../assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
<script src="../assets/modules/summernote/summernote-bs4.js"></script>
<script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

<!-- Page Specific JS File -->
<script src="../js/page/index.js"></script>

<!-- Template JS File -->
<script src="../js/scripts.js"></script>
<script src="../js/custom.js"></script>
<!-- <script src="js/ckeditor.js"></script> -->

<!-- // <script>
//     const editorIds = ['description', 'vision', 'mission', 'peo', 'popso', 'advisory', 'po','board_of_studies', 'achivements'];
//     editorIds.forEach(id => {
//         ClassicEditor.create(document.querySelector('#' + id), {
//             // toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable'],
//             toolbar: [
//                 'bold', 
//                 'italic', 
//                 'link', 
//                 'bulletedList', 
//                 'numberedList', 
//                 'blockQuote', 
//                 'insertTable', 
//                 'outdent', 
//                 'indent', 
//                 'todoList', // Adds task list support
//                 'listStyle', // Allows for different list styles
//                 'alignment', // Adds text alignment controls
//                 'fontSize', // Allows changing the font size
//                 'fontColor', // Custom text color options
//                 'highlight', // Text highlighting option
//                 'fontFamily', // Allows font family selection
//                 'underline', // Underlines text
//                 'strikethrough', // Strikes through text
//                 'subscript', // Adds subscript formatting
//                 'superscript', // Adds superscript formatting
//                 'code', // Adds inline code formatting
//                 'insertImage', // Image insertion support
//                 'insertVideo', // Video embedding
//                 'blockquote', // Blockquote option for citing sources
//                 'clearFormatting', // Clears all text formatting
//             ],
              
              
//             table: {
//                 contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties'],
//                 defaultProperties: {
//                     table: {
//                         classes: 'table table-bordered table-striped' // Set the desired default classes
//                     }
//                 }
//             },
//             htmlSupport: {
//                 allow: [
//                     { name: 'table', attributes: true, classes: true, styles: true },
//                     { name: 'thead', attributes: true, classes: true, styles: true },
//                     { name: 'tbody', attributes: true, classes: true, styles: true },
//                     { name: 'tr', attributes: true, classes: true, styles: true },
//                     { name: 'td', attributes: true, classes: true, styles: true },
//                     { name: 'th', attributes: true, classes: true, styles: true }
//                 ],
//                 disallow: [
//                 {
//                     name: 'figure',
//                     attributes: true,
//                     classes: true,
//                     styles: true
//                 }
//             ]
//             }
//         }).catch(error => console.error(`Error initializing CKEditor for #${id}:`, error));
//     });
// </script> -->
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script> -->
<!-- <script src="my-ckeditor-porject/ckeditor/ckeditor.js"></script> -->
<!-- <script src="my-ckeditor-project/ckeditor/ckeditor.js"></script> -->
<!-- <script src="my-ckeditor-project/ckeditor/ckeditor.js"></script> -->


<script>
     console.log(window.ClassicEditor); 
    const editorIds = 
    ['id','branch_name', 'lab_name', 'configuration_details', 'course', 'number_of_systems', 'lab_details', 'lab_image', 'laboratories', 'syllabus','timetable', 'academic_calendar'];
    // const editorIds = ['selectAll', 'undo', 'redo', 'bold', 'italic', 'blockQuote', 'link', 'ckfinder', 'uploadImage', 'imageUpload', 'heading', 'imageTextAlternative', 'toggleImageCaption', 'imageStyle:inline', 'imageStyle:alignLeft', 'imageStyle:alignRight', 'imageStyle:alignCenter', 'imageStyle:alignBlockLeft', 'imageStyle:alignBlockRight', 'imageStyle:block', 'imageStyle:side', 'imageStyle:wrapText', 'imageStyle:breakText', 'indent', 'outdent', 'numberedList', 'bulletedList', 'mediaEmbed', 'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells'];
    editorIds.forEach(id => {
        ClassicEditor.create(document.querySelector('#' + id), {
            // toolbar: [
            //     'bold', 
            //     'italic', 
            //     'link', 
            //     'bulletedList', 
            //     'numberedList', 
            //     'blockQuote', 
            //     'insertTable', 
            //     'outdent', 
            //     'indent', 
            //     'todoList', // Adds task list support
            //     'listStyle', // Allows for different list styles
            //     'alignment', // Adds text alignment controls
            //     'fontSize', // Allows changing the font size
            //     'fontColor', // Custom text color options
            //     'highlight', // Text highlighting option
            //     'fontFamily', // Allows font family selection
            //     'underline', // Underlines text
            //     'strikethrough', // Strikes through text
            //     'subscript', // Adds subscript formatting
            //     'superscript', // Adds superscript formatting
            //     'code', // Adds inline code formatting
            //     'insertImage', // Image insertion support
            //     'insertVideo', // Video embedding
            //     'blockquote', // Blockquote option for citing sources
            //     'clearFormatting', // Clears all text formatting
            // ],
            toolbar: ['selectAll', 'undo', 'redo', 'bold', 'italic', 'blockQuote', 'link', 'ckfinder', 'uploadImage', 'imageUpload', 'heading', 'imageTextAlternative', 'toggleImageCaption', 'imageStyle:inline', 'imageStyle:alignLeft', 'imageStyle:alignRight', 'imageStyle:alignCenter', 'imageStyle:alignBlockLeft', 'imageStyle:alignBlockRight', 'imageStyle:block', 'imageStyle:side', 'imageStyle:wrapText', 'imageStyle:breakText', 'indent', 'outdent', 'numberedList', 'bulletedList', 'mediaEmbed', 'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells'],

            // Configure Font Plugin
            fontSize: {
                options: [
                    8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30
                ]
            },
            fontFamily: {
                options: [
                    'default', 'Arial', 'Courier New', 'Georgia', 'Lucida Sans Unicode', 'Tahoma', 'Times New Roman', 'Verdana'
                ]
            },

            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties'],
                defaultProperties: {
                    table: {
                        classes: 'table table-bordered table-striped' // Set the desired default classes
                    }
                }
            },

            htmlSupport: {
                allow: [
                    { name: 'table', attributes: true, classes: true, styles: true },
                    { name: 'thead', attributes: true, classes: true, styles: true },
                    { name: 'tbody', attributes: true, classes: true, styles: true },
                    { name: 'tr', attributes: true, classes: true, styles: true },
                    { name: 'td', attributes: true, classes: true, styles: true },
                    { name: 'th', attributes: true, classes: true, styles: true }
                ],
                disallow: [
                    {
                        name: 'figure',
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        }).then(editor => {
    // List all available component names in the editor's UI
    // console.log(Array.from(editor.ui.componentFactory.names()));
}).catch(error => console.error(`Error initializing CKEditor for #${id}:`, error));
    });
</script>


</body>
</html>

