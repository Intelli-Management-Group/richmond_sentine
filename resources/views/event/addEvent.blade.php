@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('event-list') }}"> Event List</a></li>
        <li class="active">Event {{ isset($event_data) ? "Edit" : "Add" }}</li>
    </ol>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel business_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>Event {{ isset($event_data) ? "Edit" : "Add" }}</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form 
                        action="{{ url('insert-event') }}" 
                        method="post" 
                        enctype="multipart/form-data" 
                        class="form-horizontal" 
                        id="event_form" 
                        data-url="insert-event"
                    >
                        @csrf
                        <input type="hidden" name="event_id" value="{{ isset($event_data) ? $event_data->id : '' }}">
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name', isset($event_data) ? $event_data->name : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter event name"
                                >
                                @error('name')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Description <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea 
                                    name="description" 
                                    id="event_description" 
                                    class="form-control" 
                                    placeholder="Enter description" 
                                    cols="4" 
                                    rows="3"
                                >{{ old('description', isset($event_data) ? $event_data->description : '') }}</textarea>
                                @error('description')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Event date <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="eventDate" 
                                    value="{{ old('eventDate', isset($event_data) ? $event_data->eventDate : '') }}" 
                                    class="form-control" 
                                    id="eventDate" 
                                    autocomplete="off" 
                                    placeholder="Select date"
                                >
                                @error('eventDate')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                            <label class="col-sm-2 control-label">Event time <span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input 
                                    type="time" 
                                    name="eventTime" 
                                    value="{{ old('eventTime', isset($event_data) ? $event_data->eventTime : '') }}" 
                                    class="form-control" 
                                    autocomplete="off"
                                >
                                @error('eventTime')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Venue <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="venue" 
                                    value="{{ old('venue', isset($event_data) ? $event_data->venue : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter venue"
                                >
                                @error('venue')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                            <label class="col-sm-2 control-label">Venue address<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="venueAddress" 
                                    value="{{ old('venueAddress', isset($event_data) ? $event_data->venueAddress : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter venue address"
                                >
                                @error('venueAddress')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Venue phone</label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="venuePhone" 
                                    value="{{ old('venuePhone', isset($event_data) ? $event_data->venuePhone : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter venue phone no"
                                >
                                @error('venuePhone')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <h4 class="mt20">Organizer Information</h4>
                        <hr>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="organizerName" 
                                    value="{{ old('organizerName', isset($event_data) ? $event_data->organizerName : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter organizer name"
                                >
                                @error('organizerName')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                            <label class="col-sm-2 control-label">Phone</label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="organizerPhone" 
                                    value="{{ old('organizerPhone', isset($event_data) ? $event_data->organizerPhone : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter organizer phone"
                                >
                                @error('organizerPhone')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="organizerEmail" 
                                    value="{{ old('organizerEmail', isset($event_data) ? $event_data->organizerEmail : '') }}" 
                                    class="form-control mb20" 
                                    placeholder="Enter organizer email-id"
                                >
                                @error('organizerEmail')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                            <label class="col-sm-2 control-label">Website</label>
                            <div class="col-sm-3">
                                <input 
                                    type="text" 
                                    name="organizerWebsite" 
                                    value="{{ old('organizerWebsite', isset($event_data) ? $event_data->organizerWebsite : '') }}" 
                                    class="form-control mb20" 
                                    placeholder="Enter organizer website"
                                >
                                @error('organizerWebsite')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-2 control-label">Banner <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <div class="upload_container text-center">
                                    <div class="browsebtn browsebtn-edit">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i> Upload Image</span>
                                        <input 
                                            name="bannerImage" 
                                            id="bannerImage" 
                                            type="file" 
                                            accept=".jpeg,.png,.jpg,.gif,.svg"
                                        >
                                    </div>
                                    <img 
                                        class="thumbnail" 
                                        id="bannerThumbnail" 
                                        {{ isset($event_data) && $event_data->bannerImage ? "src='" . asset("uploads/event/{$event_data->bannerImage}") . "'" : '' }}
                                    ><br>
                                    <span>W 825px X H 413px</span>
                                    <label class="image_format_label">Please select (jpeg,png,jpg,gif,svg) image format</label>
                                </div>
                                @error('bannerImage')
                                    <label id="bannerImageError" class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="col-sm-2 control-label mt20">Category <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <select name="categoryId" class="form-control mt20">
                                    <option value="">Select category</option>
                                    @foreach($event_categories as $key => $value)
                                        <option value="{{ $key }}" {{ old('categoryId', isset($event_data) ? $event_data->categoryId : '') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoryId')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                            <label class="col-sm-2 control-label mt20">Business</label>
                            <div class="col-sm-3">
                                <select name="businessId" class="form-control mt20">
                                    <option value="">Select business</option>
                                    @foreach($all_business as $key => $value)
                                        <option value="{{ $key }}" {{ old('businessId', isset($event_data) ? $event_data->businessId : '') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('businessId')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Price <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <select name="price" class="form-control" id="price_selection">
                                    <option value="">Please select</option>
                                    @foreach(config('constants.event_price_option') as $key => $value)
                                        <option value="{{ $key }}" {{ old('price', isset($event_data) ? $event_data->price : '') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('price')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                            <div class="col-sm-2" id="cost_outer">
                                <input 
                                    type="text" 
                                    name="cost" 
                                    value="{{ old('cost', isset($event_data) ? $event_data->cost : '') }}" 
                                    class="form-control" 
                                    placeholder="Cost"
                                >
                                @error('cost')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Price button text</label>
                            <div class="col-sm-2">
                                <input 
                                    type="text" 
                                    name="linkText" 
                                    value="{{ old('linkText', isset($event_data) ? $event_data->linkText : '') }}" 
                                    class="form-control" 
                                    placeholder="Text for price button"
                                >
                                @error('linkText')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Ticket link</label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="bookingLink" 
                                    value="{{ old('bookingLink', isset($event_data) ? $event_data->bookingLink : '') }}" 
                                    class="form-control" 
                                    placeholder="Enter ticket booking link"
                                >
                                @error('bookingLink')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt20">
                            <div class="col-sm-9 col-sm-offset-2">
                                <button 
                                    type="button" 
                                    id="save_event" 
                                    class="btn btn-success btn-quirk btn-wide mr5" 
                                    onclick="javascript:saveEvent();"
                                >
                                    {{ !isset($event_data) ? 'Save' : 'Update' }}
                                </button>
                                <a href="{{ url('event-list') }}" class="btn btn-success btn-quirk btn-wide">Cancel</a>
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('common.cropModal')
    @extends('common.footer')

    @section('jquerysection')
        <link rel="stylesheet" href="{{ asset('css/business.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/css/fileinput.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('css/croppie.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/css/bootstrap-datepicker.css') }}">
        <script src="{{ asset('js/event.js') . '?t=' . now()->timestamp }}"></script>
        <script src="{{ asset('ui/js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('js/croppie.js') }}"></script>
    @endsection
@endsection