var dhLoading, data_products = [];
var base_url = $('#base_url').val();
const SwalBs = Swal.mixin({
   customClass: {
      confirmButton: 'btn btn-primary',
      cancelButton: 'btn btn-danger mr-2'
   },
   // showClass: {
   //    popup: 'animate__animated animate__fadeInDown'
   // },
   // hideClass: {
   //    popup: 'animate__animated animate__fadeOutUp'
   // },
   reverseButtons: true,
   heightAuto: false,
   buttonsStyling: false
})

const swalDelete = {
   title: 'Apakah anda yakin?',
   text: "Anda tidak dapat mengembalikan data yang sudah dihapus!", 
   icon: 'warning', 
   confirmButtonText: 'Ya!',
   showCancelButton: true
}

const slideToTop = $('#back-to-top');

const img_loading =  '<svg style="margin: auto;background: none;display: block;shape-rendering: auto;" width="71px" height="71px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">'+
                        '<circle cx="50" cy="50" r="29" stroke-width="8" stroke="#007bff" stroke-dasharray="45.553093477052 45.553093477052" fill="none" stroke-linecap="round">'+
                           '<animateTransform attributeName="transform" type="rotate" dur="2s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"/>'+
                        '</circle>'+
                        '<circle cx="50" cy="50" r="20" stroke-width="8" stroke="#314bf2" stroke-dasharray="31.41592653589793 31.41592653589793" stroke-dashoffset="31.41592653589793" fill="none" stroke-linecap="round">'+
                           '<animateTransform attributeName="transform" type="rotate" dur="2s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"/>'+
                        '</circle>'+
                     '</svg>';
$(document).ready(function()
{

   // initiate select2 default
   if($('.select2').length)
      $('.select2:visible').select2();

   if($('[data-widget="iframe-fullscreen"]').length)
   {
      setTimeout(() => {
         $('[data-widget="iframe-fullscreen"]').trigger('click')
      }, 1000)
   }
   $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   }); 

   if($('.select2ajax').length)
   {
      $('.select2ajax:visible').each(function()
      {
         initiateSelect2Ajax(this)
      })
   }

   $(document).on('focus', '.numbering,input[type=number]', function()
   {
      $(this).select();
   })

   $(window).scroll(function () {
      if ($(window).scrollTop() >= 150) 
      {
         if (!$(slideToTop).is(':visible')) 
            $(slideToTop).fadeIn(500);
      } 
      else
         $(slideToTop).fadeOut(500);
   });
   
   $(slideToTop).click(function () {
      
      $("body").animate({
         scrollTop: 0
      }, 1000);
   });

   if($('.bsDualistbox').length)
   {
      $('.bsDualistbox').each(function()
      {
         var dualListBox = $(this).bootstrapDualListbox({
            moveAllLabel: 'Move all',
            removeAllLabel: 'Remove all',
            btnMoveAllText: '<i class="fas fa-angle-double-right"></i>',
            btnRemoveAllText: '<i class="fas fa-angle-double-left"></i>',
            btnClass: 'btn-outline-primary'
         });
         var isRequiredField = dualListBox.attr('required');
         var instance = dualListBox.data('plugin_bootstrapDualListbox');
         var nonSelectedList = instance.elements.select1;
         var el_wasvalidate = dualListBox.parent().find('.invalid-validate').html();

         dualListBox.change(function () {
            if (isRequiredField)
               initDualListBox(dualListBox);
         });

         if (isRequiredField)
            initDualListBox(dualListBox);

         $(el_wasvalidate).insertAfter(nonSelectedList);
      })
   }
   
   if($('.alert.alert-success').length)
   {
      SwalBs.fire({
         title: '',
         text: $('.alert.alert-success').html(),
         icon: 'success',
      })
   }

   if($('.alert.alert-danger').length)
   {
      SwalBs.fire({
         title: '',
         text: $('.alert.alert-danger').html(),
         icon: 'error'
      })
   }

   if($('.select2client').length)
   {
      $('.select2client:visible').select2()
   }

   if($('.alert.alert-warning').length)
   {
      SwalBs.fire({
         title: '',
         text: $('.alert.alert-warning').html(),
         icon: 'warning'
      })
   }

   if($('.numbering').length)
   {
      $('.numbering').number(true, 2)
   }

   if($('.table-responsive').length)
   {
      $('.table-responsive').overlayScrollbars({ });
   }

   if($('[data-toggle="tooltip"]').length)
      $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover' });

   if($('.btn-delete').length)
   {
      $(document).on('click', '.btn-delete', function()
      {
         var table_target = $(this).data('target');
         var curr = $(this);
         if(typeof table_target !== 'undefined')
         {
            if($(table_target+'>tbody').find('tr:visible').length == 1)
            {
               SwalBs.fire(
                  '',
                  'Table ini harus memiliki setidaknya 1 baris!',
                  'warning'
               )
            }  
            else
            {
               swalConfirm((result) => {
                  if(result.isConfirmed){
                     $(curr).closest('tr').remove();
                     switch (table_target) {
                        case '#varList':
                           createOptionVariation()
                           break;
                     
                        default:
                           break;
                     }
                  }
               })
            }
         }
      })
   }

   if($('.uploadFile').length)
   {
      $(document).on('click', '.uploadFile', function()
      {
         $(this).closest('.img-box').find('.btn-upload').click()
      })
   }

   if($('.add-row').length)
   {
      $(document).on('click', '.add-row', function()
      {
         var table_target = $(this).data('target');
         var new_class = $(this).data('classes');

         if(typeof table_target !== 'undefined')
         {
            var row_clone = $(table_target).find('.row-clone').clone('true');
            $(row_clone).attr('class', new_class);
            // $(row_clone).find('.btn-tooltip').tooltip();
            
            $(row_clone).insertBefore($(table_target).find('.row-clone'))
            $(row_clone).find('.required:visible').prop('required', true)
            $(row_clone).find('.toggle-check-tr:visible').bootstrapToggle({
               on: 'Yes',
               off: 'No',
               size: "sm",
               onstyle: "success",
               offstyle: "danger",
            })
            if($('.select2ajax:visible').length)
            {
               $('.select2ajax:visible').each(function()
               {
                  initiateSelect2Ajax(this)
               })
            }

            $(table_target).closest('.table-responsive')
                           .overlayScrollbars()
                           .scroll({ y : "100%"  });

            if($(row_clone).find('.select2product').length)
               $(row_clone).find('.select2product').select2({
                  data: data_products,
                  placeholder: 'Choose product ..',
               }).val(null).trigger('change')
         }
      })
   }

   if($('.swal-alert.top-end').length)
   {
      SwalBs.fire({
         title: '',
         text: $('.swal-alert.top-end').html(),
         icon: $('.swal-alert.top-end').data('icon'),
         position: 'top-end',
         showConfirmButton: false,
         timer: 1500
      })
   }
})

