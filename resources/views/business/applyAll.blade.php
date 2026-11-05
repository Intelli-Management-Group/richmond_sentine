<div class="mb20">
    <div class="mb10">
        <input type="checkbox" class="checkbox-inline" id="apply_to_all_days"> <strong>Apply to all days</strong>
    </div>
    <div id="apply_to_all_days_outer" class="hide">
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-2">
                <label class="switch mr-2">
                    <input type="checkbox" name="isOpenAll" id="isOpenAll" value="1" {{ old('isOpenAll') ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <span class="opening_label">Closed</span>
            </div>
        </div>
        <div class="form-group hide" id="timepicker_container">
            <div class="col-sm-3 col-sm-offset-2">
                <div class="input-group clockpicker">
                    <input type="text" name="startTimeAll" id="startTimeAll" class="form-control" value="{{ old('startTimeAll') }}">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                </div>
            </div>
            <div class="col-sm-1 text-center"> _</div>
            <div class="col-sm-3">
                <div class="input-group clockpicker">
                    <input type="text" name="endTimeAll" id="endTimeAll" class="form-control" value="{{ old('endTimeAll') }}">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                </div>
            </div>
        </div>
        <div class="form-group" id="hour_error">
            <div class="col-sm-offset-2 col-sm-4">
                <label class="error">Please select valid hours</label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-2 text-left">
                <a href="javascript:void(0)" class="text-underline" onclick="javascript:applyTimeToAll()">Apply</a>
            </div>
        </div>
    </div>
</div>
<hr/>