<?php
$array = array('add', 'edit');
$arr = array('index', 'save', 'delete');
if (in_array($action, $arr)):
?>
	<table border="1" width="100%">
		<?php foreach ($course_list as $cr): ?>
		<tr>
			<td>
				<?php echo $cr['cname']; ?>
			</td>
			<td valign="top">
				<?php echo "<a href=index/edit&id=" . $cr['cid']; ?>>Edit</a>
			</td>
			<td valign="top">
				<?php echo "<a href=index/delete&id=" . $cr['cid']; ?>>Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="3" align="center">
				<a href="index/add">Add a New Course</a>	
			</td>
		</tr>
	</table>
	<?php endif; ?>
	<?php if (in_array($action, $array)): ?>
	<form method="post" action="save" id="spa_form">
	<table>
		<tr>
			<td>
				Course Name
			</td>
			<td>
				<input type="hidden" name="cid" value="<?php echo !empty($course_edit->cid) ? $course_edit->cid : ''; ?>">
				<input type="text" name="cname" value="<?php echo !empty($course_edit->cname) ? $course_edit->cname :''; ?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="course" value="Add Course">
			</td>
		</tr>
	</table>
	<?php endif; ?>
	</form>