function initiateSelect2Ajax(curr)
{
   var url = $(curr).data('url');
   
   var data_placeholder = $(curr).data('data_placeholder');
   var $this = curr;
   if(typeof url !== 'undefined')
   {
      $(curr).select2({
         ajax: {
            type: 'POST',
            delay: 250,
            url: url,
            data: function (params) {
               var query = {
                  search: params.term,
                  page: params.page || 1
               }
         
               // Query parameters will be ?search=[term]&type=public
               return query;
            }
         },
         allowClear: $(curr).data('allow-clear') || false,
         language: {
            noResults: function(term) {
               return 'Tidak ada yang cocok dengan kata kunci!';
            }
         },
         placeholder: data_placeholder,
         cache: true,
      });
      
      $(curr).data('select2').on("results:message", function () {
         this.dropdown._resizeDropdown();
         this.dropdown._positionDropdown();
      });

      $(curr).on('select2:open', function () {
         var $el = $(curr).data('select2');
         $el.dropdown._resizeDropdown();
         $el.dropdown._positionDropdown();
         setTimeout(() => 
         {
            var $searchfield = $($this).closest('body').find('.select2-search__field');
            document.querySelector('.select2-search__field').focus()
         }, 500)
      });
   }
}

function initDualListBox(dualListBox) {
   var instance = dualListBox.data('plugin_bootstrapDualListbox');
   var nonSelectedList = instance.elements.select1;
   var isDualListBoxValidated = !(instance.selectedElements > 0);
   nonSelectedList.prop('required', isDualListBoxValidated);
   if(!nonSelectedList.hasClass('form-control'))
      nonSelectedList.addClass('form-control');
   instance.elements.originalSelect.prop('required', false);
}

function errorHandling(e, settings, techNote, message)
{
   var response = settings.jqXHR.responseJSON;

   handle(response, message);
}

function handle(response, message=null)
{
   if(typeof response !== 'undefined')
   {
      if(typeof response.source !== 'undefined' && response.source == 'middleware')
         location.reload()
      else
      {
         SwalBs.fire({
            title: '',
            text: response.error ?? response.message ?? message,
            icon: 'error'
         })
      }
   }
}

