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
      nonce: ajaxSearch.nonce,
      action: ajaxSearch.action,
    };

    if ($(this).data("recap") == true) {
      grecaptcha.ready(function () {
        grecaptcha
          .execute(siteKey, { action: "rm_search" })
          .then(function (token) {
            $("#rm_search").prepend('<input type="hidden" id="recaptcha_token" name="token" value="' +token +'">');
            data.token = $("#recaptcha_token").val();
            rm_clearResponse();
            validateName();
            apiFetch(data, ajaxSearch.url);
          });
      });
    } else {
      rm_clearResponse();
      validateName();
      apiFetch(data, ajaxSearch.url);
    }
  });

  function rm_clearResponse() {
    $(".rm-error-message").remove();
    $("#rm_query_response").empty();
    $("#rm_query_response").removeClass("r-success").removeClass("r-error");
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
    $.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#rm_results .rm_card").fadeOut(function(){
            $("#rm_results").html(response.html);
            $("#rm_results .rm_card").fadeIn();
          });
        }
      },
      error: function () {
        $("#rm_query_response")
          .addClass("r-error")
          .text("An error has occurred!");
      },
    });
  }

  function apiFetchPage(data, url) {
    $.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#rm_results .rm_card").fadeOut(function(){
            $("#rm_results").html(response.html);
            $("#rm_results .rm_card").fadeIn();
          });
        }
      },
      error: function () {
        $("#rm_query_response")
          .addClass("r-error")
          .text("An error has occurred!");
      },
    });
  }

  $(document).on("click", ".rm_ctrl", function (e) {
    const ctrlPage = $(this);
    const data = {
      pageUrl: ctrlPage.attr("data-rm-url"),
      nonce: ajaxPage.nonce,
      action: ajaxPage.action,
    };

    e.preventDefault();
    if ($('#rm_search').data("recap") == true) {
      grecaptcha.ready(function () {
        grecaptcha
          .execute(siteKey, { action: "rm_search" })
          .then(function (token) {
            if ( ctrlPage.attr("data-rm-url") != "" ) {
              data.token = token;
              rm_clearResponse();
              apiFetchPage(data, ajaxPage.url);
            }
          });
      });
    } else {
      if (ctrlPage.attr("data-rm-url") != "") {
        rm_clearResponse();
        apiFetchPage(data, ajaxPage.url);
      }
    }
  });
});
