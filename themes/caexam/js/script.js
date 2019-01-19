var Drupal = Drupal || {};

(function ($, Drupal, Bootstrap) {
	"use strict";

	var openNav = $('.open'),
		closeNav = $('.closebtn'),
		navPanel = $('#mySidenav'),
		parters = $('.logo-slider'),
		bannerSlider= $('.banners'),
		dropzone = $('#dropzone'),
		quoteForm = $('#quote'),
		freelance = $('#freelance'),
		contact = $('#contact'),
		freelanceForm = $('#wizard'),
		topicSlider = $('.topics');

	// Quote page validation -

	if (quoteForm.length) {
		quoteForm.validate({
			rules: {
				email: 'required',
				entreprise: 'required'
			}
		});

		$('.form-control').on("blur keyup", function(){
			if ($('#quote').valid()) {
				$('#submit-quote').removeAttr('disabled');
			} else {
				$('#submit-quote').attr("disabled", "disabled");
				// $('<span class="ti-alert"></span>').prependTo('div.error');
			}
		});
	}

	$('#submit-quote').on('click', function(e){
		e.preventDefault();
		$(this).attr('disabled', 'disabled');

		$.ajax({
	        url: "/submit/quote",      // Url to which the request is send
	        type: "POST",                   // Type of request to be send, called as method
	        data:  {uploadedfile:$('.uploaded-file').val(),email:$('#email').val(),entreprise:$('#entreprise').val(),information:$('#information').val()},
	        cache: false,
	        success: function(data)         // A function to be called if request succeeds
	        {
	        	$('form#quote').html('<div class="thank-you"><img src="img/thankyou.png" alt=""></div>');
	        }
	    });
	    return false;
	});
	// 

	// Freelance page validation - 

	if (freelance.length) {
		freelance.validate({
			rules: {
				email: 'required',
				name: 'required',
				firstname: 'required'
			}
		});

		$('.form-control').on("blur keyup", function(){
			if ($('#freelance').valid()) {
				$('#freelaceSubmit').removeAttr('disabled');
			} else {
				$('#freelaceSubmit').attr("disabled", "disabled");
			}
		});
	}
	// 

	// Contact page validation -

	if (contact.length) {
		contact.validate({
			rules: {
				email: 'required',
				name: 'required',
				entreprise: 'required',
				message: 'required'
			}
		});

		$('.form-control').on("blur keyup", function(){
			if ($('#contact').valid()) {
				$('#submitContact').removeAttr('disabled');
			} else {
				$('#submitContact').attr("disabled", "disabled");
			}
		});
	}
	// 

	$('[data-toggle="popover"]').popover();
	
	$(openNav).on('click', function(e){
		e.preventDefault();
		$(navPanel).css('left', '0px');
	});

	$(closeNav).on('click', function(e){
		e.preventDefault();
		$(navPanel).css('left', '-250px');
	});

	
	// Step Form
	freelanceForm.validate({
	    errorPlacement: function errorPlacement(error, element) { element.before(error); },
	    rules: {
	        dob: 'required',
	        address: 'required',
	        city: 'required',
	        zip: 'required',
	        country: 'required',
	        work: 'required',
	        'services[]': {
	        	required: true,
	        	minlength: 1
	        },
	        'support[]': {
	        	required: true
	        },
	        language1: 'required',
	        language2: 'required',
	        language3: 'required',
	        language4: 'required',
	        'translator[]': {
	        	required: true,
	        	minlength: 1
	        },
	        'working[]': {
	        	required: true,
	        	minlength: 1
	        },
	        'quality[]': {
	        	required: true,
	        	minlength: 1
	        },
	        'procedures[]': {
	        	required: true,
	        	minlength: 1
	        },
	        'involvement[]': {
	        	required: true
	        },
	        'professional[]': {
	        	required: true,
	        	minlength: 10
	        },
	        'rating1': {
	        	required: true
	        },
	        'rating2': {
	        	required: true
	        },
	        'rating3': {
	        	required: true
	        },
	        'rating4': {
	        	required: true
	        },
	        'rating5': {
	        	required: true
	        },
	        'rating6': {
	        	required: true
	        },
	        'rating7': {
	        	required: true
	        },
	        'rating8': {
	        	required: true
	        },
	        'rating9': {
	        	required: true
	        },
	        'rating10': {
	        	required: true
	        },
	        'rating11': {
	        	required: true
	        },
	        'rating12': {
	        	required: true
	        },
	        'rating13': {
	        	required: true
	        },
	        'rating14': {
	        	required: true
	        },
	        'rating15': {
	        	required: true
	        },
	        'rating16': {
	        	required: true
	        },
	        'rating17': {
	        	required: true
	        },
	        'rating18': {
	        	required: true
	        },
	        'rating19': {
	        	required: true
	        },
	        'rating20': {
	        	required: true
	        },
	        'rating21': {
	        	required: true
	        },
	        'rating22': {
	        	required: true
	        },
	        'rating23': {
	        	required: true
	        },
	        'rating24': {
	        	required: true
	        },
	        'free': {
	        	required: true
	        },
	        'transfer[]': {
	        	required: true,
	        	minlength: 1
	        },
	        profile: {
	        	required: true,
	        	url: true
	        },
	        company1: 'required',
	        contact1: 'required',
	        email1: 'required',
	        company2: 'required',
	        contact2: 'required',
	        email2: 'required'
	        
	    }
	});

	if (freelanceForm.length) {
		freelanceForm.steps({
	        headerTag: 'h3',
	        bodyTag: 'section',
	        transitionEffect: 'slideLeft',
	        saveState: true,
	        onStepChanging: function (event, currentIndex, newIndex){
	        	freelanceForm.validate().settings.ignore = ":disabled,:hidden";
        		return freelanceForm.valid();

        		if (currentIndex > newIndex) {
		            return true;
		        }
	        },
	        onStepChanged: function (event, currentIndex, newIndex){

	        	if (currentIndex === 3) {
	        		$('.prof').click(function() {
					    if($(this).is(':checked')) {
					        $(this).closest('.form-group').find('input:radio').removeAttr('disabled');
					    } else {
					        $(this).closest('.form-group').find('input:radio').attr('disabled', 'disabled');
					    }
					}); 
	        	}

		        if (currentIndex === 4) {
		        	var previewNode = document.querySelector("#template");
						previewNode.id = "";

					var previewTemplate = previewNode.parentNode.innerHTML;
						previewNode.parentNode.removeChild(previewNode);

					var myDropzone = new Dropzone(document.body, {
			            url: "/uploadfile", // Set the url
			            thumbnailWidth: 40,
			            thumbnailHeight: 40,
			            parallelUploads: 1,
			            maxFilesize: 20, // MB
			            previewTemplate: previewTemplate,
			            //autoQueue: false, // Make sure the files aren't queued until manually added
			            previewsContainer: "#previews" // Define the container to display the previews
			            // clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
			          });
			          myDropzone.on("complete", function(file) {
			            jQuery('.uploaded-file').attr('value', file.name);
			          });
		        }

		        if (currentIndex === 5) {
		        	$('.actions').hide();
		          var mainFormData = $('#freelance').serializeArray();
		          var wizardFormData = $('#wizard').serializeArray();
		          $.ajax({
		            url: "/submit/freelance", // Url to which the request is send
		            type: "POST", // Type of request to be send, called as method
		            data: {
		              mainFormData,
		              wizardFormData
		            },
		            cache: false,
		            success: function(data) // A function to be called if request succeeds
		            {
		              console.log('done');
		            }
		          });
		        }
		    }
	    });
	}

// 
	
	if (topicSlider.length) {
		topicSlider.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			infinite: false,
			nextArrow: '<button type="button" class="slick-next ti-arrow-circle-right"></button>',
			prevArrow: '<button type="button" class="slick-prev ti-arrow-circle-left"></button>'
		});
	}

	if (bannerSlider.length) {
		bannerSlider.slick({
			arrows: false,
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: true,
			fade: true,
			speed: 2000,
			cssEase: 'linear',
  			autoplaySpeed: 5000
		});
	}

	if (parters.length) {
		parters.slick({
			arrows: false,
			slidesToShow: 2,
			slidesToScroll: 1,
			autoplay: true,
  			autoplaySpeed: 1000,
  			responsive: [
			    {
			      breakpoint: 1024,
			      settings: {
			        slidesToShow: 2,
			        slidesToScroll: 1
			      }
			    },
			    {
			      breakpoint: 600,
			      settings: {
			        slidesToShow: 2,
			        slidesToScroll: 1
			      }
			    },
			    {
			      breakpoint: 480,
			      settings: {
			        slidesToShow: 1,
			        slidesToScroll: 1
			      }
			    }
			  ]
		});
	}

