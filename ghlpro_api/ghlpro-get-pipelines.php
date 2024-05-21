<?php

if ( ! function_exists( 'ghlconnectpro_get_pipelines' ) ) {
    
    function ghlconnectpro_get_pipelines() {

    	$key = 'ghlconnectpro_pipelines';
    	$expiry = 60  * 60 * 24; // 1 day

    	$pipelines = get_transient($key);

    	// if ( !empty( $pipelines ) ) {
    	// 	//delete_transient($key);
    	// 	return $pipelines;
    	// }

		$ghlconnectpro_locationId = get_option('ghlconnectpro_locationId');
		$ghlconnectpro_access_token = get_option('ghlconnectpro_access_token');
		$endpoint = "https://services.leadconnectorhq.com/opportunities/pipelines";
		$ghl_version = '2021-07-28';
        $body = array(
            'locationId' 	=> $ghlconnectpro_locationId
        );
        $request_args = array(
            'body' 		=> $body,
			'headers' => array(  
				'Authorization' => "Bearer {$ghlconnectpro_access_token}",
				'Content-Type' => 'application/json',
				'Version' => $ghl_version,
			),
		);

		$response = wp_remote_get( $endpoint, $request_args );

		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $http_code ) {

			$body = wp_remote_retrieve_body( $response );
			$pipelines = json_decode( $body )->pipelines;
			set_transient( $key, $pipelines, $expiry );
			return $pipelines;

		}elseif( 401 === $http_code ){
			ghlconnectpro_get_new_access_token();
		}
    }
}