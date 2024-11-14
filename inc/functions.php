<?
//sanitizes post data and returns sanitized in an array
function rm_sanitize(){
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

//this function builds the query for the filter rm_filter_characters function.
function rm_build_query($name, $status, $specie, $type, $gender){
    $qry = '?';
    if($name) $qry .= 'name='.urlencode($name).'&';
    if($status) $qry .= 'status='.$status.'&';
    if($specie) $qry .= 'species='.urlencode($specie).'&';
    if($type) $qry .= 'type='.urlencode($type).'&';
    if($gender) $qry .= 'gender='.$gender.'&';

    return $qry;
}

//This one is for usage along with rn_build_query. Simply pass the query with vars
function rm_filter_characters($qry_str){
    $url = 'https://rickandmortyapi.com/api/character/' . $qry_str;
    $response = wp_remote_get( $url, ["headers" => ['Content-Type' => 'application/json', 'Accept' => 'application/json']] );
    if (is_wp_error($response)) return false; // Return false on error
    return json_decode( wp_remote_retrieve_body($response) , true); // Return decoded body
}

//This one is for pagination, doesn't require anything more than the url
function rm_filter_characters_page($url){
    $response = wp_remote_get( $url, ["headers" => ['Content-Type' => 'application/json', 'Accept' => 'application/json']] );
    if (is_wp_error($response)) return false; // Return false on error
    return json_decode( wp_remote_retrieve_body($response) , true); // Return decoded body
}

//this gets all characters it's only used for first render.
function rm_get_all_characters(){
    $response = wp_remote_get( 'https://rickandmortyapi.com/api/character', ["headers" => ['Content-Type' => 'application/json', 'Accept' => 'application/json']] );
    if (is_wp_error($response)) return false; // Return false on error
    return json_decode( wp_remote_retrieve_body($response) , true); // Return decoded body
}
//this builds all the html that goes inside #rm_results which is the wrapper element.
function rm_build_results($rm_data){
    $output = '';
    if($rm_data['results']){
    foreach ($rm_data['results'] as $result){
        $output .= '<div class="rm_card">';
        $output .= '<div class="rm_char_img">';
        $output .= '<img src="'.$result['image'].'" alt="'.$result['name'].'">';
        $output .= '</div>';
        $output .= '<div id="rm_cardinfo">';
        $output .= '<p><span>Name: </span>'.$result['name'].'</p>';
        $output .= '<p><span>Status: </span>'.$result['status'].'</p>';
        $output .= '<p><span>Species: </span>'.$result['species'].'</p>';
    
        if($result['type']){ 
          $output .= '<p><span>Type: </span>'.$result['type'].'</p>';
        } 
    
        $output .= '<p><span>Gender: </span>'.$result['gender'].'</p>';
        $output .= '</div></div>';
      } 
    }else{
        $output .= '<h2><span>Ooh wee, nothing to see here!</span></h2>';
    }
      
      $prev_dis = ( !($rm_data['info']['prev']) )? 'disabled' : '';
      $next_dis = ( !($rm_data['info']['next']) )? 'disabled' : '';

      $output .= '<div class="rm_page-controls">';
      $output .= '<a class="rm_ctrl '. $prev_dis .'" data-rm-url="'.$rm_data['info']['prev'].'"'. $prev_dis .'>Previous</a>';
      $output .= '<a class="rm_ctrl '. $next_dis .'" data-rm-url="'.$rm_data['info']['next'].'"'. $next_dis .'>Next</a>';
      $output .= '</div>';
      return $output;
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
function recaptcha_validation($token){

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
