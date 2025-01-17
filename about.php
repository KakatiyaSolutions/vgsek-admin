<?php
// Connect to the database
$con = mysqli_connect('srv1328.hstgr.io', 'u629694569_vcpkacin_web', 'Kakatiya@1243', 'u629694569_vcpkacin_web');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data for the CSE department
$query = "SELECT sno, department, description, status, vision, mission, peo, po_pso, advisory_board 
          FROM dep_about WHERE department_name = 'CSE'";
$result = mysqli_query($con, $query);

// Initialize variables
$department = $vision = $mission = $description = $peo = $popso = $advisory = "";
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $department = $row['department'];
    $vision = htmlspecialchars_decode($row['vision']);
    $mission = htmlspecialchars_decode($row['mission']);
    $description = htmlspecialchars_decode($row['description']);
    $peo = htmlspecialchars_decode($row['peo']);
    $popso = htmlspecialchars_decode($row['po_pso']);
    $advisory = htmlspecialchars_decode($row['advisory_board']);
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update PEO</title>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
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
    background-color: #fff;
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

</style>
</head>
<body>
    <h1>Update Department Details</h1>
    <form action="update_dep_about.php" method="POST">
        <label for="description">Description:</label><br>
        <textarea name="description" id="description"><?php echo $description; ?></textarea><br><br>

        <label for="vision">Vision:</label><br>
        <textarea name="vision" id="vision"><?php echo $vision; ?></textarea><br><br>

        <label for="mission">Mission:</label><br>
        <textarea name="mission" id="mission"><?php echo $mission; ?></textarea><br><br>

        <label for="peo">Program Educational Objectives (PEO):</label><br>
        <textarea name="peo" id="peo"><?php echo $peo; ?></textarea><br><br>

        <label for="popso">Program Outcomes (PO) & Program Specific Outcomes (PSO):</label><br>
        <textarea name="popso" id="popso"><?php echo $popso; ?></textarea><br><br>

        <label for="advisory">Advisory Board:</label><br>
        <textarea name="advisory" id="advisory"><?php echo $advisory; ?></textarea><br><br>

        <button type="submit">Update</button>
    </form>

    <!-- <script>
        const editors = ['description', 'vision', 'mission', 'peo', 'popso', 'advisory'];
        editors.forEach(id => {
            ClassicEditor.create(document.querySelector('#' + id), {
                allowedContent: true, // Allows all HTML, styles, and classes
                extraAllowedContent: '*{*}[*]', // Allow styles, classes, and attributes
            }).catch(error => console.error(error));
        });
    </script> -->
    <!-- <script>
    const editorIds = ['description', 'vision', 'mission', 'peo', 'popso', 'advisory']; // List of all IDs
    editorIds.forEach(id => {
        ClassicEditor.create(document.querySelector('#' + id), {
            toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable'],
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            },
            htmlSupport: {
                allow: [
                    {
                        name: 'table',
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: 'thead',
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: 'tbody',
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: 'tr',
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: 'td',
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: 'th',
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        }).catch(error => console.error(`Error initializing CKEditor for #${id}:`, error));
    });
</script> -->

<script>
    const editorIds = ['description', 'vision', 'mission', 'peo', 'popso', 'advisory'];
    editorIds.forEach(id => {
        ClassicEditor.create(document.querySelector('#' + id), {
            // toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable'],
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