// File Upload with Dropzone

var dropWrapper = document.getElementsByClassName("dzone");

if (dropWrapper.length) {
	
	var previewNode = document.querySelector("#template");
		previewNode.id = "";

	var previewTemplate = previewNode.parentNode.innerHTML;
		previewNode.parentNode.removeChild(previewNode);

	var myDropzone = new Dropzone(document.body, {
	  url: "/uploadfile", // Set the url
	  thumbnailWidth: 40,
	  thumbnailHeight: 40,
	  parallelUploads: 1,
	  maxFilesize: 20, // MB
	  addRemoveLinks: true,
	  previewTemplate: previewTemplate,
	  maxFiles: 1,
	  autoQueue: true, // Make sure the files aren't queued until manually added
	  previewsContainer: "#previews", // Define the container to display the previews
	  clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
	});

	// File size -
	var totalSizeLimit = 20*1024*1024; // 20MB
	myDropzone.on("uploadprogress", function(file, progress, bytesSent) {
		var alreadyUploadedTotalSize = getTotalPreviousUploadedFilesSize();

	    if((alreadyUploadedTotalSize + bytesSent) > totalSizeLimit) {
	      jQuery(this).disable();
	    }
	});

	myDropzone.on("complete", function(file) {
	  jQuery('.uploaded-file').attr('value', file.name);
	});

	// Hide the total progress bar when nothing's uploading anymore
	myDropzone.on("queuecomplete", function(progress) {
	  document.querySelector(".progress").style.opacity = "0";
	});

	function getTotalPreviousUploadedFilesSize(){
	   var totalSize = 0;
	   myDropzone.getFilesWithStatus(Dropzone.SUCCESS).forEach(function(file){
	      totalSize = totalSize + file.size;
	   });
	   return totalSize;
	}
	// 
}

