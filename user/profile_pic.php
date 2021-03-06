<?php require_once("../config/connection.php"); ?>
<?php require_once("../include/session.php"); ?>
<?php require_once("../include/libft.php"); ?>

<!-- php update profile pic -->
<?php
if(isset($_POST['profile_pic'])) {
	if(isset($_POST["token"]) && ($_SESSION["token"] === $_POST["token"])) {
		// Upload new picture
		if(!($_FILES["imgUpload"]["tmp_name"] == '')){
			// check 5 pic in MAX
			$query = 'SELECT * FROM picture WHERE user_id="'.$_SESSION['user_id'].'"';
			$query = $db->prepare($query);
			$query->execute();
			$count = $query->rowCount();
			$la_case = $query->fetchAll(\PDO::FETCH_ASSOC); 
			if ($count < 5) {
				$imgName = $_SESSION['user_id']."_".date("Y_m_d_H_i_s")."_profile.png";
				$imgURL = "/assets/img/".$imgName;
				$imageFileType = strtolower(pathinfo($imgURL,PATHINFO_EXTENSION)); //holds the file extension of the file (in lower case)

				// Check if image file is a actual image or fake imagavatardsdsde
				$check = getimagesize($_FILES["imgUpload"]["tmp_name"]);
				if($check !== false) {
					imagepng(imagecreatefromstring(file_get_contents($_FILES["imgUpload"]["tmp_name"])), "..".$imgURL);

					$query = 'INSERT INTO `picture` (`user_id`, `username`, `imgName`, `imgURL`) VALUES (?,?,?,?)';
					$query = $db->prepare($query);
					$query->execute([$_SESSION['user_id'],$_SESSION['username'],$imgName,$imgURL]);

					// header("location:profile_pic.php");
				}
			} else {
				ft_putmsg('danger','Sorry! 5 pictures in max.','/user/profile_pic.php');
			}
		}

		// Update profile picture
		if(isset($_POST["asProfile"])) {
			// resete all 
			$asProfile = 0;
			$query = "UPDATE `picture` SET `asProfile`=? WHERE `user_id`=?";
			$query = $db->prepare($query);
			$query->execute([$asProfile,$_SESSION["user_id"]]);

			// make as profile pic
			$asProfile = 1;
			$img_id = $_POST["asProfile"];
			$query = "UPDATE `picture` SET `asProfile`=? WHERE `img_id`=?";
			$query = $db->prepare($query);
			$query->execute([$asProfile,$img_id]);
		}

		if(!isset($_POST["asProfile"]) && ($_FILES["imgUpload"]["tmp_name"] == '')) {
			ft_putmsg('danger','Choose new profile picture or upload a new!','/user/profile_pic.php');
		}
	} else {
		header("location: ../404.php");
	}
} 
?>

<!-- header -->
<?php include("../include/header.php"); ?>   
<!-- nav -->
<?php include("../include/navbar.php"); ?>

<!-- start container -->
<main role="main" class="container">   
	<?php include("../include/title.php"); ?>  
    <!-- Main -->
    <div class="my-3 p-3 bg-white rounded box-shadow">
        <div class="row">
		    <!-- Photo profile -->
            <div class="col-md-4">
            	<div class="my-3 p-3 bg-white rounded box-shadow">
			        <div class="media text-muted pt-3">
				        <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
				            <strong class="d-block text-gray-dark">Profile Picture</strong>
				        </p>
			        </div>
			    </div>
<!-- php profile picture -->
<?php
	$query = 'SELECT * FROM `picture` WHERE `user_id`="'.$_SESSION['user_id'].'" AND `asProfile` = 1';
	$query = $db->prepare($query);
	$query->execute();
	$pic = $query->fetchAll(\PDO::FETCH_ASSOC);
	// check if is set user_o profile profile
	if (isset($pic[0]['imgURL'])) {
		$user_o_pic_profile = $pic[0]['imgURL'];
	} else {
		$user_o_pic_profile = "/assets/img/avatar.png";
	}
    echo "
				<div class='my-3 p-3 bg-white rounded box-shadow'>
					<img class='card-img-top rounded' src='".$url.$user_o_pic_profile."'>
				</div>
";
?>
            </div>

		    <!-- About profile -->
            <div class="col-md-8">
				<div class="my-3 p-3 bg-white rounded box-shadow">
					<form method="POST" action="profile_pic.php" enctype="multipart/form-data">
						<input type="hidden"    name="token"        value="<?php echo $_SESSION['token']; ?>">
				        <h6 class="border-bottom border-gray pb-2 mb-0">Profile</h6>
				        <!-- Pictures -->
				        <div class="media text-muted pt-3">
					        <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
					            <strong class="d-block text-gray-dark">Pictures</strong>
					        </p>
				        </div>
				        <div class="media text-muted pt-3">
					        <div class="row">
<!-- php show pictures -->
<?php
	$query = 'SELECT * FROM `picture` WHERE `user_id`="'.$_SESSION['user_id'].'"';
	$query = $db->prepare($query);
	$query->execute();
	$count = $query->rowCount();
    $la_case = $query->fetchAll(\PDO::FETCH_ASSOC);
    $i=0;
    $result="";
    while ($count > $i) {
    	$result = $result."

<div class='col-md-2'>
    <div class='card mb-2'>
        <a href='profile_pic_detail.php?img_id=".$la_case[$i]['img_id']."'>
        <img class='card-img-top rounded' src='".$url.$la_case[$i]['imgURL']."'>
        </a>
    </div>
	<div class='form-check'>
		<input class='form-check-input' type='radio' name='asProfile' value='".$la_case[$i]['img_id']."'>
		<label class='form-check-label'>As Profile</label>
	</div>
</div>
					            ";
    	$i++;
    }
    if($count > 0) {echo $result;}
?>
					        </div>
					    </div>
					    <!-- Add picture -->
						</br>
			        	<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">Upload</span>
							</div>
							<div class="custom-file">
								<input type="file" name="imgUpload" accept="image/png, image/jpeg, image/jpg" class="custom-file-input" aria-describedby="inputGroupFileAddon01">
								<label class="custom-file-label" for="inputGroupFile01">Choose New Picture</label>
							</div>
						</div></br>					    					
						<!-- submit -->
				        <button name="profile_pic" type="submit" class="btn btn-primary">Submit</button>
			        </form>
			    </div>
            </div><!-- End About profile -->
        </div>
    </div>
</main>

<!-- footer -->
<?php include("../include/footer.php"); ?>