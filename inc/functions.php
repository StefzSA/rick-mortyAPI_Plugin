<?
//sanitizes post data and returns sanitized in an array
function rm_sanitize()
{
    // Sanitize data
    $name   = sanitize_text_field($_POST['rm_name']);
    $species = sanitize_text_field($_POST['rm_species']);
    $type   = sanitize_text_field($_POST['rm_type']);

    return array(
        'name'      => $name,
        'species'   => $species,
        'type'      => $type
    );
}

//Callback for the settings page
function rm_settings_page(){
    require(RM_DIR . 'admin/rm_settings_page.php');
}

function rm_build_query($name, $status, $specie, $type, $gender){
    $qry = '?';
    if($name) $qry .= 'name='.urlencode($name).'&';
    if($status) $qry .= 'status='.$status.'&';
    if($specie) $qry .= 'species='.urlencode($specie).'&';
    if($type) $qry .= 'type='.urlencode($type).'&';
    if($gender) $qry .= 'gender='.$gender.'&';

    return $qry;
}

function rm_filter_characters($qry_str){
    $url = 'https://rickandmortyapi.com/api/character/' . $qry_str;
    $response = wp_remote_get( $url, ["headers" => ['Content-Type' => 'application/json', 'Accept' => 'application/json']] );
    
    if (is_wp_error($response)) return false; // Return false on error
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true); // Return decoded body
}

//Detects if recaptcha settings on the page have anything.
//returns a script to add the recaptcha api with the site key to the head
//used at the form shortcode function rm_shortcode()
function rm_detect_recaptcha(){
    if ( !empty(get_option('rm_recaptcha_site_key')) and !empty(get_option('rm_recaptcha_secret_key'))) {
        $output = '<script>';
        $output .= "const siteKey = '" . get_option('rm_recaptcha_site_key') . "';\n";
        $output .= 'if (siteKey) { const script = document.createElement("script");script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`; document.head.appendChild(script); }';
        $output .= '</script>';
        return $output;
    }
    return;
}

//Uses cURL to validate the token from the form with the secret key from the settings page.
function recaptcha_validation(){
    $token = $_POST['token'];
    $response = wp_remote_post( "https://www.google.com/recaptcha/api/siteverify", array('secret' =>  get_option('rm_recaptcha_secret_key'), 'response' => $token));
    
    if (is_wp_error($response)) return false; // Return false on error
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true); // Return decoded body


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' =>  get_option('rm_recaptcha_secret_key'), 'response' => $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    return $response;
}
