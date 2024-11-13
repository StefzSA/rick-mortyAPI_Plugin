jQuery(document).ready(function ($) {
  $("#rm_search").submit(function (e) {
    e.preventDefault();
    //sets the data for ajax call
    const data = {
      rm_name: $("#rm_name").val(),
      rm_status: $("#rm_status").val(),
      rm_species: $("#rm_species").val(),
      rm_type: $("#rm_type").val(),
      rm_gender: $("#rm_gender").val(),
      nonce: ajax.nonce,
      action: ajax.action,
    };

    if ($(this).data("recap") == true) {
      grecaptcha.ready(function () {
        grecaptcha.execute(siteKey, { action: "rm_search" }).then(function (token) {
          $("#rm_search").prepend('<input type="hidden" id="recaptcha_token" name="token" value="' +token +'">');
          // Clear any previous error messages or response
          $(".rm-error-message").remove();
          $("#rm_response").empty();
          $("#rm_results").empty();
          $("#rm_response").removeClass("r-success").removeClass("r-error");
          data += {token: $("#recaptcha_token").val()};

          validateName();
          apiFetch(data, ajax.url);
        });
      });
    } else {
      // Clear any previous error messages or response
      $(".rm-error-message").remove();
      $("#query_response").empty();
      $("#rm_results").empty();
      $("#query_response").removeClass("r-success").removeClass("r-error");

      validateName();
      apiFetch(data, ajax.url);
    }
  });

  function validateName() {
    if ($.trim($("#rm_name").val()) === "") {
      $("#query_response").after('<span class="rm-error-message">Please enter a name.</span>');
      return false;
    }
  }

  function apiFetch(data, url){
    $.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        $(".rm-error-message").remove();
        if (response.success) {
          $("#rm_results").data("pageno", '1').hide().html(response.html).fadeIn();
        }
      },
      error: function () {
        $("#rm_query_response").addClass("r-error").text("An error has occurred!");
      },
    });
  }


});
