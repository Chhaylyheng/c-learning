/* Japanese initialisation for the jQuery UI date picker plugin. */
/* Written by Kentaro SATO (kentaro@ranvis.com). */
(function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define([ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}(function( datepicker ) {

datepicker.regional['ja'] = {
	closeText: '閉じる',
	prevText: '&#x3C;前',
	nextText: '次&#x3E;',
	currentText: '今日',
	monthNames: ['1月','2月','3月','4月','5月','6月',
	'7月','8月','9月','10月','11月','12月'],
	monthNamesShort: ['1月','2月','3月','4月','5月','6月',
	'7月','8月','9月','10月','11月','12月'],
	dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
	dayNamesShort: ['日','月','火','水','木','金','土'],
	dayNamesMin: ['日','月','火','水','木','金','土'],
	weekHeader: '週',
	dateFormat: 'yy/mm/dd',
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: '年'};

datepicker.regional["en"] = { // Default regional settings
	closeText: "Done", // Display text for close link
	prevText: "Prev", // Display text for previous month link
	nextText: "Next", // Display text for next month link
	currentText: "Today", // Display text for current month link
	monthNames: [ "January","February","March","April","May","June",
		"July","August","September","October","November","December" ], // Names of months for drop-down and formatting
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ], // For formatting
	dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ], // For formatting
	dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ], // For formatting
	dayNamesMin: [ "Su","Mo","Tu","We","Th","Fr","Sa" ], // Column headings for days starting at Sunday
	weekHeader: "Wk", // Column header for week of the year
	dateFormat: "mm/dd/yy", // See format options on parseDate
	firstDay: 0, // The first day of the week, Sun = 0, Mon = 1, ...
	isRTL: false, // True if right-to-left language, false if left-to-right
	showMonthAfterYear: false, // True if the year select precedes month, false for month then year
	yearSuffix: "" // Additional text to append to the year in the month headers
};

datepicker.setDefaults(datepicker.regional['en']);
return datepicker.regional['en'];
}));