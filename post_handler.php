<?php
    session_start();
    require('conn.php');
    if (isset($_POST['click'])){
        $conn -> query(sprintf("UPDATE NPOpCat SET clicks = %d;", mysqli_fetch_assoc($conn -> query("SELECT * FROM NPOpCat;"))['clicks'] + 1));
        echo mysqli_fetch_assoc($conn -> query("SELECT * FROM NPOpCat;"))['clicks'];
    } else if (isset($_POST['deactivate'])){
        $conn -> query(sprintf("UPDATE members SET activated = 0 WHERE id = '%s';", $_POST['id']));
        unset($_SESSION['acc']);
    } else if (isset($_POST['changeVisible'])){
        $conn -> query(sprintf("UPDATE members SET tribeVisible = '%d' WHERE id = '%s';", $_POST['visible'], $_POST['id']));
    } else if (isset($_POST['tribeEdit'])){
        $conn -> query(sprintf("UPDATE members SET tribe = '%s' WHERE id = '%s';", $_POST['tribe'], $_POST['id']));
    } else if (isset($_POST['editGoods'])){
        $conn -> query(sprintf("UPDATE goods SET name = '%s', price = '%d', intro = '%s', link = '%s', besold = '%d' WHERE gid = '%d';", $_POST['name'], $_POST['price'], $_POST['intro'], $_POST['link'], $_POST['besold'], $_POST['id']));
    } else if (isset($_POST['adminEditConfirmed'])){
        $conn -> query(sprintf("UPDATE members SET account = '%s', password = '%s', username = '%s', email = '%s' WHERE id = '%d';", $_POST['account'], $_POST['password'], $_POST['username'], $_POST['email'], $_POST['id']));
    } else if (isset($_POST['adminDelete'])){
        $conn -> query(sprintf("DELETE FROM members WHERE id = '%d';", $_POST['id']));
    } else if (isset($_POST['adminEdit'])){
        $conn -> query(sprintf("UPDATE members SET %s = '%s' WHERE id = '%d';", $_POST['columnName'], $_POST['column'], $_POST['id']));
    } else if (isset($_POST['deleteFeedback'])){
        $conn -> query(sprintf("DELETE FROM feedback WHERE fid = '%d';", $_POST['id']));
    } else {
        header('location:/index.php');
    }
?>