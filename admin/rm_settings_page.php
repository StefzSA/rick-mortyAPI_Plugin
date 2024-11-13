<div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1> 
  <form method="post" action="options.php">
    <?php settings_fields( 'rm_settings' ); ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="rm_recaptcha_site_key"><?php _e( 'reCAPTCHA V3 Site Key', 'rm_search' ); ?></label></th>
          <td>
            <input type="text" name="rm_recaptcha_site_key" id="rm_recaptcha_site_key" value="<?php echo esc_attr( get_option( 'rm_recaptcha_site_key' ) ); ?>" />
            <p class="description"><?php _e( 'Enter your reCAPTCHA Site Key for spam protection.', 'rm_search' ); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="rm_recaptcha_secret_key"><?php _e( 'reCAPTCHA V3 Secret Key', 'rm_search' ); ?></label></th>
          <td>
            <input type="text" name="rm_recaptcha_secret_key" id="rm_recaptcha_secret_key" value="<?php echo esc_attr( get_option( 'rm_recaptcha_secret_key' ) ); ?>" />
            <p class="description"><?php _e( 'Enter your reCAPTCHA Secret Key for verification.', 'rm_search' ); ?></p>
          </td>
        </tr>
      </tbody>
    </table>
    <?php submit_button( 'Save Changes' ); ?>
  </form>
</div>
