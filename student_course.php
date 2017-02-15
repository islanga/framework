<table border="1" width="100%">
	<?php 
	foreach ($course_lists as $cr): ?>
	<tr>
		<td>
			<?php echo "<a href=http://localhost/registration/student_man/index/edit&course_id=" . $cr['cid']; ?>><?php echo $cr["cname"]; ?></a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
		