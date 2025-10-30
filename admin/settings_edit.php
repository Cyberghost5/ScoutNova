<?php
	include 'include/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$site_name = $_POST['site_name'];
		$site_url = $_POST['site_url'];
		$site_desc = $_POST['site_desc'];
		$site_keyword = $_POST['site_keyword'];
		$mode = $_POST['mode'];
		$color = $_POST['color'];
		$offset = $_POST['offset'];

		$conn = $pdo->open();
		$stmt = $conn->prepare("SELECT * FROM settings WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		try{
			$stmt = $conn->prepare("UPDATE settings SET site_name=:site_name, site_url=:site_url, site_desc=:site_desc, site_keyword=:site_keyword, mode=:mode, theme=:color, offset=:offset WHERE id=:id");
			$stmt->execute(['site_name'=>$site_name, 'site_url'=>$site_url, 'site_desc'=>$site_desc, 'site_keyword'=>$site_keyword, 'mode'=>$mode, 'color'=>$color, 'offset'=>$offset, 'id'=>$id]);
			$_SESSION['success'] = 'Settings updated successfully';

		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}


		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up settings form first';
	}

	header('location: gen-settings');

?>
