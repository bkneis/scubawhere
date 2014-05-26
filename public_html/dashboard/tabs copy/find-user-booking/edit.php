<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	include_once($root."/engine/core/db/interface/bookings.php");
	include_once($root."/engine/core/db/interface/users.php");
	include_once($root."/engine/core/db/interface/agencies.php");
	include_once($root."/engine/core/db/interface/trips.php");
	
	$user_id = $_GET['id'];
	
	$user = get_user_by_id($user_id); 
	
	$certs = get_all_CERTIFICATE_CONSTANTS();
	
	$userCert = get_CERTIFICATE_CONSTANT_by_id($user_id);
?>
<?php if($user['isManual']){ ?>
	<form action="" method="post">
	<table>
		<tr>
			<td width="15%">
				<label>First Name</label>
			</td>
			<td width="35%">
				<input type="text" class="form-text-small" name="firstName" value="<?php echo $user['firstName']; ?>">
			</td>
		
			<td width="15%">
				<label>Surname</label>
			</td>
			<td width="35%">
				<input type="text" class="form-text-small" name="lastName" value="<?php echo $user['lastName']; ?>">
			</td>
		</tr>
		<tr>
			<td>
				<label>Email Address</label>
			</td>
			<td>
				<input type="text" class="form-text-small" name="email" value="<?php echo $user['email']; ?>">
			</td>
		
			<td>
				<label>DOB</label>
			</td>
			<td>
				<input type="text" class="form-text-small" name="dob" data-validation="date" data-validation-format="dd-mm-yyyy" value="<?php echo date("DD-MM-YY",strtotime($user['dob'])); ?>">
			</td>
		</tr>
		
		<tr>
			<td>
				<label>Date of Last Dive</label>
			</td>
			<td>
				<input type="text" class="form-text-small" name="lastDive" data-validation="date" data-validation-format="dd-mm-yyyy" value="<?php echo date("DD-MM-YY",strtotime($user['lastDive'])); ?>">
			</td>
		
			<td>
				<label>Dive Certificate</label>
			</td>
			<td>
				<select class="form-select-small" name="certificate">
					<option value="">Please select..</option>
					<?php foreach($certs as $cert){ ?>
						<option value="<?php echo $cert['id'] ?>" <?php if($userCert['id'] == $cert['id']){ ?>selected<?php } ?>><?php echo $cert['name']; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
	</table>
		
		
		<input type="submit" class="form-button-small">
	</form>
<?php }else{ ?>
	
	Sorry, you can't edit a user booking unless you manually entered it.
	
<?php } ?>