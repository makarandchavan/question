// https://learn.jquery.com/plugins/basic-plugin-creation/
var Drupal = Drupal || {};

(function ($, Drupal, Bootstrap) {
  //noinspection JSAnnotator
  $.fn.pack = function(options) {
    var settings = $.extend({
        // margin: 10px;
      }, options );

    // this should be a container div with a series of img or figure children.
    $(this).children().each(function() {
        if ($(this).is("img")) {
          var img = this;
          $(this).wrap('<div />');
          var flexWrapper = $(this).parent();
        } else { // <figure> or <a>
          var img = $(this).find("img")[0];
          var flexWrapper = $(this)
        }
        let aspect = img.naturalWidth / img.naturalHeight;
        flexWrapper.css({ flex: aspect + "" });
      });

    return this;
    // });
  };
})(window.jQuery, window.Drupal, window.Drupal.bootstrap);
