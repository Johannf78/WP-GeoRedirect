
<?php

// Function to get the user's IP address
function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Function to get geolocation data using ipapi.co without an API key
function get_geolocation_data($ip) {
    // Check if the data is cached
    $cache_key = 'geo_data_' . md5($ip);
    $cached_data = get_transient($cache_key);

    if ($cached_data !== false) {
        return $cached_data;
    }

    // Get the geolocation data from ipapi.co
    $response = wp_remote_get("https://ipapi.co/{$ip}/json/");
    if (is_wp_error($response)) {
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Cache the data for 24 hours
    set_transient($cache_key, $data, 24 * HOUR_IN_SECONDS);

    return $data;
}




// Function to redirect users based on their location
function redirect_based_on_location($debug) {
	 $debug_info='';//Just to define the variable.
	
    // Check if the current user is an administrator
    if (current_user_can('administrator')) {
		if ($debug){
			$debug_info = ' Do not redirect administrators<br>';	
		} else {
			return; // Do not redirect administrators
		}
    }

		
    // Get the user's IP address
    $ip = get_user_ip();

    // Get the geolocation data
    $data = get_geolocation_data($ip);
    if ($data && isset($data['country_code'])) {
        // Get the current request URI
        $request_uri = $_SERVER['REQUEST_URI'];

        // Debug information
        if ($debug){
        	$debug_info .= 'Request URI: ' . $request_uri . '<br>';
        	$debug_info .= 'Country Code: ' . $data['country_code'] . '<br>';
		}
		
        if ($data['country_code'] === 'ZA' || $data['country_code'] === 'NA' || $data['country_code'] === 'ZM') {
        	$redirect_url = 'https://ampx.co.za' . $request_uri;	
        }else{
			$redirect_url = 'https://ampx-shop.de' . $request_uri;
        }

		if ($debug){
           	$debug_info .= 'Redirect URL: ' . $redirect_url . '<br>';
		}else{
        	wp_redirect($redirect_url);
           	exit;
		}
    }
	return  $debug_info;
}


// Closure to pass the variable and call the main function
add_action('template_redirect', function() {
    $debug = false; // Set this to false to enable redirection
    redirect_based_on_location($debug);
});

// Function to retrieve debug information without performing redirection
function get_debug_info() {

	
    $debug_info = '';

    // Get the user's IP address
    $ip = get_user_ip();
    $debug_info .= 'User IP: ' . $ip . '<br>';

    // Get the geolocation data
    $data = get_geolocation_data($ip);
    if ($data) {
        $debug_info .= 'Geolocation Data: <pre>' . print_r($data, true) . '</pre>';
    } else {
        $debug_info .= 'Failed to retrieve geolocation data.<br>';
    }
	$debug_info .= redirect_based_on_location(true);
    return $debug_info;
}

// Shortcode to display debug information
function display_debug_info() {
    // Check if the current user is an administrator
    if (current_user_can('administrator')) {
        return get_debug_info();
    } else {
        return 'You do not have permission to view this information.';
    }
}
add_shortcode('debug_info', 'display_debug_info');
