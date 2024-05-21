<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['course_register_btn'])) {
            // Check if the checkbox is checked
            $choice = isset($_POST["choice"]) && $_POST["choice"] === "yes" ? "yes" : "no";
            update_option('ghlconnectpro_course_choice', $choice);
        }
    }
?>

<form method="post" class="form-table">
		<?php $register_course=get_option('ghlconnectpro_course_choice');
		?>
		<table>
			<tbody>
				<tr>
					<th scope="row">
						<label >Import Course to GHL?</label>
					</th>
					<td>
						<input type="checkbox" name="choice" <?php if ($register_course==='yes') echo "checked";?> value="yes">
						
			
						<?php if ($register_course==='yes') { ?>
						<p class="description"> Course are sync in GHL</p>
						<?php }?>				
					</td>
				</tr>							
			</tbody>	
		</table>
        <button class="ghl_connect button" type="submit" name="course_register_btn">Sync Course</button>
</form>