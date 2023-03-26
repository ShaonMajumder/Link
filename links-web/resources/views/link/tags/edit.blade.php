@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
  $("#tag").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  });

  // $("#tag").on("input", function(){
  //   // Print entered value in a div box
    

  //   let tag = $('#tag').val();
  //   $.ajax({
  //     url: "/links/check-unique",
  //     type:"POST",
  //     data:{
  //       "_token": "{{ csrf_token() }}",
  //       tag:tag 
  //     },
  //     success:function(response){
  //       if(response.status == false) {
  //         toastr.warning(response.message);
  //         let data = response.data.selected_tags;
  //         data = JSON.parse(data); //convert to javascript array
  //         values = '';
  //         $.each(data,function(key,value){
  //           values+="<option value='"+value.id+"' selected>"+value.name+"</option>";
  //         });

  //         data = response.data.unselected_tags;
  //         data = JSON.parse(data); //convert to javascript array
  //         $.each(data,function(key,value){
  //           values+="<option value='"+value.id+"'>"+value.name+"</option>";
  //         });

  //         $("#tag").html(values);
  //       }
  //     },
  //     error: function(response) {
  //       toastr.error(response.message);
  //     },
  //   });
  // });

  
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

                    <form id="form" action="{{url('links/insert')}}" method="post">
                      @csrf
                        <input type="hidden" id="tag_id" value="{{ $tag->id }}" />
                      
                        <div class="form-group">
                          <label for="tag_name">Tag</label>
                          <input type="text" class="form-control" id="tag_name" name="tag_name" placeholder="tag_name" value="{{ $tag->name }}">
                        </div>

                        
                        <div class="form-group">
                          <label for="inputPropery">Child Tags</label>
                          {{-- <input type="text" class="form-control" id="inputPropery" aria-describedby="tagHelp" placeholder="Enter email"> --}}
                          <select style="width:100%;" id="tag" name="tag[]" multiple="">
                            <option></option>
                            @foreach ($tags as $item)
                              @if( in_array($item->id, $child_tags ) )
                                <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                              @else
                                <option value="{{ $item->id }}" >{{ $item->name }}</option>
                              @endif
                            @endforeach
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
    let parent_tag = {{ $tag->id }};
    let data = response.data;
    data = JSON.parse(data); //convert to javascript array
    values = '';
    $.each(data,function(key,value){
      if(value.id != parent_tag)
        values+="<option value='"+value.id+"'>"+value.name+"</option>";
    });
    $("#tag").html(values); 
  });




  $("#form").submit(function(e){
    
    e.preventDefault();

    let tag = $('#tag_name').val();
    let tags = $('#tag').val();
    let tag_id = $('#tag_id').val();
    
    $.ajax({
      url: tag_id+"/update",
      type:"POST",
      data: {
        "_token": "{{ csrf_token() }}",
        tags:tags
      },
      success:function(response){
        toastr.success(response.message);
        // window.location.href = "{{ route('links.index','message=New links added ...') }}";
        // if(response.status)
        //   $('#form')[0].reset();
      },
      error: function(response) {
        let data = response.responseJSON;
        toastr.error(data.message);
      },
    });
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