function post(url, data, key)
{
   if(!dhLoading)
   {
      dhLoading = $.ajax({
         url: url,
         data: data,
         cache: false,
         contentType: false,
         processData: false,
         method: 'POST',
         beforeSend: () => {
            overlay()
         },
         success: (data) => {
            try
            {
               if(data.response !== null)
               {
                  if(typeof data.response.errors !== 'undefined')
                  {
                     SwalBs.fire({
                        title: 'Validasi gagal!',
                        html: data.response.errors,
                        icon: 'error'
                     })
                     overlay()
                  }
                  else if(typeof data.response[key] !== 'undefined' && typeof data.redirect !== 'undefined')
                  {
                     location.href = data.redirect;
                  }
                  else
                  {
                     SwalBs.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error'
                     })
                     overlay()
                  }
               }
               else if(data.error)
               {
                  SwalBs.fire({
                     title: 'Error!',
                     text: data.message,
                     icon: 'error'
                  })
                  overlay()
               }
            } 
            catch (error) 
            {
               SwalBs.fire({
                  title: 'Error JS',
                  text: error,
                  icon: 'error'
               })
               overlay()
            }
            dhLoading = null;
         },
         error: function (xhr, ajaxOptions, thrownError) {
            var response = xhr.responseJSON;
            if(typeof response.source !== 'undefined')
               handle(response);
            else
               SwalBs.fire({
                  title: thrownError,
                  text: response.message,
                  icon: 'error'
               })
            overlay()
            dhLoading = null;
         }
      });
   }
}

function deleteDataTable(url, key, tableId, data = {})
{
   if(!dhLoading)
   {
      dhLoading = $.ajax({
         url: url,
         cache: false,
         method: 'DELETE',
         data: data,
         beforeSend: () => {
            $('.delete-btn').prop('disabled', true)
         },
         success: (data) => {
            try
            {
               if(data.response !== null)
               {
                  if(typeof data.response[key] !== 'undefined' && data.message !== null)
                  {
                     SwalBs.fire({
                        title: '',
                        text: data.message,
                        icon: 'success',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                     })
                     setTimeout(() => {
                        $(tableId).DataTable().ajax.reload()
                     }, 1000)
                  }
                  else
                  {
                     SwalBs.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error'
                     })
                     setTimeout(() => {
                        $(tableId).DataTable().ajax.reload()
                     }, 1000)
                  }
               }
               else if(data.error)
               {
                  SwalBs.fire({
                     title: 'Error!',
                     text: data.message,
                     icon: 'error'
                  })
                  setTimeout(() => {
                     $(tableId).DataTable().ajax.reload()
                  }, 1000)
               }

               dhLoading = null
            } 
            catch (error) 
            {
               SwalBs.fire({
                  title: 'Error JS',
                  text: error,
                  icon: 'error'
               })
               setTimeout(() => {
                  $(tableId).DataTable().ajax.reload()
               }, 1000)
            }
            dhLoading = null;
         },
         error: function (xhr, ajaxOptions, thrownError) {
            var response = xhr.responseJSON;
            SwalBs.fire({
               title: thrownError,
               text: response.message,
               icon: 'error'
            })
            setTimeout(() => {
               $(tableId).DataTable().ajax.reload()
            }, 1000)
            dhLoading = null;
         }
      });
   }
}

function swalConfirm(callback = () => {},data = swalDelete)
{
   SwalBs.fire(data).then((result) => {
      callback(result)
   })
}

function overlay(classes=".overlay")
{
   if($(classes).hasClass('d-none'))
      $(classes).removeClass('d-none')
   else
      $(classes).addClass('d-none')
}

function dualListBoxOption(url, cur, page, selected=[])
{
   $.ajax({
      url: url,
      data: {page: page, result_html: 1, selected:selected},
      cache: false,
      method: 'POST',
      success: (data) => {
         try
         {
            if(typeof data.result_html !== 'undefined' && data.results.length)
            {
               $(cur).append(data.result_html)

               if(data.pagination.more)
                  dualListBoxOption(url, cur, page+1, selected)
               else
               {
                  var dualListBox = $(cur).bootstrapDualListbox('refresh' ,true);
                  initDualListBox(dualListBox);
               }
            }
            else if(data.error)
            {
               SwalBs.fire({
                  title: 'Error!',
                  text: data.message,
                  icon: 'error'
               })
               setTimeout(() => {
                  $(tableId).DataTable().ajax.reload()
               }, 1000)
            }
         } 
         catch (error) 
         {
            SwalBs.fire({
               title: 'Error JS',
               text: error,
               icon: 'error'
            })
         }
         dhLoading = null;
      },
      error: function (xhr, ajaxOptions, thrownError) {
         var response = xhr.responseJSON;
         SwalBs.fire({
            title: thrownError,
            text: response.message,
            icon: 'error'
         })
         dhLoading = null;
      }
   });
}
