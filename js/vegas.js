/**
 * @file
 * Vegas jQuery Plugin Drupal Integration.
 */

(function ($) {

/**
 * Drupal vegas behavior.
 */
Drupal.behaviors.vegas = {
  attach: function (context, settings) {

    var vegas = drupalSettings.vegas.settings || [];

    if (vegas) {
      $('body', context).once('vegas').vegas(vegas);
    }
  }
};

})(jQuery);
