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
    <div class="table-responsive">
      <table id="table" class="table table-striped table-bordered table-hover mb-5">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Created By</th>
                <th scope="col">Child Tag</th>
            </tr>
        </thead>
        <tbody id="tablecontents" >
            @foreach($tags as $tag)
            <tr class="rowRef" data-id="{{ $tag->id }}" >
                <td><a href="tags/{{ $tag->id }}"><i class="fa-solid fa-plus"></i></a>
                  
                    <button type="button" class="btn btn-primary">
                      {{ $tag->name }} <span class="badge badge-light">{{ $tag->link_count }}</span>
                    </button>

                  </td>
                <td><a href="/users/{{ $tag->createdBy->id }}">{{ $tag->createdBy->name }}</td>
                <td>
                  @foreach ( $tag->childTags as $child_tag )
                    <a href="tags/{{ $child_tag->id }}">{{ $child_tag->name }}</a>,
                  @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
      </table>
      
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
    let tags = $('#tag').val();
    let file = $('#file')[0].files[0];

    var formData = new FormData();
    formData.append('file', file);
    formData.append('link', link);
    formData.append('tags', tags);
    formData.append("_token", "{{ csrf_token() }}");
    // link:link,
    // tags:tags,
    // file:file,
    
    $.ajax({
      url: "/links/insert",
      type:"POST",
      data: formData,
      processData: false,  // tell jQuery not to process the data
       contentType: false,  // tell jQuery not to set contentType
      success:function(response){
        toastr.success(response.message);
        // window.location.href = "{{ route('links.list','message=New links added ...') }}";
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
