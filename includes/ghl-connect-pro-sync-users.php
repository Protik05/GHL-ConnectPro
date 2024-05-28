<?php
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['contact_register_btn'])) {
        // Check if the checkbox is checked
        update_option('ghlconnectpro_contact_register_choice', "yes");
        $users = get_users();

        // Loop through each user
        foreach ($users as $user) {
            // Get user details
            $user_info = get_userdata($user->ID);

            //send to ghl contact
            $locationId = get_option( 'ghlconnectpro_locationId' );

            $contact_data = array(
                "locationId"    => $locationId,
                "firstName"     => $user_info->first_name,
                "lastName"      => $user_info->last_name,
                "email"         => $user_info->user_email,
                "phone"         =>  $user_info->billing_phone,
                "tags"          => "WP-User"  //add tags for sync user.
            );

            // Get Contact Data
            // It will Upsert contact to GHL
            $contact = ghlconnectpro_get_location_contact_data($contact_data);
        }
    

    }
}
?>
<form method="post" class="form-table">
		<?php $register_data=get_option('ghlconnectpro_contact_register_choice');
        $ghlconnectpro_location_connected	= get_option( 'ghlconnectpro_location_connected', GHLCONNECTPRO_LOCATION_CONNECTED );
		?>
		<table>
			<tbody>
				<tr>
					<th scope="row">
						<label >Add All Users to GHL?</label>
					</th>
					<td>
                    <?php if ($register_data==="yes") { ?>
                        <button class="ghl_connectpro_sync button" type="submit" name="contact_register_btn">Sync Again</button>
                        <p class="description"> ALL users are sync in GHL</p>
                      
                        <?php } else { ?>
                            <?php if($ghlconnectpro_location_connected) { ?>

                        	<button class="ghl_connectpro_sync button" type="submit" name="contact_register_btn">Sync Users</button>
                            <?php }else { ?>
                                <button class="ghl_connectpro_sync button" type="submit" name="contact_register_btn" disabled>Sync Users</button> 
                                <p class="syncp">First Connect Your GHL Subaccount.</p>
                                <?php } ?>     
                            
                            <?php } ?>
					</td>
				</tr>
                </tbody>	
		</table>
</form>
            