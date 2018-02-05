(function($) {
	'use strict';
	$(function() {
		$.widget('ui.autocomplete', $.ui.autocomplete, {
			options: {
				highlight: false
			},
			_init: function() {
				if (this.options.highlight === true) {
					this.options.highlight = '<span class="cl-word-highlight">$&</span>';
				}
			},
			_renderItem: function(ul, item) {
				return (typeof this.options.highlight !== 'string')
					? this._super(ul, item)
					: $('<li>').html(
						String(item.label).replace(
							new RegExp('(' + $.ui.autocomplete.escapeRegex(this.term) + ')', 'gi'),
							this.options.highlight
						)
				).appendTo(ul);
			}
		});
	});
})(jQuery);