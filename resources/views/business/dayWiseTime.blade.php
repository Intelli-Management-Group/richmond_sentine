<div class="day_outer mb20">
    <div class="form-group">
        <label for="{{ $key }}" class="col-sm-2 control-label text-left">{{ ucfirst($value) }}</label>
        <div class="col-sm-2">
            <label class="switch mr-2">
                <input type="checkbox" 
                       name="isOpen[{{ $key }}]" 
                       value="1" 
                       class="change_time"
                       data-day="{{ $key }}"
                       {{ (isset($hours) && count($hours) > 0) ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
            <span class="opening_label">
                {{ (isset($hours) && count($hours) > 0) ? 'Open' : 'Closed' }}
            </span>
        </div>
    </div>
    
    @isset($hours)
        @foreach(count($hours) > 0 ? $hours : [] as $time)
            @include('business.timeSelection', ['time' => $time, 'set_day' => $key])
        @endforeach
    @endisset
</div>