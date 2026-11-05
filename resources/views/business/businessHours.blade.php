<div>
    @include("business.applyAll")
    @if(isset($business_data))
        @foreach($business_data->businessHours as $day => $hours)
            @include('business.dayWiseTime',["key" => $day,"value" => $day_array[$day],"hours" => $hours])
        @endforeach
    @else
        @foreach($day_array as $key => $value)
            @include('business.dayWiseTime',["key" => $key,"value" => $value])
        @endforeach
    @endif
    <div class="form-group">
        <div class="col-sm-offset-2">
            <label class="error" for="businesstime"></label>
        </div>
    </div>
</div>
