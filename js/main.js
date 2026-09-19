(function ($) {
  "use strict";

  // Spinner
  var spinner = function () {
    setTimeout(function () {
      if ($("#spinner").length > 0) {
        $("#spinner").removeClass("show");
      }
    }, 1);
  };
  spinner();

  // Initiate the WOW JS
  new WOW().init();

  // Back to Top Button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      $(".back-to-top").fadeIn("slow");
    } else {
      $(".back-to-top").fadeOut("slow");
    }
  });
  $(".back-to-top").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
    return false;
  });

  // Header carousel
  $(".header-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    loop: true,
    nav: false,
    dots: true,
    items: 1,
  });

  // Our Client
  const clientsSlider = new Swiper(".clients-slider", {
    loop: true,

    slidesPerView: 3,

    spaceBetween: 10,

    centeredSlides: false,

    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },

    speed: 800,

    breakpoints: {
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },

      992: {
        slidesPerView: 4,
        spaceBetween: 30,
      },

      1200: {
        slidesPerView: 6,
        spaceBetween: 40,
      },
    },
  });

  // Smooth Scroll
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      const target = document.querySelector(targetId);

      if (!target) return;

      e.preventDefault();

      const startPosition = window.scrollY;
      const targetPosition =
        target.getBoundingClientRect().top + window.scrollY;
      const distance = targetPosition - startPosition;
      const duration = 1500;
      let startTime = null;

      function smoothScroll(currentTime) {
        if (!startTime) startTime = currentTime;

        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Ease in-out
        const ease =
          progress < 0.5
            ? 2 * progress * progress
            : 1 - Math.pow(-2 * progress + 2, 2) / 2;

        window.scrollTo(0, startPosition + distance * ease);

        if (progress < 1) {
          requestAnimationFrame(smoothScroll);
        }
      }

      requestAnimationFrame(smoothScroll);
    });
  });

  // Consultation Form
  $("#consultationForm").on("submit", function (e) {
    e.preventDefault();

    const form = this;
    const submitButton = $("#consultationSubmit");
    const status = $("#consultationStatus");

    // Clear previous message
    status.html("");

    // Disable button
    submitButton.prop("disabled", true);
    submitButton.text("Sending...");

    $.ajax({
      url: "php/send-consultation.php",

      type: "POST",

      data: $(form).serialize(),

      dataType: "json",

      success: function (response) {
        if (response.success) {
          status.html(
            '<div class="alert alert-success mt-2">' +
              response.message +
              "</div>",
          );

          // Clear form
          form.reset();
        } else {
          status.html(
            '<div class="alert alert-danger mt-2">' +
              response.message +
              "</div>",
          );
        }
      },

      error: function () {
        status.html(
          '<div class="alert alert-danger mt-2">' +
            "Unable to send your request. Please try again later." +
            "</div>",
        );
      },

      complete: function () {
        // Enable button again
        submitButton.prop("disabled", false);

        submitButton.text("Schedule Consultation");
      },
    });
  });

  // =========================================================
  // CONTACT FORM
  // =========================================================

  $("#contactForm").on("submit", function (e) {
    e.preventDefault();

    var form = $(this);
    var button = $("#sendMessageButton");
    var buttonText = $("#sendMessageText");
    var spinner = $("#sendMessageSpinner");
    var status = $("#contactStatus");

    // Clear previous message
    status.removeClass("text-success text-danger");
    status.html("");

    // Disable button
    button.prop("disabled", true);

    // Change button state
    buttonText.text("Sending...");
    spinner.removeClass("d-none");

    // Send form data
    $.ajax({
      url: "../php/send-contact.php",
      type: "POST",
      data: form.serialize(),
      dataType: "json",

      success: function (response) {
        if (response.status === "success") {
          status.addClass("text-success").html(response.message);

          // Reset form
          form[0].reset();
        } else {
          status.addClass("text-danger").html(response.message);
        }
      },

      error: function () {
        status
          .addClass("text-danger")
          .html("Sorry, something went wrong. Please try again later.");
      },

      complete: function () {
        // Enable button
        button.prop("disabled", false);

        // Restore button
        buttonText.text("Send Message");
        spinner.addClass("d-none");
      },
    });
  });

  // =========================================================
  // GET QUOTE FORM
  // =========================================================

  $("#quoteForm").on("submit", function (e) {
    e.preventDefault();

    var form = this;

    var button = $("#quoteSubmit");
    var buttonText = $("#quoteSubmitText");
    var spinner = $("#quoteSubmitSpinner");
    var status = $("#quoteStatus");

    // Clear previous message
    status.removeClass("text-success text-danger").html("");

    // Disable button
    button.prop("disabled", true);

    // Change button state
    buttonText.text("Sending...");
    spinner.removeClass("d-none");

    // =====================================================
    // FORM DATA
    // =====================================================

    var formData = new FormData(form);

    // =====================================================
    // SEND DATA
    // =====================================================

    $.ajax({
      url: "../php/send-quote.php",

      type: "POST",

      data: formData,

      processData: false,

      contentType: false,

      dataType: "json",

      // =================================================
      // SUCCESS
      // =================================================

      success: function (response) {
        if (response.status === "success") {
          status.addClass("text-success").html(response.message);

          // Reset form
          form.reset();
        } else {
          status.addClass("text-danger").html(response.message);
        }
      },

      // =================================================
      // ERROR
      // =================================================

      error: function (xhr, statusText, errorThrown) {
        console.log(xhr.responseText);

        status
          .addClass("text-danger")
          .html("Sorry, something went wrong. Please try again later.");
      },

      // =================================================
      // COMPLETE
      // =================================================

      complete: function () {
        // Enable button
        button.prop("disabled", false);

        // Restore button
        buttonText.text("Request a Quote");

        // Hide spinner
        spinner.addClass("d-none");
      },
    });
  });
})(jQuery);
