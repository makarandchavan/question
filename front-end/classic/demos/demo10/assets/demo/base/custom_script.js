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
    })

    // Add Tab

    $(".nav-tabs").on("click", "a", function (e) {
        e.preventDefault();
        if (!$(this).hasClass('add-contact')) {
            $(this).tab('show');
        }
    })
    .on("click", "span", function () {
        var anchor = $(this).siblings('a');
        $(anchor.attr('href')).remove();
        $(this).parent().remove();
        $(".nav-tabs li").children('a').first().click();
    });

    $('.add-contact').click(function (e) {
        e.preventDefault();
        var id = $(".nav-tabs").children().length;
        var tabId = 'contact_' + id;
        $(this).closest('li').before('<li class="nav-item"><a class="nav-link" href="#contact_' + id + '">New Tab</a> <span> x </span></li>');
        $('.tab-content').append('<div class="tab-pane" id="' + tabId + '">Contact Form: New Contact ' + id + '</div>');
       $('.nav-tabs li:nth-child(' + id + ') a').click();
    });

    // End Add Tab
});