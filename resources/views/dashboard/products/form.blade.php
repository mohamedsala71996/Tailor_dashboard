<div class="card-body">
    <div class="row">

        <div class="col-12">
            <div class="form-group">
                <label for="photos">رفع الصور</label>
                <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
            </div>
            @error('photos')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            @error('photos.*')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div>
