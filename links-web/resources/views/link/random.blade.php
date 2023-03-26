@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
  $("#tag").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  }).on("select2:selecting", function (e) {
      // var str = $("#s2id_search_code .select2-choice span").text();
      // DOSelectAjaxProd(e.val, str);
      let tag = e.params.args.data.id;
      $.ajax({
        url: "/links/tags/"+tag+"/select-all-parent-tags",
        type:"POST",
        data:{
          "_token": "{{ csrf_token() }}",
        },
        success:function(response){
          if(response.status == true) {
            toastr.warning(response.message);
            let data = response.data;
            let values = $("#tag").val();
            $.each(data,function(key,value){
              values.push(value.id);
            });
            $("#tag").val(values).trigger('change');
          }
        },
        error: function(response) {
          let data = response.responseJSON;
          toastr.error(data.message);
        },
      });
  });

  $("#link").on("input", function(){
    // Print entered value in a div box
    

    let link = $('#link').val();
    $.ajax({
      url: "/links/check-unique",
      type:"POST",
      data:{
        "_token": "{{ csrf_token() }}",
        link:link,
        tags:$('#tag').val()
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
        let data = response.responseJSON;
        toastr.error(data.message);
      },
    });
  });

  


// $(document.body).on("change","#tag",function(){
//  alert(this.value);
// });
// $("#tag").on("select2-selecting", function(e) {
//   alert(e.choice);
//     // $("#search_code").select2("data",e.choice);
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
                      <div class="form-group">
                        <input style="display: none;" type="file" class="form-control" id="file" name="file" placeholder="Choose file">
                        <button id="file-button">Bulk Input</button>
                      </div>

                      <div class="form-group">
                        <label for="inputPropery">tag Name</label>
                        {{-- <input type="text" class="form-control" id="inputPropery" aria-describedby="tagHelp" placeholder="Enter email"> --}}
                        <select style="width:100%;"   id="tag" name="tag[]" multiple="">
                          <option></option>
                        </select>
                        {{-- <small id="tagHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                      </div>
                      
                      <button type="submit" class="btn btn-primary">Search</button>
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
    let tags = $('#tag').val();
    console.log(tags);
    var formData = new FormData();
    formData.append('tags', tags);
    formData.append("_token", "{{ csrf_token() }}");
    // link:link,
    // tags:tags,
    // file:file,

    $.ajax({
      url: "/links/pick/random",
      type:"POST",
      data: formData,
      processData: false,  // tell jQuery not to process the data
       contentType: false,  // tell jQuery not to set contentType
      success:function(response){
        toastr.success(response.message);
        window.open(response.data, "_blank");
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
