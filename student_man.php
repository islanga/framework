<form method="post" action="index/index/save" id="spa_form">
<table border="1">
	<tr>
		<th>
			Courses
		</th>
		<th>
			Year
		</th>
		<th>
			Surname
		</th>
		<th>
			Initials
		</th>
		<th>
			First Name
		</th>
		<th>
			Title
		</th>
		<th>
			Maiden Name
		</th>
		<th>
			Date of Birth
		</th>
		<th>
			Sex
		</th>
		<th>
			Language
		</th>
		<th>
			ID Number
		</th>
		<th>
			Tel Home
		</th>
		<th>
			Tel Work
		</th>
		<th>
			Cell Phone
		</th>
		<th>
			Fax
		</th>
		<th>
			Email
		</th>
		<th>
			Address
		</th>
	</tr>
	<?php 
	foreach ($student_lists as $cr): ?>
	<tr>
		<?php 
			$course_id = $course_students->course_id($cr['sno']); 
			$course_ids = explode("*|*", $course_id[0]['cid']);
			foreach ($course_ids as $key => $value):
				try {
						$courses->load($value);
						$cnames[] = $courses->cname;
					} catch (Exception $ex) {
	
					}
			endforeach;
		?>
		<td>
			<?php echo implode(', ', $cnames);?>
		</td>
		<td>
			<?php echo (int)$course_id[0]['year']; ?>
		</td>
		<td>
			<?php echo $cr['sname']; ?>
		</td>
		<td>
			<?php echo $cr['init']; ?>
		</td>
		<td>
			<?php echo $cr['fname']; ?>
		</td>
		<td>
			<?php echo $cr['title']; ?>
		</td>
		<td>
			<?php echo $cr['msname']; ?>
		</td>
		<td>
			<?php echo $cr['dob']; ?>
		</td>
		<td>
			<?php echo $cr['sex']; ?>
		</td>
		<td>
			<?php echo $cr['lang']; ?>
		</td>
		<td>
			<?php echo $cr['idno']; ?>
		</td>
		<td>
			<?php echo $cr['telh']; ?>
		</td>
		<td>
			<?php echo $cr['telw']; ?>
		</td>
		<td>
			<?php echo $cr['cel']; ?>
		</td>
		<td>
			<?php echo $cr['fax']; ?>
		</td>
		<td>
			<?php echo $cr['email']; ?>
		</td>
		<td>
			<?php echo $cr['address']; ?>
		</td>
		<td>
			<?php echo "<a href=http://localhost/registration/index/index/edit&id=" . $cr['sno']; ?>>Edit</a>
		</td>
	</tr>
	<?php endforeach; ?>
	<tr>
		<td colspan="17" align="center">
			<a href="http://localhost/registration/">Add a New Student</a>
		</td>
	</tr>
</table>
</form>
