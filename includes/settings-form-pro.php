<?php
	 if ( ! defined( 'ABSPATH' ) ) exit;
	if ( isset( $_GET['connection_status'] ) && sanitize_text_field($_GET['connection_status']) === 'success' ) {
		$ghlconnectpro_access_token 	= sanitize_text_field( $_GET['acctn'] );
		$ghlconnectpro_refresh_token 	= sanitize_text_field( $_GET['reftn'] );
		$ghlconnectpro_locationId 	    = sanitize_text_field( $_GET['locid'] );
		$ghlconnectpro_client_id 		= sanitize_text_field( $_GET['cntid'] );
		$ghlconnectpro_client_secret 	= sanitize_text_field( $_GET['cntst'] );
        
		// Save data
	    update_option( 'ghlconnectpro_access_token', $ghlconnectpro_access_token );
	    update_option( 'ghlconnectpro_refresh_token', $ghlconnectpro_refresh_token );
	    update_option( 'ghlconnectpro_locationId', $ghlconnectpro_locationId );
	    update_option( 'ghlconnectpro_client_id', $ghlconnectpro_client_id );
	    update_option( 'ghlconnectpro_client_secret', $ghlconnectpro_client_secret );
	    update_option( 'ghlconnectpro_location_connected', 1 );
		update_option( 'ghlconnectpro_loc_name', ghlconnectpro_location_name($ghlconnectpro_locationId)->name);
	    //  (delete if any old transient  exists )
	    delete_transient('ghlconnectpro_location_tags');
	    delete_transient('ghlconnectpro_location_wokflow');

	    wp_redirect('admin.php?page=ib-ghlconnectpro');
	}
    
	$ghlconnectpro_location_connected	= get_option( 'ghlconnectpro_location_connected', GHLCONNECTPRO_LOCATION_CONNECTED );
	$ghlconnectpro_client_id 			= get_option( 'ghlconnectpro_client_id' );
	$ghlconnectpro_client_secret 		= get_option( 'ghlconnectpro_client_secret' );
	$ghlconnectpro_locationId 		    = get_option( 'ghlconnectpro_locationId' );
	$redirect_page 				    = get_site_url(null, '/wp-admin/admin.php?page=ib-ghlconnectpro');
	$redirect_uri 				    = get_site_url();
	$client_id_and_secret 		    = '';

	$auth_end_point = GHLCONNECTPRO_AUTH_END_POINT;
	$scopes = "workflows.readonly contacts.readonly contacts.write campaigns.readonly conversations/message.readonly conversations/message.write forms.readonly locations.readonly locations/customValues.readonly locations/customValues.write locations/customFields.readonly locations/customFields.write opportunities.readonly opportunities.write users.readonly links.readonly links.write surveys.readonly users.write locations/tasks.readonly locations/tasks.write locations/tags.readonly locations/tags.write locations/templates.readonly calendars.write calendars/groups.readonly calendars/groups.write forms.write medias.readonly medias.write";

    $connect_url = GHLCONNECTPRO_AUTH_URL . "?get_code=1&redirect_page={$redirect_page}";

	if ( ! empty( $ghlconnectpro_client_id ) && ! str_contains( $ghlconnectpro_client_id, 'lq4sb5tt' ) ) {
		
		$connect_url = $auth_end_point . "?response_type=code&redirect_uri={$redirect_uri}&client_id={$ghlconnectpro_client_id}&scope={$scopes}";
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['global_tags'])) {
			// Capture the raw input
			$raw_tags = $_POST['global_tags'];

			// Split the input string into an array, trimming whitespace and removing empty values
			$ghlconnectpro_globTags = array_filter(array_map('trim', explode(',', $raw_tags)));
			// Update the option with the array of tags
			update_option('ghlconnectpro_globTags', $ghlconnectpro_globTags);
			update_option('ghlconnectpro_global', $raw_tags);
		}

		if (isset($_POST['contact_register_btn'])) {
			// Check if the checkbox is checked
			$choice = isset($_POST["choice"]) && $_POST["choice"] === "yes" ? "yes" : "no";
			update_option('ghlconnectpro_contact_register_choice', $choice);

			
			
			if($choice==='yes'){
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
						"tags"          => "contact Sync"  //add tags for sync user.
					);

					// Get Contact Data
					// It will Upsert contact to GHL
					$contact = ghlconnectpro_get_location_contact_data($contact_data);
				}
			}

		}
		if (isset($_POST['invoiceCreate'])){
			//check for invoice
			$Invoicechoice = isset($_POST["choiceInvoice"]) && $_POST["choiceInvoice"] === "yes" ? "yes" : "no";
			update_option('ghlconnectpro_invoice_check', $Invoicechoice);
			
		}


	}
