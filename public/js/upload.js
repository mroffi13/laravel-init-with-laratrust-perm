$(document).ready(function()
{
   $(document).on('change', '.btn-upload', function(){
      console.log($(this)[0].files);
      
      readFile($(this));
   })
})

function readFile(that)
{
   var file = $(that)[0].files[0];

   if(file.size > 5*(1024*1024))
   {
      SwalBs.fire(
         '',
         "This file is more than 5 mb",
         'warning'
      )
      $(that).val('')

      return false;
   }

   var min_width = $(that).data('min-width')
   var min_height = $(that).data('min-height')
   var imgPreview = $(that).closest('.img-box').find('.img-preview');
   var imgPreviewDefault = $(that).data('preview-default');
   var file_name = file.name

   var reader = new FileReader();
   reader.onload = function(e) {
     // The file's text will be printed here
      var tmpImg = new Image() ;
      tmpImg.src = e.target.result
      tmpImg.onload = function() {
         tmpImgWidth = this.width
         tmpImgHeight = this.height

         var width = min_width > tmpImgWidth
         var height = min_height > tmpImgHeight
         
         if(width || height)
         {
            if(width)
            {
               SwalBs.fire(
                  '',
                  `Minimum width ${min_width}`,
                  'warning'
               )
               $(that).val('')
               $(imgPreview).attr('src', imgPreviewDefault)
               $(that).closest('.file-box').find('.file-label').html('Choose file')
            }
            else
            {
               SwalBs.fire(
                  '',
                  `Minimum height ${min_height}`,
                  'warning'
               )
               $(that).val('')
               $(imgPreview).attr('src', imgPreviewDefault)
               $(that).closest('.file-box').find('.file-label').html('Choose file')
            }
         }
         else if((tmpImgWidth/min_width)*min_height !== tmpImgHeight)
         {
            var returnBack = (tmpImgWidth/min_width)*min_height
            SwalBs.fire(
               '',
               `Height must be ${returnBack}`,
               'warning'
            )
            $(that).val('')
            $(imgPreview).attr('src', imgPreviewDefault)
            $(that).closest('.file-box').find('.file-label').html('Choose file')
         }
         else
         {
            $(imgPreview).attr('src', this.src)
            $(that).closest('.file-box').find('.file-label').html(file_name)
         }
      } ;
   };
 
   reader.readAsDataURL(file);
}

