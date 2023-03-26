@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
  $("#tag").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  });

  $("#link").on("input", function(){
    // Print entered value in a div box
    

    let link = $('#link').val();
    $.ajax({
      url: "/links/check-unique",
      type:"POST",
      data:{
        "_token": "{{ csrf_token() }}",
        link:link 
      },
      success:function(response){
        if(response.status == false) {
          toastr.warning(response.message);
          let data = response.data.selected_tags;
          data = JSON.parse(data); //convert to javascript array
          values = '';
          $.each(data,function(key,value){
            values+="<option value='"+value.id+"' selected>"+value.name+"</option>";
          });

          data = response.data.unselected_tags;
          data = JSON.parse(data); //convert to javascript array
          $.each(data,function(key,value){
            values+="<option value='"+value.id+"'>"+value.name+"</option>";
          });

          $("#tag").html(values);
        }
      },
      error: function(response) {
        toastr.error(response.message);
      },
    });
  });

  
  $( "#file-button" ).click(function(e){
    e.preventDefault();
    $('#file').click();
  });

  $('input[type=file]').change(function() { 
    // select the form and submit
      $('#form').submit(); 
  });

});

</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="form" action="" method="post">
                      @csrf
                        <div class="form-group">
                          <label for="link">Full link</label>
                          <input type="text" class="form-control" id="link" name="link" placeholder="Link">
                        </div>

                        <div class="form-group">
                          <label for="inputPropery">tag Name</label>
                          {{-- <input type="text" class="form-control" id="inputPropery" aria-describedby="tagHelp" placeholder="Enter email"> --}}
                          <select style="width:100%;"   id="tag" name="tag" multiple="">
                            <option></option>
                          </select>
                          {{-- <small id="tagHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                        </div>

                        
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready( function() {
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  };

  $.getJSON("/links/listtags",function(response){
    let data = response.data;
    data = JSON.parse(data); //convert to javascript array
    values = '';
    $.each(data,function(key,value){
      
      values+="<option value='"+value.id+"'>"+value.name+"</option>";
    });
    $("#tag").html(values); 
  });




  $("#form").submit(function(e){
    
    e.preventDefault();

    let link = $('#link').val();
    let tags = $('#tag_id').val();
    let tags = $('#tag').val();
    let file = $('#file')[0].files[0];

    var formData = new FormData();
    formData.append('link', link);
    formData.append('tags', tags);
    formData.append("_token", "{{ csrf_token() }}");
    // link:link,
    // tags:tags,
  
    
    $.ajax({
      url: "/tags/"++"/update",
      type:"POST",
      data: formData,
      processData: false,  // tell jQuery not to process the data
      contentType: false,  // tell jQuery not to set contentType
      success:function(response){
        toastr.success(response.message);
        // window.location.href = "{{ route('links.index','message=New links added ...') }}";
        // if(response.status)
        //   $('#form')[0].reset();
      },
      error: function(response) {
        toastr.error(response.message);
      },
    });
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