?>

<div id="ib-ghlconnectpro">
    <h1> <?php esc_html_e('Connect With Your GHL Subaccount', 'ghl-connect-pro'); ?> </h1>
    <hr />
    <table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label> <?php esc_html_e('Connect GHL Subaccount Location', 'ghl-connect-pro'); ?> </label>
				</th>
				<td>
					<?php if ($ghlconnectpro_location_connected) { ?>
						<div class="connected-location">
							<button class="button button-connected" disabled>Connected</button>
							<!-- Show success message after connection -->
							<?php if (isset($_GET['connected']) && sanitize_text_field($_GET['connected']) === 'true') { ?>
							<p class="success-message">You have successfully connected to Subaccount Location ID: <?php echo esc_html($ghlconnectpro_locationId); ?></p>
						<?php } ?>
							<p class="description">To connect another subaccount location, click below:</p>
							<a class="ghl_connect button" href="<?php echo esc_url($connect_url); ?>">Connect Another Subaccount</a>	
						</div>
					<?php } else { ?>
						<div class="not-connected-location">
							<p class="description">You're not connected to any subaccount location yet.</p>
							<a class="ghl_connect button" href="<?php echo esc_url($connect_url); ?>">Connect GHL Subaccount</a>
						</div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Your Connected GHL Subaccount LocationID', 'ghl-connect-pro'); ?></label>
				</th>
				<td>
					<?php if ($ghlconnectpro_location_connected) { ?>
						<p class="description">Location ID: <?php echo esc_html($ghlconnectpro_locationId); ?></p>
					<?php } else { ?>
						<p class="description">You are not connected yet. Please connect by clicking the above button</p>
					<?php } ?>
				</td>
			</tr>
		</tbody>
    </table>
	<form method="post" class="form-table">
		<?php $register_data=get_option('ghlconnectpro_contact_register_choice');
		$invoice_data=get_option('ghlconnectpro_invoice_check');
		$globTags = get_option('ghlconnectpro_global');
		?>
		<table>
			<tbody>
				<tr>
					<th scope="row">
						<label >Add All Users to GHL?</label>
					</th>
					<td>
						<input type="checkbox" name="choice" <?php if ($register_data==='yes') echo "checked";?> value="yes">
						
			
						<?php if ($register_data==='yes') { ?>
						<p class="description"> ALL users are sync in GHL</p>
						<?php }?>
						<br>
						<button class="ghl_connect_sync button" type="submit" name="contact_register_btn">Sync Users</button>
					
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Do you want Create Invoice in GHL CRM?</label>
					</th>
					<td>
						<input type="checkbox" name="choiceInvoice" <?php if ($invoice_data==='yes') echo "checked";?> value="yes">
					</td>
				</tr>			
				<tr>
					<th scope="row">
						<label>Add Global Tags?</label>
					</th>
					<td>
						
						<input type="text" name="global_tags" value="<?php echo esc_attr($globTags); ?>">
						<p class="glob-desc">This tags get fired when there is no products specific tags.</p>
					</td>
				</tr>	
			</tbody>	
		</table>
		<button class="ghl_connect button" type="submit" name="invoiceCreate">Update Settings</button>
	</form>

</div>