// if (cvUpload.length) {
	
// 	var previewNode = document.querySelector("#template");
// 		previewNode.id = "";

// 	var previewTemplate = previewNode.parentNode.innerHTML;
// 		previewNode.parentNode.removeChild(previewNode);

// 	var myDropzone = new Dropzone(document.body, {
// 	  url: "/target-url", // Set the url
// 	  thumbnailWidth: 40,
// 	  thumbnailHeight: 40,
// 	  parallelUploads: 20,
// 	  maxFilesize: 20, // MB
// 	  previewTemplate: previewTemplate,
// 	  //autoQueue: false, // Make sure the files aren't queued until manually added
// 	  previewsContainer: "#previews" // Define the container to display the previews
// 	  // clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
// 	});

// }



// Slide In Panel
	
	var panelTriggers = document.getElementsByClassName('js-cd-panel-trigger');
	if( panelTriggers.length > 0 ) {
		for(var i = 0; i < panelTriggers.length; i++) {
			(function(i){
				var panelClass = 'js-cd-panel-'+panelTriggers[i].getAttribute('data-panel'),
					panel = document.getElementsByClassName(panelClass)[0];
				// open panel when clicking on trigger btn
				panelTriggers[i].addEventListener('click', function(event){
					event.preventDefault();
					addClass(panel, 'cd-panel--is-visible');
				});
				//close panel when clicking on 'x' or outside the panel
				panel.addEventListener('click', function(event){
					if( hasClass(event.target, 'js-cd-close') || hasClass(event.target, panelClass)) {
						event.preventDefault();
						removeClass(panel, 'cd-panel--is-visible');
					}
				});
			})(i);
		}
	}
	
	//class manipulations - needed if classList is not supported
	//https://jaketrent.com/post/addremove-classes-raw-javascript/
	function hasClass(el, className) {
	  	if (el.classList) return el.classList.contains(className);
	  	else return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
	}
	function addClass(el, className) {
	 	if (el.classList) el.classList.add(className);
	 	else if (!hasClass(el, className)) el.className += " " + className;
	}
	function removeClass(el, className) {
	  	if (el.classList) el.classList.remove(className);
	  	else if (hasClass(el, className)) {
	    	var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
	    	el.className=el.className.replace(reg, ' ');
	  	}
	}

})(window.jQuery, window.Drupal, window.Drupal.bootstrap);
