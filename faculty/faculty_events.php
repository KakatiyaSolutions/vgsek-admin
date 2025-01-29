<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$department = isset($_GET['department']) ? $_GET['department'] : 'DS'; // Default to 'EEE'

// Fetch data for the selected department
$query = "SELECT sno, title, department, department_name, description, status
          FROM faculty_event2 WHERE department_name LIKE ?";
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
        // Count how many times "EEE" appears
        if (strpos($row['department_name'], $department) !== false) {
            $eee_count++;
            // Store the form data for each record where "EEE" appears
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
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>VAAGESWARI COLLEGE OF ENGINEERING</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Template CSS -->
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
</style>

</head>
<body>
   
<div class="main-content">
    <section class="section">
        <h1>Update Department Details of <?php echo htmlspecialchars($department); ?></h1>

        <!-- Display how many times "EEE" appears -->
        <p><strong>EEE is found <?php echo $eee_count; ?> times in the department records.</strong></p>

        <!-- Render a form for each row that contains "EEE" -->
        <?php foreach ($forms as $index => $form) : ?>
            <form action="update_faculty_event.php?department=<?php echo urlencode($form['department_name']); ?>" method="POST">
            <input type="hidden" name="sno" id="sno" value="<?php echo $form['sno']; ?>">    
           
            <!-- Title Field with Edit Functionality -->
            <label for="title-<?php echo $index; ?>"><?php echo htmlspecialchars(strip_tags($form['title'])); ?></label>
            <span class="edit-icon" onclick="toggleEdit('title-<?php echo $index; ?>')">&#9998;</span><br>

            <!-- Initially hidden Textarea for Title -->
            <textarea name="title" id="title-<?php echo $index; ?>" class="hidden-textarea"><?php echo htmlspecialchars($form['title']); ?></textarea><br><br>

            <!-- Description Field with CKEditor Initialized from the Start -->
            <!-- <label for="description-<?php echo $index; ?>">Description:</label><br> -->
            <textarea name="description" id="description-<?php echo $index; ?>"><?php echo htmlspecialchars($form['description']); ?></textarea><br><br>

            <input type="hidden" name="department_name" value="<?php echo $form['department_name']; ?>">
            <button type="submit" class="btnsubmit" style="display: block; margin: 0 auto;">Update</button>
            </form>
            <br><hr><br>
        <?php endforeach; ?>
        <div id="form-container">
    <!-- Existing rows will be inserted here -->
</div>
          <!-- Button to Add New Row -->
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

<script>
    // Initialize CKEditor for Description field as soon as the page loads
    <?php foreach ($forms as $index => $form) : ?>
        ClassicEditor.create(document.querySelector('#description-<?php echo $index; ?>'))
            .catch(error => console.error(`Error initializing CKEditor for description-<?php echo $index; ?>:`, error));
    <?php endforeach; ?>

    // Initialize CKEditor only for the Title textarea when it's revealed
    // let editors = {};

    // function toggleEdit(textareaId) {
    //     var textarea = document.getElementById(textareaId);
    //     if (textarea.style.display === "none" || textarea.style.display === "") {
    //         textarea.style.display = "block";  // Show textarea
            
    //         // Initialize CKEditor for the title textarea only if it's not already initialized
    //         if (!editors[textareaId]) {
    //             ClassicEditor.create(textarea)
    //                 .then(editor => {
    //                     editors[textareaId] = editor; // Save the instance to prevent re-initialization
    //                 })
    //                 .catch(error => console.error(`Error initializing CKEditor for ${textareaId}:`, error));
    //         }
    //     } else {
    //         textarea.style.display = "none";  // Hide textarea
    //     }
    // }
    // Object to keep track of initialized editors to avoid re-initialization
let editors = {};

function toggleEdit(textareaId) {
    var textarea = document.getElementById(textareaId);

    // If the textarea is currently hidden or has no display set, show it
    if (textarea.style.display === "none" || textarea.style.display === "") {
        textarea.style.display = "block";  // Show the textarea

        // Initialize CKEditor for the title textarea only if it's not already initialized
        if (!editors[textareaId]) {
            ClassicEditor.create(textarea)
                .then(editor => {
                    editors[textareaId] = editor; // Save the instance to prevent re-initialization
                })
                .catch(error => console.error(`Error initializing CKEditor for ${textareaId}:`, error));
        }
    } else {
        // If textarea is already shown, hide it and destroy CKEditor instance
        textarea.style.display = "none";  // Hide the textarea

        // Destroy CKEditor instance when the textarea is hidden
        if (editors[textareaId]) {
            editors[textareaId].destroy()
                .then(() => {
                    delete editors[textareaId]; // Remove the editor instance from the tracker
                })
                .catch(error => console.error(`Error destroying CKEditor for ${textareaId}:`, error));
        }
    }
}


function addNewRow() {
            const newRowForm = `
                <form class="new-row-form" id="new-row-form">
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

            // Initialize CKEditor for new description field
            ClassicEditor.create(document.querySelector('#new-description'))
                .catch(error => console.error(`Error initializing CKEditor for new-description:`, error));
            ClassicEditor.create(document.querySelector('#new-title'))
                .catch(error => console.error(`Error initializing CKEditor for new-title:`, error));
        }

        // Save the new row via AJAX
        function saveNewRow() {
            const department = '<?php echo urlencode($department); ?>';
    const title = document.querySelector('#new-title').value;
    const description = document.querySelector('#new-description').value;

    const data = new FormData();
    data.append('title', title);
    data.append('description', description);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_faculty_event.php?department=' + department, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('New row added successfully!');
            // Optionally reload the page or update UI to show the new row
        } else {
            alert('Error adding new row: ' + xhr.status + " " + xhr.statusText);
            console.error('Error details:', xhr.responseText);
        }
    };

    xhr.send(data);
}
</script>

</body>
</html>
