<?php
session_start();
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if(empty($name) || empty($email) || empty($password)) {
        echo "Please fill out all fields";
        exit;
    }
    if(!validateEmail($email)) {
        echo "Please enter a valid email address";
        exit;
    }

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $unique_filename = uniqid() . '_' . date('YmdHis') . '.' . $imageFileType;
    $target_file = $target_dir . $unique_filename;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $csv_data = array($name, $email, $unique_filename);
        $fp = fopen('users.csv', 'a');
        fputcsv($fp, $csv_data);
        fclose($fp);
        
        $_SESSION['name'] = $name;
        setcookie('name', $name, time()+3600);
        
        echo "Profile picture uploaded and data saved successfully";
    } else {
        echo "Error uploading file";
    }
}
?>

<html>
<head>
    <title>Profile Form</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name"><br><br>
        <label>Email:</label>
        <input type="email" name="email"><br><br>
        <label>Password:</label>
        <input type="password" name="password"><br><br>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture"><br><br>
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>


<!DOCTYPE html>
<html>

<head>
	<title>User Data</title>
</head>

<body>
	<table>
		<tr>
			<th>Name</th>
			<th>Email</th>
			<th>Profile Picture</th>
		</tr>
		<?php
		  $file = fopen('users.csv', 'r');
		  while (($data = fgetcsv($file)) !== FALSE) {
			echo '<tr>';
			echo '<td>' . $data[0] . '</td>';
			echo '<td>' . $data[1] . '</td>';
			echo '<td><img src="uploads/' . $data[2] . '" width="100"></td>';
			echo '</tr>';
		  }
		  fclose($file);
		?>
	</table>

</body>

</html>