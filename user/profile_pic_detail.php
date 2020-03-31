<!-- connection -->
<?php require_once("../config/connection.php"); ?>
<!-- session -->
<?php require_once("../include/session.php"); ?>
<!-- php delete picture -->
<?php
	if(isset($_POST["pic_delete"]) && isset($_POST["img_id"]) && ($_SESSION["token"] === $_POST["token"])) {
	    $query = 'DELETE FROM `picture` WHERE `img_id` = :img_id';
        $query = $db->prepare($query);
        $query->bindParam(':img_id', $_POST['img_id'], PDO::PARAM_INT); 
        $query->execute();
        $msg = 'The picture '.$_POST['img_id'].' is deleted with succeed.';
        header("location:profile_pic.php?msg=".$msg."");
	}
?>
<!-- header -->
<?php include("../include/header.php"); ?>   
<!-- nav -->
<?php include("../include/navbar_user.php"); ?> 

<main role="main" class="container">
    <div class="d-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded box-shadow">
        <img class="mr-3" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-outline.svg" alt="" width="48" height="48">
        <div class="lh-100">
        <h6 class="mb-0 text-white lh-100">Matcha</h6>
        <small>Since 2020</small>
        </div>
    </div>
    
    <!-- Main -->
    <form method="POST" action="profile_pic_detail.php">
	    <div class="my-3 p-3 bg-white rounded box-shadow">
	        <div class="row">
		    	<div class='col-md-4'></div>
			    <!-- Photo profile -->
			   	<div class='col-md-4'>
		            <div class='card mb-2'>
<?php
	$query = 'SELECT * FROM `picture` WHERE `img_id`="'.$_GET['img_id'].'"';
	$query = $db->prepare($query);
	$query->execute();
    $pic = $query->fetchAll(\PDO::FETCH_ASSOC);
    echo "
		                <img class='card-img-top rounded' src='".$url.$pic[0]['imgURL']."'> "; ?>
		                <input type="hidden" name="img_id" value="<?php echo $pic[0]['img_id']; ?>">
		                <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
			            <button name="pic_delete" type="submit" class="btn btn-primary">Delete</button>
			        </div>

		        </div>
		    	<div class='col-md-4'></div>
	        </div>
	    </div>
	</form>
</main>

<!-- footer -->
<?php include("../include/footer.php"); ?>