<?php
//Callback for the shortcode, prints the api script of recaptcha on detection
//meaning the two needed keys are set up on the settings page.
function rm_shortcode($atts)
{
  $atts = shortcode_atts(array(
    'title' => 'Rick & Morty Search API',
  ), $atts);

  $output = '';
  $data_recap = 'false';
  
  if(rm_detect_recaptcha()){
    $data_recap = 'true';
    $output .= rm_detect_recaptcha();
  }

  // Form output
  $output .= file_get_contents(RM_DIR . 'templates/search.html');
  $output = str_replace('{{title}}', $atts['title'], $output);
  $output = str_replace('{{dataRecap}}', $data_recap, $output);
  return $output;
}
add_shortcode('rm_shortcode', 'rm_shortcode'); //register the shortcode

//Callback for the ajax call which gets data from the form
function rm_submit_search(){
  // Check if form is submitted correctly
  if ( ! isset($_POST['rm_name'] ) ) {
    wp_send_json_error(["message" => 'At least a name Morty! Cmon *burp*']);
    wp_die();
  }
  
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

  $output = '';

  foreach ($rm_data['results'] as $result){
    $output .= '
    <div class="rm_card">
        <div class="rm_char_img">
            <img src="'.$result['image'].'" alt="'.$result['name'].'">
        </div>
        <span>Name: '.$result['name'].'</span>
        <span>Status: '.$result['status'].'</span>
        <span>Species: '.$result['species'].'</span>';

    if($result['type']){ 
      $output .= '<span>Type: '.$result['type'].'</span>';
    } 
    
    $output .= '<span>Gender: '.$result['gender'].'</span>
      </div>';
  }
  $response['html'] = $output;

  wp_send_json( $response, '200');
}
