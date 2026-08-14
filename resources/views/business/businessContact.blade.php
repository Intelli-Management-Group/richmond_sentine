<div class="form-group">
    <label for="address1" class="col-sm-3 control-label">Address line 1<span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="address1" id="address1" class="form-control" placeholder="Enter address" value="{{ old('address1') }}">
        <label class="error" for="address1">
            @error('address1')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="address2" class="col-sm-3 control-label">Address line 2</label>
    <div class="col-sm-6">
        <input type="text" name="address2" id="address2" class="form-control" value="{{ old('address2') }}">
        <label class="error" for="address2">
            @error('address2')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="city" class="col-sm-3 control-label">City<span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="city" id="city" class="form-control" placeholder="Enter city" value="{{ old('city') }}">
        <label class="error" for="city">
            @error('city')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="province" class="col-sm-3 control-label">Province<span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="province" id="province" class="form-control" placeholder="Enter province" 
               value="{{ old('province', isset($business_data) ? $business_data->province : 'British Columbia') }}">
        <label class="error" for="province">
            @error('province')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="postalCode" class="col-sm-3 control-label">Postal code<span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="postalCode" id="postalCode" class="form-control" placeholder="Enter postal code" 
               value="{{ old('postalCode') }}">
        <label class="error" for="postalCode">
            @error('postalCode')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="country" class="col-sm-3 control-label">Country<span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="country" id="country" class="form-control" placeholder="Enter country" 
               value="{{ old('country', isset($business_data) ? $business_data->country : 'Canada') }}">
        <label class="error" for="country">
            @error('country')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="bemail" class="col-sm-3 control-label">Email</label>
    <div class="col-sm-6">
        <input type="text" name="bemail" id="bemail" class="form-control" placeholder="Enter email Id" 
               value="{{ old('bemail') }}" disabled>
    </div>
</div>

<div class="form-group">
    <label for="website" class="col-sm-3 control-label">Website</label>
    <div class="col-sm-6">
        <input type="text" name="website" id="website" class="form-control" placeholder="Enter website" 
               value="{{ old('website') }}">
        <label class="error" for="website">
            @error('website')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="phone" class="col-sm-3 control-label">Phone</label>
    <div class="col-sm-6">
        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone" 
               value="{{ old('phone') }}">
        <label class="error" for="phone">
            @error('phone')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>