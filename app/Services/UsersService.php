<?php

namespace App\Services;

use App\Models\Image;
use App\Models\User;
use App\Traits\Upload;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    use Upload;
    public function insert($request){
        $role = '';
        if ($request->role_id == 1) {
            $role = 'admin';
            $store_id=$request->store_id[0] ?? null;
        } elseif ($request->role_id == 2) {
            $role = 'supervisor';
            $store_id=$request->store_id[0] ?? null;

        } elseif ($request->role_id == 3) {
            $role = 'tailor';
            if (is_null($request->store_id)) {
                return ['error' => 'خانة المتجر مطلوبة']; // Return error message instead of throwing exception
            }
            $store_id=$request->store_id[0];


        }
        $user = User::create([
            'username'      => $request->username,
            'name'          => $request->name,
            'store_id'          =>  $store_id,
            'role'          => $role,
            'password'      => Hash::make($request->password),
        ]);
        if ( $role =='tailor') {
            $user->stores()->attach($request->store_id);
        }
        // $user->stores()->attach($request->store_id);
        $user->roles()->attach([$request->role_id]);

        return $user;
    }

    public function update($user, $request){
        if($request->password == NULL){
            $password = $user->password;
        } else{
            $password = Hash::make($request->password);
        }
        $role = '';
        if ($request->role_id == 1) {
            $role = 'admin';
            $store_id=$request->store_id[0] ?? null;
        } elseif ($request->role_id == 2) {
            $role = 'supervisor';
            $store_id=$request->store_id[0] ?? null;

        } elseif ($request->role_id == 3) {
            $role = 'tailor';
            if (is_null($request->store_id) && is_null($user->store_id) ) {
                return ['error' => 'خانة المتجر مطلوبة']; // Return error message instead of throwing exception
            }
            $store_id=$request->store_id[0] ?? $user->store_id ;
        }
        $user->username       = $request->username;
        $user->name           = $request->name;
        $user->store_id           = $store_id;
        $user->password       = $password;
        $user->role       = $role;
        $user->save();

        if($request->role_id){
            $user->roles()->detach([$user->getRoleId()]);
            $user->roles()->attach([$request->role_id]);
        }
        if ($request->has('store_id')) {
            $user->stores()->sync($request->store_id); // Syncing stores from the pivot table
        }
    }

    public function update_user_image($user,$image){
        $path = $this->uploadImage($image, 'uploads/users', 660);

        if($user->Image == null){
            //if user don't have image
            Image::create([
                'imageable_id'   => $user->id,
                'imageable_type' => 'App\Models\User',
                'src'            => $path,
            ]);

        } else {
            //ig user have image
            $oldImage = $user->Image->src;

            if(file_exists(base_path('public/uploads/users/') . $oldImage))
                unlink(base_path('public/uploads/users/') . $oldImage);

            $user->Image->src = $path;
            $user->Image->save();
        }
    }
}
