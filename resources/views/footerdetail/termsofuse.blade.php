@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active">Terms of Use</li>
    </ol>
    
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success" id="successMessage">
                    {!! session('success') !!}
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger" id="errorMessage">
                    {!! session('error') !!}
                </div>
            @endif
            
            <div class="panel footerdetail_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>Terms of Use</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    
                    <form class="form-horizontal" method="POST" id="terms-of-use" action="{{ route('update-terms-of-use') }}">
                        @csrf
                        
                        <input type="hidden" name="type" value="terms_of_use">
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <textarea name="description" id="description" class="form-control">{{ $description }}</textarea>
                            </div>
                            
                            @error('description')
                                <label class="error" for="description">{{ $message }}</label>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <div class="row submit">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button class="btn btn-success btn-quirk btn-wide mr5" name="submit" id="update" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @extends('common.footer')
    
    @section('jquerysection')
        <link rel="stylesheet" href="{{ asset('ui/css/footerdetail.css') . '?t=' . Carbon\Carbon::now()->timestamp }}">
        
        <script type="text/javascript">
            $(document).ready(function() {
                $('#description').summernote({
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph', 'height']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['codeview', 'help', 'undo', 'redo']],
                    ],
                    disableDragAndDrop: true
                });

                $('#terms-of-use').on('submit', function(e) {
                    if ($('#description').summernote('isEmpty')) {
                        console.log('contents is empty, fill it!');
                        alert("Please enter description");
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endsection
@endsection