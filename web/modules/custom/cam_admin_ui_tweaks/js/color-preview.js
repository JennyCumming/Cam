(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.colorPreview = {
    attach: function (context, settings) {
      const $select = $('.color-select-wrapper select', context);
      const $colorBox = $('.color-preview-box', context);
      
      if ($select.length && $colorBox.length) {
        const colors = JSON.parse($colorBox.attr('data-colors'));
        
        // Function to update color box
        const updateColorBox = () => {
          const selectedColor = $select.val();
          if (colors[selectedColor]) {
            $colorBox.css('background-color', colors[selectedColor]);
          }
        };

        // Update on change and initial load
        $select.on('change', updateColorBox).trigger('change');
      }
    }
  };
})(jQuery, Drupal);
