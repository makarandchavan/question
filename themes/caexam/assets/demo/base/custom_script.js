$(function () {
	var _course = $('#course'),
		_others = $('.others'),
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

        var id = $(".nav-tabs").children().length;

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

            $('.custom-tabs .tab-pane.active table tbody').append('<tr data-nid="'+ $(this).attr('data-nid') +'"" id="dest' + theid + '"><td>' +
                $(this).find("td").eq(0).html() + '</td><td>' +
                $(this).find("td").eq(1).html() + '</td></tr>');

        }


    });

    // End add row

    // Table row click event

    $(".question-list tbody").on('click', 'tr', function(event) {
        event.preventDefault();
        $.ajax( "/get-question?qid=" + $(this).attr('data-nid'))
        .done(function(html_response) {
          var myJSON = JSON.parse(html_response);
          $('#question-question').html(myJSON.question);
          $('.question-heading').html(myJSON.nid + '<small>' + myJSON.title + '</small>');
          $('.question-solution').html(myJSON.solution);
          $('.question-vsolution').html(myJSON.video_solution);
          $('.question-review').html(myJSON.question_review);

          $('.report-error-nid').val(myJSON.nid);
          $('.report-error-title').val(myJSON.title);
        });
    });

    // End Table row click event

    // Save list
    _savelist.on('click', function(){
        $(this).attr('disabled', 'disabled');
        console.log('SAVED');
    });
    // End Save list

    // Datatable filter
    var table = $('#qList').DataTable({
       dom: 'lrtip',
       paging:   false,
       ordering: false,
       info:     false
    });
    
    $('#m_select2_exam').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#m_select2_author').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#m_select2_year').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#m_select2_subject').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#m_select2_topic').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#m_select2_sub_topic').on('change', function(){
       table.search(this.value).draw();   
    });

    $('.copy-url').click(function(e){
        e.preventDefault();
        var copyText = $('.copy-text-value');
        copyText.select();
        console.log(copyText);
        document.execCommand("copy");
    });

    $('.report-error').on('click', function(event){
        event.preventDefault();
        $.ajax( "/report-error?qid=" + $('.report-error-nid').val() + '&title=' + $('.report-error-title').val())
        .done(function(html_response) {
          $('.report-success').show();
          $('.report-error-form').hide();
          $('.report-error').hide();
        });
    });
});