<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$active_department = isset($_GET['department']) ? $_GET['department'] :         'EEE';
$department = isset($_GET['department']) ? $_GET['department'] : 'CSE'; // Default to 'CSE'

// Fetch data for the selected department
$query = "SELECT sno, department,department_name, description, status
          FROM cos_pos WHERE department_name = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

// Initialize variables
$department = $department_name = $description= "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $department = $row['department'];
    $department_name = $row['department_name'];
    $description = htmlspecialchars_decode($row['description']);
    // $board_of_studies = htmlspecialchars_decode($row['board_of_studies']);
    // $achivements = htmlspecialchars_decode($row['achivements']);
}

$stmt->close();
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Events</title>
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
</head>
<body>
   
<div class="main-content">
            <section class="section">
            <h1>Update Student Results Department Details of <?php echo htmlspecialchars($department_name); ?></h1>
             <form action="update_cos_pos.php?department=<?php echo urlencode($department_name); ?>" method="POST">
        <label for="description">Description:</label><br>
        <textarea name="description" id="description"><?php echo $description; ?></textarea><br><br>

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


<!-- script goes here-->
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
    ['description'];
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