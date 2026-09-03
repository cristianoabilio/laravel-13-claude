/*
Add Prescription modal on the doctor Patient Details page: lets the doctor
add/remove repeatable medicine rows before submitting. Each row's inputs are
named items[N][field] - "Add More" clones the last row and re-indexes it,
the trash icon removes a row (but never the last one).
*/

(function ($) {
	"use strict";

	function reindexRows($container) {
		$container.find('[data-item-row]').each(function (index) {
			$(this).find('input').each(function () {
				var name = $(this).attr('name');
				if (!name) {
					return;
				}

				$(this).attr('name', name.replace(/items\[\d+\]/, 'items[' + index + ']'));
			});
		});
	}

	$(document).on('click', '#add-prescription-row', function (e) {
		e.preventDefault();

		var $container = $('#prescription-items');
		var $rows = $container.find('[data-item-row]');
		var $newRow = $rows.last().clone();

		$newRow.find('input').val('');
		$container.append($newRow);
		reindexRows($container);
	});

	$(document).on('click', '#prescription-items .remove-item', function (e) {
		e.preventDefault();

		var $container = $('#prescription-items');
		if ($container.find('[data-item-row]').length <= 1) {
			return;
		}

		$(this).closest('[data-item-row]').remove();
		reindexRows($container);
	});

})(jQuery);
