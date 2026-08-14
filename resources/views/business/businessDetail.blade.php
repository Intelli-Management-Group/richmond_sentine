<div class="form-group">
    <label for="name" class="col-sm-3 control-label">Business name <span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="name" id="business_name" class="form-control" 
               placeholder="Enter business name" value="{{ old('name') }}">
        <label class="error" for="name">
            @error('name')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="shortCode" class="col-sm-3 control-label text-left">User name <span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" name="shortCode" id="shortCode" class="form-control" 
               placeholder="Enter business user name" value="{{ old('shortCode') }}">
        <label class="image_format_label">It will display in browser url for business details, must be unique</label>
        <label class="error" for="shortCode">
            @error('shortCode')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="categoryId" class="col-sm-3 control-label">Business category <span class="text-danger">*</span></label>
    <div class="col-sm-4">
        <select class="form-control" name="business_category" id="business_category">
            <option value="">--Select category--</option>
            @foreach($categories as $key => $value)
                <option value="{{ $key }}" {{ old('business_category') == $key ? 'selected' : '' }}>
                    {{ $value['name'] }}
                </option>
            @endforeach
            <option value="other" {{ old('business_category') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
        
        <select class="form-control subcategory hidden mt10" name="categoryId">
            <option value="">--Select category--</option>
        </select>
        
        <select class="form-control hidden mt10" id="subcategoryClone">
            <option value="">--Select category--</option>
            @foreach($categories as $key => $value)
                <optgroup id="subCat-{{ $key }}">
                    @foreach(count($value['child_list']) > 0 ? $value['child_list'] : [] as $ckey => $cvalue)
                        <option value="{{ $ckey }}" data-parent="{{ $key }}"
                                {{ old('categoryId') == $ckey ? 'selected' : '' }}>
                            {{ $cvalue['name'] }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        
        <input type="text" name="otherCategoryName" id="otherCategoryName" 
               class="form-control hidden mt10" placeholder="Enter category name" 
               value="{{ old('otherCategoryName') }}">
        
        <label class="error" for="categoryId">
            @error('categoryId')
                {{ $message }}
            @enderror
        </label>
        <label class="error" for="otherCategoryName">
            @error('otherCategoryName')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <label for="about" class="col-sm-3 control-label">About <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <textarea name="about" id="business_description" class="form-control" 
                  placeholder="Enter description" cols="4" rows="3">{{ old('about') }}</textarea>
        <label class="error" for="about">
            @error('about')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>