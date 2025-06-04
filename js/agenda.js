(function ($) {
    Drupal.behaviors.appointmentCalendar = {
      attach: function (context, settings) {
        // Initialize the modal dialog when button is clicked
        $('#calendar-launch-button').click(function () {
          Drupal.dialog.ajax({
            url: '/appointment/calendar-modal',
            dialogType: 'modal',
            dialog: {
              title: 'Appointment Calendar',
              modalClass: 'appointment-calendar-modal',
              width: '80%',
              height: '80%',
              draggable: true,
            },
            width: '80%',
            height: '80%',
          }).show();
        });
      },
    };
  })(jQuery);
  