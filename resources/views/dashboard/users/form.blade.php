<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'attribute' => 'required',
                'name' => "username",
                'value' => isset($data) ? $data->username : old('username') ,
                'label' => trans('admin.Username'),
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.Name'),
                'value' => isset($data) ? $data->name : old('name') ,
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.input', [
                'type' => 'password',
                'class' => 'form-control',
                'name' => "password",
                'label' => trans('admin.Password'),
                'value' => old('password'),
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $roles,
                'index' => 'id',
                'select' => isset($data) ? $data->getRoleId() : old('role_id'),
                'name' => 'role_id',
                'label' => trans('admin.Roles'),
                'class' => 'form-control select2',
                'firstDisabled' => true,
                'attribute' => 'required',
            ])
        </div>

        {{-- <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $stores, // Pass the collection of stores
                'index' => 'id',
                'select' => isset($data) ? $data->store_id : old('store_id'), // Get store_id from data or old input
                'name' => 'store_id',
                'label' =>'المتجر', // Add the appropriate label
                'class' => 'form-control select2',
                'firstDisabled' => true,
                'attribute' => 'required', // Make it required if needed
            ])
        </div> --}}
        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $stores,
                'index' => 'id',
                'select' => isset($data) ? $data->stores->pluck('id')->toArray() : old('store_id'), // Get all stores related to user
                'name' => 'store_id[]', // Notice the array format 'store_id[]'
                'label' => 'المتجر',
                'class' => 'form-control select2',
                'id' => '',
                'display' => 'name',
                'firstDisabled' => true,
                'attribute' => 'multiple', // Allow selecting multiple stores
            ])
        </div>

    </div>
</div>
