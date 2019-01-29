//== Class definition

var DatatableColumnRenderingDemo = function() {
  //== Private functions

  // basic demo
  var demo = function() {

    var datatable = $('.m_datatable').mDatatable({
      // datasource definition
      data: {
        type: 'remote',
        source: {
          read: {
            url: 'https://keenthemes.com/metronic/themes/themes/metronic/dist/preview/inc/api/datatables/demos/default.php',
          },
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
      },

      // layout definition
      layout: {
        theme: 'default', // datatable theme
        class: '', // custom wrapper class
        scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
        footer: false, // display/hide footer
        height: 500
      },

      // column sorting
      sortable: true,

      pagination: true,

      search: {
        input: $('#generalSearch'),
        delay: 400,
      },

      rows: {
        callback: function(row, data, index) {
        },
      },

      // columns definition
      columns: [
        {
          field: 'RecordID',
          title: 'Sr. No',
          sortable: false, // disable sort for this column
          width: 40,
          textAlign: 'center'
          // selector: {class: 'm-checkbox--solid m-checkbox--brand'},
        }, {
          width: 200,
          field: 'CompanyAgent',
          title: 'Date',
          template: function(data) {
            var number = mUtil.getRandomInt(1, 14);
            var user_img = '100_' + number + '.jpg';

            if (number > 8) {
              output = '<div class="m-card-user m-card-user--sm">\
                <div class="m-card-user__pic">\
                  <img src="https://keenthemes.com/metronic/themes/themes/metronic/dist/preview/assets/app/media/img/users/' + user_img + '" class="m--img-rounded m--marginless" alt="photo">\
                </div>\
                <div class="m-card-user__details">\
                  <span class="m-card-user__name">' + data.CompanyAgent + '</span>\
                  <a href="" class="m-card-user__email m-link">' +
                  data.CompanyName + '</a>\
                </div>\
              </div>';
            } else {
              var stateNo = mUtil.getRandomInt(0, 7);
              var states = [
                'success',
                'brand',
                'danger',
                'accent',
                'warning',
                'metal',
                'primary',
                'info'];
              var state = states[stateNo];

              output = '<div class="m-card-user m-card-user--sm">\
                <div class="m-card-user__pic">\
                  <div class="m-card-user__no-photo m--bg-fill-' + state +
                  '"><span>' + data.CompanyAgent.substring(0, 1) + '</span></div>\
                </div>\
                <div class="m-card-user__details">\
                  <span class="m-card-user__name">' + data.CompanyAgent + '</span>\
                  <a href="" class="m-card-user__email m-link">' +
                  data.CompanyName + '</a>\
                </div>\
              </div>';
            }

            return output;
          },
        }, {
          field: 'ShipCountry',
          title: 'User',
          width: 150,
          // callback function support for column rendering
          template: function(data) {
            return data.ShipCountry + ' - ' + data.ShipCity;
          },
        }, {
          field: 'ShipAddress',
          title: 'Exam',
          width: 200,
        }, {
          field: 'CompanyEmail',
          title: 'Attempt',
          width: 150,
          template: function(data) {
            return '<a class="m-link" href="mailto:' + data.CompanyEmail +
                '">' +
                data.CompanyEmail + '</a>';
          },
        }, {
          field: 'Status',
          title: 'Subject',
          // callback function support for column rendering
          template: function(data) {
            var status = {
              1: {'title': 'Pending', 'class': 'm-badge--brand'},
              2: {'title': 'Delivered', 'class': ' m-badge--metal'},
              3: {'title': 'Canceled', 'class': ' m-badge--primary'},
              4: {'title': 'Success', 'class': ' m-badge--success'},
              5: {'title': 'Info', 'class': ' m-badge--info'},
              6: {'title': 'Danger', 'class': ' m-badge--danger'},
              7: {'title': 'Warning', 'class': ' m-badge--warning'},
            };
            return '<span class="m-badge ' + status[data.Status].class +
                ' m-badge--wide">' + status[data.Status].title + '</span>';
          },
        }, {
          field: 'Type',
          title: 'Notification/Update',
          // callback function support for column rendering
          template: function(data) {
            var status = {
              1: {'title': 'Online', 'state': 'danger'},
              2: {'title': 'Retail', 'state': 'primary'},
              3: {'title': 'Direct', 'state': 'accent'},
            };
            return '<span class="m-badge m-badge--' + status[data.Type].state +
                ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' +
                status[data.Type].state + '">' + status[data.Type].title +
                '</span>';
          },
        }, {
          field: 'Actions',
          width: 110,
          title: 'Notes',
          sortable: false,
          overflow: 'visible',
          template: function (row, index, datatable) {
            var dropup = (datatable.getPageSize() - index) <= 4 ? 'dropup' : '';
            return '\
            <div class="dropdown ' + dropup + '">\
              <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">\
                                <i class="la la-ellipsis-h"></i>\
                            </a>\
                <div class="dropdown-menu dropdown-menu-right">\
                  <a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>\
                  <a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>\
                  <a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>\
                </div>\
            </div>\
            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
              <i class="la la-edit"></i>\
            </a>\
            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete">\
              <i class="la la-trash"></i>\
            </a>\
          ';
          },
        }],
    });

var E1 = [
    {display: "S - 1", value: "s1"},
    {display: "S - 2", value: "s2"},
    {display: "S - 3", value: "s3"},
    {display: "S - 4", value: "s4"},
    {display: "S - 5", value: "s5"}
];

var E2 = [
    {display: "S - 6", value: "s6"},
    {display: "S - 7", value: "s7"},
    {display: "S - 8", value: "s8"}
];

var E3 = [
    {display: "S - 9", value: "s9"},
    {display: "S - 10", value: "s10"},
    {display: "S - 11", value: "s11"},
    {display: "S - 12", value: "s12"}
];

var E4 = [
    {display: "S - 13", value: "s13"},
    {display: "S - 14", value: "s14"},
    {display: "S - 15", value: "s15"},
    {display: "S - 16", value: "s16"},
    {display: "S - 17", value: "s17"}
];

var E5 = [
    {display: "S - 18", value: "s18"},
    {display: "S - 19", value: "s19"},
    {display: "S - 20", value: "s20"},
    {display: "S - 21", value: "s21"},
    {display: "S - 22", value: "s22"}
];

var E6 = [
    {display: "S - 23", value: "s23"},
    {display: "S - 24", value: "s24"},
    {display: "S - 25", value: "s25"},
    {display: "S - 26", value: "s26"},
    {display: "S - 27", value: "s27"}
];

    $('#m_form_exam').on('change', function() {
      var parent = $(this).val();

      switch (parent) {
        case 'CA Final (Old Course)':
            list(E1);
            break;
        case 'CA IPCC(Old Course)':
            list(E2);
            break;
        case 'CA CPT(Old Course)':
            list(E3);
            break;
        case 'CA Final (New Course)':
            list(E1);
            break;
        case 'CA Intermediate(New Course)':
            list(E2);
            break;
        case 'CA Foundation(New Course)':
            list(E3);
            break;
        case 'All':
            $('#m_form_subject').attr('disabled', 'disabled');
            $('#m_form_subject').html('<option value="">Select subject</option>').selectpicker('refresh');
            break;
        default:
            $('#m_form_subject').attr('disabled', 'disabled').selectpicker('refresh');
            break;
        }

      datatable.search($(this).val(), 'Status');
    });

    $('#m_form_attempt').on('change', function() {
      datatable.search($(this).val(), 'Type');
    });

    $('#m_form_subject').on('change', function() {
      datatable.search($(this).val(), 'Type');
    });

    $('#m_form_exam, #m_form_attempt, #m_form_subject').selectpicker();

    function list(array_list) {
        $('#m_form_subject').removeAttr('disabled');
        $('#m_form_subject').html('');

        $(array_list).each(function (i) {
            $('#m_form_subject').append("<option value=\""+array_list[i].value+"\">"+array_list[i].display+"</option>").selectpicker('refresh');
        });
    }

  };

  return {
    // public functions
    init: function() {
      demo();
    },
  };
}();

jQuery(document).ready(function() {
  DatatableColumnRenderingDemo.init();
});