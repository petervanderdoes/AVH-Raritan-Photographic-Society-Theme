(function ($) {
  var media = wp.media;

  // Wrap the render() function to append controls.
  media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
    render: function () {
      var $el = this.$el;
      media.view.Settings.prototype.render.apply(this, arguments);

      // Append the type template and update the settings.
      $el.append(media.template('rps-gallery-settings'));

      // lil hack that lets media know there's a type attribute.
      media.gallery.defaults.type = 'default';
      this.update.apply(this, ['layout']);

      // Hide the Columns setting for all types except Default
      $el.find('select[name=layout]').on('change', function () {
        var columnSetting = $el.find('select[name=columns]').closest('label.setting');
        if ($(this).val() === 'masonry') {
          columnSetting.hide();
        } else {
          columnSetting.show();
        }
      }).change();
      return this;
    },
  });
})(jQuery);
