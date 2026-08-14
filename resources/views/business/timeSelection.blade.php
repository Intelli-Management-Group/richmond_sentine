<div class="form-group {{ isset($time) ? '' : 'hidden' }} time_selection"
     @if(!isset($time)) id="time_selection" @endif>
    <div class="col-sm-3 col-sm-offset-2">
        <div class="input-group clockpicker">
            <input type="text" 
                   name="{{ isset($time) ? "starttime[$set_day][]" : '' }}"
                   value="{{ isset($time) ? $time->start_time : '' }}"
                   class="form-control start_input_time">
            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
        </div>
    </div>
    <div class="col-sm-1 text-center"> _</div>
    <div class="col-sm-3">
        <div class="input-group clockpicker">
            <input type="text" 
                   name="{{ isset($time) ? "endtime[$set_day][]" : '' }}"
                   value="{{ isset($time) ? $time->end_time : '' }}"
                   class="form-control end_input_time">
            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
        </div>
    </div>
    <div class="col-sm-3 text-center">
        <a href="javascript:void(0)" 
           class="text-underline" 
           @isset($time) data-day="{{ $set_day }}" @endisset 
           onclick="javascript:addTimeselectionDiv(this)">Add hours</a>
        <a href="javascript:void(0)" 
           class="text-underline text-danger ml10" 
           onclick="javascript:removeTime(this)">Remove</a>
    </div>
</div>