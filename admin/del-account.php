<?php
	include 'include/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['id'];

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("DELETE FROM users WHERE id=:id");
			$stmt->execute(['id'=>$id]);

			$stmt = $conn->prepare("DELETE FROM players WHERE user_id=:userid");
			$stmt->execute(['userid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM agent_profiles WHERE user_id=:userid");
			$stmt->execute(['userid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM PODRatings WHERE player_id=:playerid");
			$stmt->execute(['playerid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM PlayerStats WHERE player_id=:playerid");
			$stmt->execute(['playerid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM chats WHERE user1_id=:userid OR user2_id=:userid");
			$stmt->execute(['userid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM messages WHERE user_id=:userid");
			$stmt->execute(['userid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM videos WHERE player_id=:userid");
			$stmt->execute(['userid'=>$id]);

			$stmt = $conn->prepare("DELETE FROM watchlist WHERE player_id=:userid OR agent_id=:userid");
			$stmt->execute(['userid'=>$id]);

			$_SESSION['success'] = 'User deleted successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Select User to delete first';
	}

	header('location: players');

?>
