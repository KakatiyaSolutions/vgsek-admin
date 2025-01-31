<?php

 //include '../department_dropdown.php'; 
// Connect to the databas
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($con, 'utf8mb4');


 // Start the session
// session_start();
// // Check if the department is stored in the session
// if (isset($_SESSION['department'])) {
//     $department = $_SESSION['department'];
//     echo "Selected Department in Faculty Page: " . htmlspecialchars($department);
//     // Use $department as needed
// } else {
//     echo "No department selected.";
// }

// $department_query = "SELECT DISTINCT department FROM student_placements2";
$department_query = "SELECT DISTINCT department FROM departments WHERE status = 1";

$department_result = mysqli_query($con, $department_query);

// Get the selected department, default to 'EEE' if not selected
$department = isset($_GET['department']) ? $_GET['department'] : 'EEE'; 

// $department = isset($_GET['department']) ? $_GET['department'] : 'EEE'; // Default to 'DS'

// Fetch data for the selected department
$query = "SELECT sno, title, department, department_name, description, status
          FROM student_placements2 WHERE department_name LIKE ?";
$department_with_wildcards = "%" . $department . "%"; 
$stmt = $con->prepare($query);
$stmt->bind_param("s", $department_with_wildcards);
$stmt->execute();
$result = $stmt->get_result();

// Initialize counter for EEE and container for forms
$eee_count = 0;
$forms = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Count how many times department appears
        if (strpos($row['department_name'], $department) !== false) {
            $eee_count++;
            // Store the form data for each record
            $forms[] = $row;
        }
    }
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
    <link rel="stylesheet" href="faculty.css"> <!-- Link to external CSS file -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <!-- <meta charset="UTF-8"> -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.min.css">
    <link rel="stylesheet" href="assets/css/components.min.css">
    
    <style>
        .hidden-textarea {
            display: none;
            width: 100%;
            height: 100px;
        }

        .edit-icon {
            cursor: pointer;
            color: blue;
            font-size: 18px;
            margin-left: 5px;
        }

        .edit-icon:hover {
            color: darkblue;
        }
        .accordion-content {
            display: none;
            margin-top: 10px;
        }
        .accordion-content.active {
            display: block;
        }
    </style>
</head>
<body>
   
