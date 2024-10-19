# WordPress Geolocation and Redirection Code Snippet
This code snippet is designed to retrieve and display geolocation information based on the user's IP address. It also includes functionality to conditionally redirect users based on their location. Additionally, it provides a shortcode to display debug information for administrators.

## Functions
### get_user_ip()
This function retrieves the user's IP address from the server variables.

### get_geolocation_data($ip)
This function retrieves geolocation data using the ipapi.co service without an API key. It also caches the data for 24 hours to improve performance.

### redirect_based_on_location($debug)
This function redirects users based on their location if the $debug parameter is false. It does not redirect administrators and provides debug information if $debug is true.

### get_debug_info()
This function retrieves the user's IP address and geolocation data, and returns it as a formatted string. It also calls the redirect_based_on_location function with true to include redirection information in the debug output.

### display_debug_info()
This function checks if the current user is an administrator. If true, it calls the get_debug_info function to retrieve and display the debug information. If the user is not an administrator, it returns a permission error message.

## Usage
Add the **[debug_info]** shortcode to any page or post where you want to display the debug information.
Visit the page while logged in as an administrator to see the debug information.

In the following code chagne the debug variable to true to view debug info, and turn it to false to do the actual redirection of all visitors.
// Closure to pass the variable and call the main function
add_action('template_redirect', function() {
    $debug = false; // Set this to false to enable redirection
    redirect_based_on_location($debug);
});

