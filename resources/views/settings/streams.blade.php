@extends('common.header')

@section('title')
    Streams
@endsection

@section('body')
<div class="container-fluid">
    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Streams</h5>

    <div class="row justify-content-between">
        <div class="col-md-4">
            <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                <span class="underline">Streams in School</span>
            </p>
            <div class="class_list">
                @foreach ($streams as $stream)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>{{ $stream->stream }}</div>
                        <div>
                            <form method="post" class="mb-0 delete_stream_form">
                                @csrf
                                <input type="hidden" value="{{ $stream->id }}" name="delete_id">
                                <button class="btn btn-outline-danger rounded-5" type="submit"><i class="fa fa-x"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-4">
            <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                <span class="underline">Add Stream</span>
            </p>

            <form method="post" id="add_stream_form">
                @csrf
                <div>
                    <label class="form-label fw-bold h6">Stream</label>
                    <input type="text" class="form-control rounded-0" name="stream" placeholder="Enter Stream" required>
                </div>
                <button class="submit-btn mt-3" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            //Add Stream here
            $("#add_stream_form").on('submit',function(e){
                e.preventDefault();
                $.ajax({
                    type:"POST",
                    url:"{{ route('stream.add') }}",
                    data:new FormData(this),
                    processData:false,
                    contentType:false,
                    cache:false,
                    success:function(response){
                        alert(response);
                        location.reload();
                    },error:function(){
                        alert("Failed to Add Stream!");
                    }
                });
            });

            //Delete Stream
            $(".delete_stream_form").on('submit',function(e){
                e.preventDefault();
                const confirm_delete = confirm("Are you sure?");
                if(confirm_delete == true){
                    $.ajax({
                        type:"POST",
                        url:"{{ route('stream.delete') }}",
                        data:new FormData(this),
                        processData:false,
                        contentType:false,
                        cache:false,
                        success:function(response){
                            alert(response);
                            location.reload();
                        },error:function(){
                            alert("Failed to Delete Stream!");
                        }
                    });
                }
            });

        });
    </script>
@endpush