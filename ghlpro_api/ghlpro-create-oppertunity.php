<?php

if ( ! function_exists( 'ghlconnectpro_get_create_oppertunity' ) ) {
    
    function ghlconnectpro_get_create_oppertunity($data) {
		$ghlconnectpro_access_token = get_option('ghlconnectpro_access_token');
		$endpoint = "https://services.leadconnectorhq.com/opportunities/";
		$ghl_version = '2021-07-28';

        $request_args = array(
            'body' 		=> $data,
			'headers' => array(  
				'Authorization' => "Bearer {$ghlconnectpro_access_token}",
				'Version' => $ghl_version
			),
		);

		$response = wp_remote_post( $endpoint, $request_args );
		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $http_code || 201 === $http_code ) {
            $body = json_decode( wp_remote_retrieve_body( $response ) );
			$create_oppertunity = $body->opportunity;
			return $create_oppertunity;
		}
    	
		return "";
		
    }
}