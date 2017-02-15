<form method="post" action="<?php echo $this->baseUrl() ?>index/index/save" id="spa_form">
	<table border="1" width="100%">
		<?php
			if (count($errorMessage)) {?>
			<tr>
				<td colspan="2"><div>
				<font color="#FF0000">There are errors in this page</font>
			<?php
			  print "<ul><li>";
			  print implode("</li color=\"#FF0000\"><li>", $errorMessage);
			  print "</li></ul>";
			}
			?></div></td>
		</tr>
		<tr>
			<td>Course Name</td>
			<td>
			<select name="cid[]" class="" multiple="multiple">
				<?php foreach ($course_results as $cr): ?>
					<option value="<?php echo $cr["cid"]; ?>" <?php echo (in_array($cr["cid"], $c_id) || in_array($cr["cid"], $cid)) ? "selected" : "";?>><?php echo $cr['cname']; ?></option>
				<?php endforeach; ?>
			</select>
			</td>
		</tr>
		<input type="hidden" name="sno" value="<?php echo $students->sno; ?>" />
		<tr>
			<td>Surname</td>
			<td><input type="text" name="sname" value="<?php echo $students->sname; ?>" /></td>
		</tr>
		<tr>
			<td>Initials</td>
			<td><input type="text" name="init" value="<?php echo $students->init; ?>" /></td>
		</tr>
		<tr>
			<td>Full First Name</td>
			<td><input type="text" name="fname" value="<?php echo $students->fname; ?>" /></td>
		</tr>
		<tr>
			<td>Title</td>
			<td><input type="radio" name="title" value="Ms" <?php echo ($students->title == "Ms") ? ' checked':''; ?> />Ms
			<input type="radio" name="title" value="Mrs" <?php echo ($students->title == "Mrs") ? ' checked':''; ?> />Mrs
			<input type="radio" name="title" value="Mr" <?php echo ($students->title == "Mr") ? ' checked':''; ?> />Mr
			</td>
		</tr>
		<tr>
			<td>Maiden or Previous Surname</td>
			<td><input type="text" name="msname" value="<?php echo $students->msname; ?>" /></td>
		</tr>
		<tr>
			<td>Date of Birth</td>
			<td><?php $dob = explode("/", $students->dob); ?>
				<select name="day">
					<option value="0" <?php echo ($dob[0] == "0") ? " selected" : ""; ?>>Select Day</option>
					<?php for($x = 1; $x <= 31; $x++): ?>
					<option <?php echo ($dob[0] == $x) ? " selected" : ""; ?>><?php echo ($x < 10) ? "0".$x : $x; ?></option>
					<?php endfor; ?>
				</select>
				<select name="month">
					<option value="0" selected="selected">Select Month</option>
					<?php 
					$months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
					for($x = 1; $x <= 12; $x++): ?>
					<option <?php echo ($dob[1] == $x) ? " selected" : ""; ?> value="<?php echo ($x < 10) ? "0".$x : $x; ?>"><?php echo $months[$x]; ?>
					<?php endfor; ?>
				</select>
				<select name="year">
					<option value="0" selected="selected">Select Year</option>
					<?php for($x = 1900; $x <= date("Y"); $x++): ?>
					<option <?php echo ($dob[2] == $x) ? " selected" : ""; ?>><?php echo $x; ?></option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Gender</td>
			<td><input type="text" name="sex" value="<?php echo $students->sex; ?>"/></td>
		</tr>
		<tr>
			<td>Language for correspondence</td>
			<td><input type="text" name="lang" value="<?php echo $students->lang; ?>" /></td>
		</tr>
		<tr>
			<td>Identity Number</td>
			<td><input type="text" name="idno" value="<?php echo $students->idno; ?>" /></td>
		</tr>
		<tr>
			<td>Home Telephone Code + Number</td>
			<td><input type="text" name="telh" value="<?php echo $students->telh; ?>" /></td>
		</tr>
		<tr>
			<td>Work Telephone Code + Number</td>
			<td><input type="text" name="telw" value="<?php echo $students->telw; ?>" /></td>
		</tr>
		<tr>
			<td>Cell Phone Number</td>
			<td><input type="text" name="cel" value="<?php echo $students->cel; ?>" /></td>
		</tr>
		<tr>
			<td>Fax Code + Number</td>
			<td><input type="text" name="fax" value="<?php echo $students->fax; ?>" /></td>
		</tr>
		<tr>
			<td>E-Mail Address</td>
			<td><input type="text" name="email" value="<?php echo $students->email; ?>" /></td>
		</tr>
		<tr>
			<td>Postal Address of Student</td>
			<td><input type="text" name="address" value="<?php echo $students->address; ?>" /></td>
		</tr>
		<tr>
			<td>Share Contact Details?</td>
			<td><input type="checkbox" name="contact_flag" value="1" <?php echo ((int)$students->contact_flag) ? ' checked' : ''; ?>/></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="action" value="Register Student" />
			</td>
		</tr>
	</table>
</form>