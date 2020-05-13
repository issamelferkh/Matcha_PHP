<!-- connection -->
<?php require_once("../config/connection.php"); ?>
<!-- session -->
<?php require_once("../include/session.php"); ?>
<!-- libft -->
<?php require_once("../include/libft.php"); ?>

<?php
	if(isset($_GET["user"]) && isset($_GET["action"]) && ($_SESSION["token"] === $_GET["token"])) {
		// add like or nope to like_table
		$user_p = $_SESSION["user_id"]; // principal user
		$user_o = htmlspecialchars(trim($_GET["user"])); // other user
		$_GET["action"] === "liked" ? $liked = 1 : $liked = 0;
		$_GET["action"] === "noped" ? $noped = 1 : $noped = 0;
		$_GET["action"] === "reported" ? $reported = 1 : $reported = 0;
		$_GET["action"] === "blocked" ? $blocked = 1 : $blocked = 0;

		$query2 = 'INSERT INTO `like_table` (`user_p`, `user_o`, `liked`, `noped`, `reported`, `blocked`) VALUES (?,?,?,?,?,?)';
        $query2 = $db->prepare($query2);
		$query2->execute([$user_p,$user_o,$liked,$noped,$reported,$blocked]);
		
		// update popularity
			// calcul total (likes + nopes)
			$query = 'SELECT * FROM `like_table` WHERE `user_o`="'.$user_o.'"';
			$query = $db->prepare($query);
			$query->execute();
			$total = $query->rowCount();
			// calcul likes
			$query = 'SELECT * FROM `like_table` WHERE `user_o`="'.$user_o.'" AND `liked` = 1';
			$query = $db->prepare($query);
			$query->execute();
			$likes = $query->rowCount();
			// calcul popularity %
			$popularity = $likes/$total*100;
		// update popularity in user table
			$update_popularity = $db->query("UPDATE `user` SET `popularity` = $popularity WHERE `user_id` =".$user_o);

		header("Location: profile_detail.php?id=".$user_o);
	
} else {
	// 404
	// msg csrf detected !
	header('Location: index.php');
}
?>