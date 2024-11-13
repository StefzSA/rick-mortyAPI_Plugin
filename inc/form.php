<?php
//Callback for the shortcode, prints the api script of recaptcha on detection
//meaning the two needed keys are set up on the settings page.
function rm_shortcode($atts){
  $atts = shortcode_atts(array(
    'title' => 'Rick & Morty Search API',
  ), $atts);

  $output = '';
  $data_recap = 'false';
  
  if(rm_detect_recaptcha()){
    $data_recap = 'true';
    $output .= rm_detect_recaptcha();
  }
  
  $chars = rm_build_results( rm_get_all_characters() );
  // Form output
  $output .= file_get_contents(RM_DIR . 'templates/search_bar.html');
  $output = str_replace('{{title}}', $atts['title'], $output);
  $output = str_replace('{{dataRecap}}', $data_recap, $output);
  $output = str_replace('{{chars}}', $chars, $output);
  return $output;
}
add_shortcode('rm_shortcode', 'rm_shortcode'); //register the shortcode

//Callback for the ajax call which gets data from the form
function rm_submit_search(){
  // Nonce verification, nonce define at rm_api.php
  if ( ! wp_verify_nonce( $_POST['nonce'], 'rm_search_nonce' ) ) {
    wp_send_json_error(["message" => 'Something wrong happened, Morty! *burp*']);
    wp_die();
  }

  //Checks if recaptcha is enabled, meaning it has the two keys set up
  //then validates the recaptcha by checking the response json from the cURL on recaptcha_validation()
  if( rm_detect_recaptcha() ){
    $recaptcha = recaptcha_validation();
    if ( ! $recaptcha['success'] ){
      wp_send_json_error(["message" => 'Something wrong happened, Morty! *burp*']);
      wp_die();
    }
  }

  //Use custom function to sanitize and return sanitized data.
  //Then check if the custom function for inserting the data returned false.
  $rm_data  = rm_sanitize();
  $query    = rm_build_query($rm_data['name'], $_POST['rm_status'], $rm_data['species'], $rm_data['type'], $_POST['rm_gender']);
  $rm_data  = rm_filter_characters($query);
  $response['message'] = 'Ooh Wee';
  $response['success'] = true;

  $response['html'] = rm_build_results($rm_data);
  wp_send_json( $response, '200');
}

function rm_submit_search_page(){
  // Nonce verification, nonce define at rm_api.php
  if ( ! wp_verify_nonce( $_POST['nonce'], 'rm_search_page_nonce' ) ) {
    wp_send_json_error(["message" => 'No funny business, Morty! *burp*']);
    wp_die();
  }

  //Checks if recaptcha is enabled, meaning it has the two keys set up
  //then validates the recaptcha by checking the response json from the cURL on recaptcha_validation()
  if( rm_detect_recaptcha() ){
    $recaptcha = recaptcha_validation();
    if ( ! $recaptcha['success'] ){
      wp_send_json_error(["message" => 'Something wrong happened, Morty! *burp*']);
      wp_die();
    }
  }
  $query = $_POST['pageUrl'];
  $rm_data  = rm_filter_characters_page($query);
  $response['message'] = 'Ooh Wee';
  $response['success'] = true;
  $response['html'] = rm_build_results($rm_data);
  wp_send_json( $response, '200');
}
