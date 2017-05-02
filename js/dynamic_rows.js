$(document).ready(function(){
      var i=1;
      $('#add').click(function(){
           i++;
           $('#dynamic_field').append('<tr id="row'+i+'"><td>'+i+'</td><td><input type="number" step=".01" name="demanda[]" placeholder="Demanda" class="form-control required" /><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove pull-right">X</button></td></tr>');
      });
      $(document).on('click', '.btn_remove', function(){
           var button_id = $(this).attr("id");
           $('#row'+button_id+'').remove();
      });
});
