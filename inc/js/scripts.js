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
        grecaptcha
          .execute(siteKey, { action: "rm_search" })
          .then(function (token) {
            $("#rm_search").prepend('<input type="hidden" id="recaptcha_token" name="token" value="' +token +'">');
            data.token = $("#recaptcha_token").val();
            validateName();
            apiFetch(data, ajax.url);
          });
      });
    } else {
      rm_clearResponse();
      validateName();
      apiFetch(data, ajax.url);
    }
  });

  function rm_clearResponse() {
    $(".rm-error-message").remove();
    $("#query_response").empty();
    $("#rm_results").fadeOut();
    $("#query_response").removeClass("r-success").removeClass("r-error");
  }

  function validateName() {
    if ($.trim($("#rm_name").val()) === "") {
      $("#query_response").after(
        '<span class="rm-error-message">Please enter a name.</span>'
      );
      return false;
    }
  }

  function apiFetch(data, url) {
    jQuery.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        jQuery(".rm-error-message").remove();
        if (response.success) {
          rm_clearResponse();
          jQuery("div#rm_results").html(response.html);
          jQuery("div#rm_results").fadeIn();
        }
      },
      error: function () {
        jQuery("#rm_query_response")
          .addClass("r-error")
          .text("An error has occurred!");
      },
    });
  }

  function apiFetchPage(data, url) {
    jQuery.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          jQuery("div#rm_results").html(response.html);
          jQuery("div#rm_results").fadeIn();
        }
      },
      error: function () {
        jQuery("#rm_query_response")
          .addClass("r-error")
          .text("An error has occurred!");
      },
    });
  }

  $(document).on("click", ".rm_ctrl", function (e) {
    const ctrlPage = $(this);
    e.preventDefault();
    if ($('#rm_search').data("recap") == true) {
      grecaptcha.ready(function () {
        grecaptcha
          .execute(siteKey, { action: "rm_search" })
          .then(function (token) {
            if ( ctrlPage.attr("data-rm-url") != "" ) {
              const data = {
                pageUrl: ctrlPage.attr("data-rm-url"),
                nonce: ajaxPage.nonce,
                action: ajaxPage.action,
                token: token,
              };
              apiFetchPage(data, ajaxPage.url);
            }
          });
      });
    } else {
      if (ctrlPage.attr("data-rm-url") != "") {
        const data = {
          pageUrl: ctrlPage.attr("data-rm-url"),
          nonce: ajaxPage.nonce,
          action: ajaxPage.action,
        };
        apiFetchPage(data, ajaxPage.url);
      }
    }
  });
});
