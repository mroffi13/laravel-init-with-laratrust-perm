var ajaxLoad, table;

$(document).ready(function()
{
   if($('#permissionList').length)
   {
      getPermissionList()

      $(document).on('click', '.delete-btn', function() {
         var id = $(this).data('id')
         var url = base_url+ 'access-control/permissions/'+id
         swalConfirm((result) => {
            if(result.isConfirmed)
               deleteDataTable(url, 'permission', '#permissionList')
         })
      })
   }

   $('#formSubmit').on('submit', function() {
      console.log('masuk')
      var $form = $('#formSubmit')[0];
      // console.log($('#formSubmit')[0]);
      // console.log(document.querySelector('#formSubmit'));
      console.log($form.checkValidity())
      if($form.checkValidity())
      {
         var data = new FormData($form);
         post($(this).attr('action'), data, 'permission')
      }

      return false;
   })
})

function getPermissionList()
{
   table = $('#permissionList').DataTable( {
      iDisplayLength: 25,
      bServerSide: true,
      ajax: {
         url: base_url + 'access-control/permissions/getPermissionList',
         "data": function ( d ) {
            // console.log(d)
            // d.custom = $('#myInput').val();
            // etc
         }
      },
      // sAjaxDataProp: 'data',
      sServerMethod: 'POST',
      responsive: true,
      processing:true,
      // searchDelay: 1000,
      language: {
         "emptyTable": "Permissions not available.",
         processing: img_loading + 'Loading...',
      },
      "fnDrawCallback": function( oSettings ) 
      { 
         $('[data-toggle-second="tooltip"]').tooltip({ trigger: "hover" });
      },
      "initComplete":function( settings, data)
      {
         $('#permissionList').wrap('<div class="table-responsive"></div>');
      },
      responsive: {
         details: {
            renderer: function ( api, rowIdx, columns ) {
               var data = $.map( columns, function ( col, i ) {
                  return col.hidden ?
                        '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                           '<td class="align-middle" style="border: none">'+col.title+':'+'</td> '+
                           '<td class="align-middle" style="border: none">'+col.data+'</td>'+
                        '</tr>' :
                        '';
               } ).join('');

               return data ?
                  $('<table class="w-sm-100" />').append( data ) :
                  false;
            }
         }
      },  
      columns: [
         {
            data:'number',
            "orderable": false,
            responsivePriority: 1,
            "className": 'align-middle'
         },
         {
            data:'display_name',
            responsivePriority: 2,
            "className": 'align-middle'
         },
         {
            data:'created_name',
            responsivePriority: 4,
            defaultContent: "<i>-</i>",
            "className": 'align-middle'
            // width:"200px"
         },
         {
            data:'created_at',
            responsivePriority: 5,
            defaultContent: "<i>-</i>",
            "className": 'align-middle'
            // width:"200px"
         },
         {
            data:'updated_name',
            responsivePriority: 6,
            defaultContent: "<i>-</i>",
            "className": 'align-middle'
            // width:"150px"
         },
         {
            data:'updated_at',
            responsivePriority: 7,
            defaultContent: "<i>-</i>",
            "className": 'align-middle'
            // width:"200px"
         },
         {
            data:'status',
            responsivePriority: 8,
            defaultContent: "<i>-</i>",
            "className": 'align-middle'
         },
         {
            data:'action_html',
            "orderable": false,
            responsivePriority: 3,
            width: "100px",
            "className": 'text-center align-middle'
         }
      ],
      'order': [[1, 'asc']] // sort order by modified date
      
   });

   $('div.dataTables_filter input').off('keyup.DT input.DT');
 
   var searchDelay = null;
   
   $('div.dataTables_filter input').on('keyup', function() {
      var search = $('div.dataTables_filter input').val();
   
      clearTimeout(searchDelay);
   
      searchDelay = setTimeout(function() {
         if (search != null) {
            table.search(search).draw();
         }
      }, 1000);
   });

   $.fn.dataTable.ext.errMode = 'none';

   table.on('error.dt', function (e, settings, techNote, message) 
   {
      errorHandling(e, settings, techNote, message);
      return true;
   });

}