<div class="main-content">
    <section class="section">
        <!-- <h1>Update Department Details of <?php echo htmlspecialchars($department); ?></h1> -->
 <!-- Department Selection Dropdown -->
        <form method="GET" action="">
        <h1>Update Department Details of <?php echo htmlspecialchars($department); ?></h1>
            <label for="department"><span style="margin:right"> Select Department:</span> </label>
            <select name="department" id="department" onchange="this.form.submit()" style="padding: 1rem;">
                <option value="">-- Select Department --</option>
                <?php while ($row = mysqli_fetch_assoc($department_result)) : ?>
                    <option value="<?php echo $row['department']; ?>" <?php echo ($department == $row['department']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['department']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <p><strong><?php echo htmlspecialchars($department); ?> is found <?php echo $eee_count; ?> times in the department records.</strong></p>

        <!-- Render a form for each existing row -->
        <?php foreach ($forms as $index => $form) : ?>
            <form action="update_student_placement.php?department=<?php echo urlencode($form['department_name']); ?>" method="POST">
            <input type="hidden" name="sno" id="sno" value="<?php echo $form['sno']; ?>">    
<!-- . '-' . htmlspecialchars($form['sno']) -->
            <label id="titles" for="title-<?php echo $index; ?>"onclick="toggleEditor('description-<?php echo $index; ?>')"><?php echo htmlspecialchars(strip_tags($form['title'])); ?></label>
            <span class="edit-icon" onclick="toggleEdit('title-<?php echo $index; ?>')">&#9998;</span><br>
            <textarea name="title" id="title-<?php echo $index; ?>" class="hidden-textarea"><?php echo htmlspecialchars($form['title']); ?></textarea><br><br>

            <!-- <textarea name="description" id="description-<?php echo $index; ?>"><?php echo htmlspecialchars($form['description']); ?></textarea><br><br> -->

            <div class="accordion-content" id="description-<?php echo $index; ?>-container">
                    <textarea name="description" id="description-<?php echo $index; ?>" class="hidden-textarea"><?php echo htmlspecialchars($form['description']); ?></textarea><br><br>
                </div>
            <input type="hidden" name="department_name" value="<?php echo $form['department_name']; ?>">
            <button type="submit" class="btnsubmit" style="display: block; margin: 0 auto;">Update</button>
            </form>
            <br><hr><br>
        <?php endforeach; ?>

        <div id="form-container"></div> <!-- New rows will be inserted here -->
        <button class="btn btn-success" onclick="addNewRow()">Add New Row</button>
    </section>
</div>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-left">
        <div class="bullet"></div>  
        <a href="https://kakatiyasolutions.in/vageshwari_clg/" target="_blank">VAAGESWARI COLLEGE OF ENGINEERING</a>
    </div>
    <div class="footer-right">
        <div class="bullet"></div>Design & Developed By  
        <a href="https://kakatiyasolutions.com/" target="_blank">Kakatiya Solutions</a>
    </div>
</footer>

<script src="assets/bundles/lib.vendor.bundle.js"></script>
<script src="js/CodiePie.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize CKEditor for existing description fields
    <?php foreach ($forms as $index => $form) : ?>
        ClassicEditor.create(document.querySelector('#description-<?php echo $index; ?>'))
            .catch(error => console.error(`Error initializing CKEditor for description-<?php echo $index; ?>:`, error));
    <?php endforeach; ?>

    // Toggle edit function to show/hide the textarea for titles
    let editors = {};
    function toggleEdit(textareaId) {
        var textarea = document.getElementById(textareaId);
        if (textarea.style.display === "none" || textarea.style.display === "") {
            textarea.style.display = "block";
            if (!editors[textareaId]) {
                ClassicEditor.create(textarea)
                    .then(editor => { editors[textareaId] = editor; })
                    .catch(error => console.error(`Error initializing CKEditor for ${textareaId}:`, error));
            }
        } else {
            textarea.style.display = "none";
            if (editors[textareaId]) {
                editors[textareaId].destroy()
                    .then(() => { delete editors[textareaId]; })
                    .catch(error => console.error(`Error destroying CKEditor for ${textareaId}:`, error));
            }
        }
    }

    
    // Toggle editor visibility (accordion effect)
    // function toggleEditor(editorId) {
    //     const containerId = editorId + "-container"; // Container that holds the editor
    //     const editorContainer = document.getElementById(containerId);

    //     // Toggle active class for accordion effect
    //     editorContainer.classList.toggle('active');

    //     const textarea = document.getElementById(editorId);
    //     if (textarea.style.display === "none" || textarea.style.display === "") {
    //         textarea.style.display = "block";
    //         document.getElementById("titles").style.backgroundColor = "#F15E2E";
    //         // Initialize CKEditor if not already initialized
    //         if (!editors[editorId]) {
    //             ClassicEditor.create(textarea)
    //                 .then(editor => { editors[editorId] = editor; })
    //                 .catch(error => console.error(`Error initializing CKEditor for ${editorId}:`, error));
    //         }
    //     } else {
    //         textarea.style.display = "none";
    //         document.getElementById("titles").style.backgroundColor = "#1D3A6C !important";

    //         // Destroy CKEditor instance if the editor is hidden
    //         if (editors[editorId]) {
    //             editors[editorId].destroy()
    //                 .then(() => { delete editors[editorId]; })
    //                 .catch(error => console.error(`Error destroying CKEditor for ${editorId}:`, error));
    //         }
    //     }
    // }

    // working below code fine
//     function toggleEditor(editorId) {
//     const containerId = editorId + "-container"; // Container that holds the editor
//     const editorContainer = document.getElementById(containerId);

//     // Toggle active class for accordion effect
//     editorContainer.classList.toggle('active');

//     const textarea = document.getElementById(editorId);
//     const label = document.querySelector(`label[for="${editorId}"]`); // Get the label for the specific editor

//     if (textarea.style.display === "none" || textarea.style.display === "") {
//         textarea.style.display = "block";

//         // Change the background color of the label (title) for this editorId only
//         if (label) {
//             label.style.backgroundColor = "#F15E2E"; // Change background color to indicate active state
//         }

//         // Initialize CKEditor if not already initialized
//         // if (!editors[editorId]) {
//         //     ClassicEditor.create(textarea)
//         //         .then(editor => { editors[editorId] = editor; })
//         //         .catch(error => console.error(`Error initializing CKEditor for ${editorId}:`, error));
//         // }
//     } else {
//         textarea.style.display = "none";

//         // Reset the background color of the label when the editor is hidden
//         if (label) {
//             label.style.backgroundColor = "#1D3A6C"; // Reset to original background color
//         }

//         // Destroy CKEditor instance if the editor is hidden
//         // if (editors[editorId]) {
//         //     editors[editorId].destroy()
//         //         .then(() => { delete editors[editorId]; })
//         //         .catch(error => console.error(`Error destroying CKEditor for ${editorId}:`, error));
//         // }
//     }
// }

// testing new code of issues made chnage aof above code

// function toggleEditor(editorId) {
//     const containerId = editorId + "-container"; // Container that holds the editor
//     const editorContainer = document.getElementById(containerId);
//     const textarea = document.getElementById(editorId); // Get the textarea
//     const label = document.querySelector(`label[for="${editorId}"]`); // Get the label for the specific editor

//     // Toggle active class for accordion effect
//     editorContainer.classList.toggle('active');

//     // Only toggle the CKEditor and hide the textarea
//     if (textarea.style.display === "none" || textarea.style.display === "") {
//         textarea.style.display = "none";  // Ensure textarea is hidden

//         // Show CKEditor (ensure it's not already initialized)
//         if (!editors[editorId]) {
//             ClassicEditor.create(textarea)
//                 .then(editor => {
//                     editors[editorId] = editor; // Store the editor instance
//                 })
//                 .catch(error => console.error(`Error initializing CKEditor for ${editorId}:`, error));
//         }

//         // Change the background color of the label (title) for this editorId only
//         if (label) {
//             label.style.backgroundColor = "#F15E2E"; // Change background color to indicate active state
//         }
//     } else {
//         // Hide CKEditor and textarea
//         if (editors[editorId]) {
//             editors[editorId].destroy()
//                 .then(() => {
//                     delete editors[editorId]; // Clean up the editor instance
//                 })
//                 .catch(error => console.error(`Error destroying CKEditor for ${editorId}:`, error));
//         }

//         // Reset the background color of the label when the editor is hidden
//         if (label) {
//             label.style.backgroundColor = "#1D3A6C"; // Reset to original background color
//         }
//     }
// }
// same above more improving code

 editors = {};  // This object stores editor instances.

function toggleEditor(editorId) {
    const containerId = editorId + "-container"; // Container that holds the editor
    const editorContainer = document.getElementById(containerId);
    const textarea = document.getElementById(editorId);
    const label = document.querySelector(`label[for="${editorId}"]`); // Get the label for the specific editor

    // Toggle active class for accordion effect (show/hide)
    editorContainer.classList.toggle('active');

    // Only show the CKEditor (hide the textarea) when toggling
    if (textarea.style.display === "none" || textarea.style.display === "") {
        textarea.style.display = "none";  // Ensure the textarea is hidden

        // Only initialize CKEditor if it is not already initialized
        // if (!editors[editorId]) {
        //     // Initialize CKEditor only if not already created
        //     ClassicEditor.create(textarea)
        //         .then(editor => {
        //             editors[editorId] = editor; // Store the editor instance
        //         })
        //         .catch(error => console.error(`Error initializing CKEditor for ${editorId}:`, error));
        // }

        // Change the background color of the label for this editorId
        if (label) {
            label.style.backgroundColor = "#F15E2E"; // Change background color to indicate active state
        }
    } else {
        // If CKEditor is visible, hide it and remove its instance
        // if (editors[editorId]) {
        //     editors[editorId].destroy()
        //         .then(() => {
        //             delete editors[editorId]; // Delete the editor instance
        //         })
        //         .catch(error => console.error(`Error destroying CKEditor for ${editorId}:`, error));
        // }

        // Reset the background color of the label when the editor is hidden
        if (label) {
            label.style.backgroundColor = "#1D3A6C"; // Reset to original background color
        }

        // Hide the textarea when CKEditor is destroyed
        textarea.style.display = "none";
    }
}




    // Function to add new form row
    // function addNewRow() {
    //     const newRowForm = `
    //         <form class="new-row-form" id="new-row-form" action="update_student_placement.php?department=<?php echo urlencode($department); ?>" method="POST">
    //             <input type="hidden" name="sno" id="new-sno" value=""> 
    //             <label for="new-title">Title:</label><br>
    //             <textarea name="title" id="new-title" class="hidden-textarea"></textarea><br><br>

    //             <label for="new-description">Description:</label><br>
    //             <textarea name="description" id="new-description"></textarea><br><br>

    //             <button type="button" onclick="saveNewRow()">Save New Row</button>
    //         </form>
    //         <br><hr><br>
    //     `;
    //     document.getElementById('form-container').insertAdjacentHTML('beforeend', newRowForm);

    //     // Initialize CKEditor for new description field
    //     ClassicEditor.create(document.querySelector('#new-description'))
    //         .catch(error => console.error(`Error initializing CKEditor for new-description:`, error));
    //     ClassicEditor.create(document.querySelector('#new-title'))
    //         .catch(error => console.error(`Error initializing CKEditor for new-title:`, error));
    // }

    function addNewRow() {
    const newRowForm = `
        <form class="new-row-form" id="new-row-form" action="update_student_placement.php?department=<?php echo urlencode($department); ?>" method="POST">
            <input type="hidden" name="sno" id="new-sno" value=""> 
            <label for="new-title">Title:</label><br>
            <textarea name="title" id="new-title" class="hidden-textarea"></textarea><br><br>

            <label for="new-description">Description:</label><br>
            <textarea name="description" id="new-description"></textarea><br><br>

            <button type="button" onclick="saveNewRow()">Save New Row</button>
        </form>
        <br><hr><br>
    `;
    document.getElementById('form-container').insertAdjacentHTML('beforeend', newRowForm);

    // Initialize CKEditor for new title and description fields
    ClassicEditor.create(document.querySelector('#new-title'))
        .then(editor => {
            editors['new-title'] = editor;
        })
        .catch(error => console.error('Error initializing CKEditor for new-title:', error));

    ClassicEditor.create(document.querySelector('#new-description'))
        .then(editor => {
            editors['new-description'] = editor;
        })
        .catch(error => console.error('Error initializing CKEditor for new-description:', error));
}


    // Save the new row via AJAX
    // function saveNewRow() {
    //     const department = '<?php echo urlencode($department); ?>';
    //     // const title = document.querySelector('#new-title').value;
    //     // const description = document.querySelector('#new-description').value;
    //     const title = editors['new-title'] ? editors['new-title'].getData() : document.querySelector('#new-title').value;
    // const description = editors['new-description'] ? editors['new-description'].getData() : document.querySelector('#new-description').value;

    //     const data = new FormData();
    //     data.append('title', title);
    //     data.append('description', description);
    //     console.log("the title is: "+title);
    //     console.log("the description is:"+description);
    //     const xhr = new XMLHttpRequest();
    //     xhr.open('POST', 'update_student_placement.php?department=' + department, true);

    //     xhr.onload = function() {
    //         if (xhr.status === 200) {
    //             alert('New row added successfully!');
    //             // Optionally reload or update the UI to reflect the changes
    //         } else {
    //             alert('Error adding new row: ' + xhr.status + " " + xhr.statusText);
    //             console.error('Error details:', xhr.responseText);
    //         }
    //     };

    //     xhr.send(data);
    // }

    function saveNewRow() {
    const department = '<?php echo urlencode($department); ?>';
    
    // Retrieve content from CKEditor instances if they exist
    const title = editors['new-title'] ? editors['new-title'].getData() : document.querySelector('#new-title').value;
    const description = editors['new-description'] ? editors['new-description'].getData() : document.querySelector('#new-description').value;

    console.log("The title is: " + title);  // Check the title in console
    console.log("The description is: " + description);  // Check the description in console

    const data = new FormData();
    data.append('title', title);
    data.append('description', description);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_student_placement.php?department=' + department, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // alert('New row added successfully!');
            // // Optionally reload or update the UI to reflect the changes
            //    location.reload();  // This will reload the page

            Swal.fire({
                icon: 'success',
                title: 'New row added successfully!',
                showConfirmButton: true,
                // width: '100px',  // Note the quotes around the width value.
                height: '350px',  // Height property might not work as expected. 
                timer: 1500
            });

            // Reload the page after 1.5 seconds to reflect the new data
            setTimeout(function() {
                location.reload();  // Reload the page
            }, 1000);
        } else {
            // alert('Error adding new row: ' + xhr.status + " " + xhr.statusText);
            // console.error('Error details:', xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Error adding new row',
                text: xhr.status + " " + xhr.statusText,
                showConfirmButton: true
            });
            console.error('Error details:', xhr.responseText);
        }
    };

    xhr.send(data);
}

</script>

</body>
</html>
