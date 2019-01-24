$(function () {
	var _course = $('#course'),
		_others = $('.others');

    $('#m_datepicker').datepicker();

    _course.on('change', function() {
    	var self = $(this).val();

    	if (self === 'Others') {
    		_others.removeClass('m--hide');
    	} else {
    		_others.addClass('m--hide');
    	}
    	console.log(self);
    })
});