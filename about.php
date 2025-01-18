<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$active_department = isset($_GET['department']) ? $_GET['department'] : 'EEE';
$department = isset($_GET['department']) ? $_GET['department'] : 'CSE'; // Default to 'CSE'

// Fetch data for the selected department
$query = "SELECT sno, po, department,department_name, description, status, vision, mission, peo, po_pso,  advisory_board , achivements, board_of_studies
          FROM dep_about WHERE department_name = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

// Initialize variables
$department = $department_name = $vision = $mission = $description = $peo = $po = $popso = $board_of_studies = $achivements = $advisory = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $department = $row['department'];
    $department_name = $row['department_name'];
    $vision = htmlspecialchars_decode($row['vision']);
    $mission = htmlspecialchars_decode($row['mission']);
    $description = htmlspecialchars_decode($row['description']);
    $peo = htmlspecialchars_decode($row['peo']);
    $po = htmlspecialchars_decode($row['po']);
    $popso = htmlspecialchars_decode($row['po_pso']);
    $advisory = htmlspecialchars_decode($row['advisory_board']);
    // $board_of_studies = htmlspecialchars_decode($row['board_of_studies']);
    // $achivements = htmlspecialchars_decode($row['achivements']);
    $achivements = isset($row['achivements']) ? htmlspecialchars_decode($row['achivements']) : "";
$board_of_studies = isset($row['board_of_studies']) ? htmlspecialchars_decode($row['board_of_studies']) : "";

}

$stmt->close();
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>VAAGESWARI COLLEG OF ENGINEERING</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- General CSS Files -->
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- CSS Libraries -->
<link rel="stylesheet" href="assets/modules/jqvmap/dist/jqvmap.min.css">
<link rel="stylesheet" href="assets/modules/summernote/summernote-bs4.css">
<link rel="stylesheet" href="assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
<link rel="stylesheet" href="assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">

<!-- Template CSS -->
<link rel="stylesheet" href="assets/css/style.min.css">
<link rel="stylesheet" href="assets/css/components.min.css">
<style>
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
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-book"></i><span>Syllabus</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fas fa-calendar-check"></i><span>Timetables</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-calendar-days"></i><span>Academic Calendar</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-flask-vial"></i><span>Laboratories</span></a></li>
                    </ul>
                </li>
                 <!--  droopdown for M.Tech course -->
                 <li class="dropdown">
                    <a class="nav-link" ><i class="fa-solid fa-graduation-cap"></i> <span>M.Tech Courses</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                    <ul class="submenu">
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-book"></i><span>Syllabus</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fas fa-calendar-check"></i><span>Timetables</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-calendar-days"></i><span>Academic Calendar</span></a></li>
                        <li><a href="#" style="padding-left: 0px;"><i class="fa-solid fa-flask-vial"></i><span>Laboratories</span></a></li>
                    </ul>
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
            <section class="section">
            <h1>Update Department Details of <?php echo htmlspecialchars($department_name); ?></h1>
    <form action="update_dep_about.php?department=<?php echo urlencode($department_name); ?>" method="POST">
        <label for="description">Description:</label><br>
        <textarea name="description" id="description"><?php echo $description; ?></textarea><br><br>

        <label for="vision">Vision:</label><br>
        <textarea name="vision" id="vision"><?php echo $vision; ?></textarea><br><br>

        <label for="mission">Mission:</label><br>
        <textarea name="mission" id="mission"><?php echo $mission; ?></textarea><br><br>

        <label for="peo">Program Educational Objectives (PEO):</label><br>
        <textarea name="peo" id="peo"><?php echo $peo; ?></textarea><br><br>

        <label for="po">Program Outcomes (PO) </label><br>
        <textarea name="po" id="po"><?php echo $po; ?></textarea><br><br>

        <label for="popso"> Program Specific Outcomes (PSO):</label><br>
        <textarea name="popso" id="popso"><?php echo $popso; ?></textarea><br><br>

        <label for="board_of_studies">Board Of Studies:</label><br>
        <textarea name="board_of_studies" id="board_of_studies"><?php echo $board_of_studies; ?></textarea><br><br>
        
        <label for="advisory">Advisory Board:</label><br>
        <textarea name="advisory" id="advisory"><?php echo $advisory; ?></textarea><br><br>
        
        <label for="achivements">Achivements:</label><br>
        <textarea name="achivements" id="achivements"><?php echo $achivements; ?></textarea><br><br>
        
        <input type="hidden" name="<?php echo $department_name; ?>" id="<?php echo $department_name; ?>" value="<?php echo $department_name; ?>">
        <button type="submit" class="btnsubmi "  style="display: block; margin: 0 auto;">Update</button>
    </form>
            </section>
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
    <script src="assets/bundles/lib.vendor.bundle.js"></script>
<script src="js/CodiePie.js"></script>

<!-- JS Libraies -->
<script src="assets/modules/jquery.sparkline.min.js"></script>
<script src="assets/modules/chart.min.js"></script>
<script src="assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
<script src="assets/modules/summernote/summernote-bs4.js"></script>
<script src="assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

<!-- Page Specific JS File -->
<script src="js/page/index.js"></script>

<!-- Template JS File -->
<script src="js/scripts.js"></script>
<script src="js/custom.js"></script>
// <script>
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
// </script>
// <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    const editorIds = ['description', 'vision', 'mission', 'peo', 'popso', 'advisory', 'po', 'board_of_studies', 'achivements'];

    editorIds.forEach(id => {
        ClassicEditor.create(document.querySelector('#' + id), {
            toolbar: [
                'bold', 
                'italic', 
                'link', 
                'bulletedList', 
                'numberedList', 
                'blockQuote', 
                'insertTable', 
                'outdent', 
                'indent', 
                'todoList', // Adds task list support
                'listStyle', // Allows for different list styles
                'alignment', // Adds text alignment controls
                'fontSize', // Allows changing the font size
                'fontColor', // Custom text color options
                'highlight', // Text highlighting option
                'fontFamily', // Allows font family selection
                'underline', // Underlines text
                'strikethrough', // Strikes through text
                'subscript', // Adds subscript formatting
                'superscript', // Adds superscript formatting
                'code', // Adds inline code formatting
                'insertImage', // Image insertion support
                'insertVideo', // Video embedding
                'blockquote', // Blockquote option for citing sources
                'clearFormatting', // Clears all text formatting
            ],

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
        }).catch(error => console.error(`Error initializing CKEditor for #${id}:`, error));
    });
</script>


</body>
</html>

