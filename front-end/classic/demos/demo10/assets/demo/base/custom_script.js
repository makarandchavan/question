$(function () {
	var _course = $('#course'),
		_others = $('.others')
        _savelist = $('#saveList');

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
        _savelist.removeAttr('disabled');
        
        var anchor = $(this).siblings('a');
        $(anchor.attr('href')).remove();
        $(this).parent().remove();
        $(".nav-tabs li").children('a').first().click();
    });

    $('.add-contact').click(function (e) {
        e.preventDefault();
        _savelist.removeAttr('disabled');

        var id = $(".nav-tabs").children().length - 3;

        var tabId = 'contact_' + id;
        $(this).closest('li').before('<li class="nav-item"><a class="nav-link" href="#contact_' + id + '">Type '+id+'</a> <span> x </span></li>');
        $('.tab-content').append('<div class="tab-pane" id="'+ tabId +'" role="tabpanel"><form method="POST" action=""><table class="table table-striped table-bordered question-list" id=""><thead><tr><th scope="col">Question Code - <span>Topic</span></th><th scope="col">Subject</th></tr></thead><tbody></tbody></table></form></div>');
       $('.nav-tabs li:nth-child(' + id + ') a').click();
    });

    // End Add Tab

    // Add row

    var addedrows = new Array();

    $("#qList tbody tr").dblclick(function(event) {
        _savelist.removeAttr('disabled');

        var ok = 0;
        var theid = $(this).attr('id').replace("sour", "");

        var newaddedrows = new Array();

        for (index = 0; index < addedrows.length; ++index) {

            // if already selected then remove
            if (addedrows[index] == theid) {

                $(this).css("background-color", "#e5f3fe");

                // remove from second table :
                var tr = $("#dest" + theid);
                tr.css("background-color", "#ffa78f");
                tr.fadeOut(400, function() {
                    tr.remove();
                });

                //addedrows.splice(theid, 1);   

                //the boolean
                ok = 1;
            } else {

                newaddedrows.push(addedrows[index]);
            }
        }

        addedrows = newaddedrows;
        // if no match found then add the row :
        if (!ok) {
            // retrieve the id of the element to match the id of the new row :
            addedrows.push(theid);

            $(this).css("background-color", "#cacaca");

            $('.custom-tabs .tab-pane.active table tbody').append('<tr id="dest' + theid + '"><td>' +
                $(this).find("td").eq(0).html() + '</td><td>' +
                $(this).find("td").eq(1).html() + '</td></tr>');

        }


    });

    // End add row

    // Table row click event

    $(".question-list tbody").on('click', 'tr', function(event) {
        event.preventDefault();

        console.log($(this));
    });

    // End Table row click event

    // Save list
    _savelist.on('click', function(){
        $(this).attr('disabled', 'disabled');
        console.log('SAVED');
    });
    // End Save list
});