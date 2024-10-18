<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create Admin role with all permissions
        $adminRole = Role::create([
            'id' => 1,
            'name' => 'الادمن',
            'description' => 'كل الصلاحيات'
        ]);

        // Admin permissions
        $adminPermissions = [
            'المستخدمين' => ['قراءة', 'اضافة', 'تعديل', 'حذف'],
            'المتاجر' => ['قراءة', 'اضافة', 'تعديل', 'حذف','تبديل'],
            'الادوار' =>  ['قراءة', 'تعديل', 'حذف'],
            'المنتجات' =>  ['قراءة', 'اضافة', 'تعديل', 'حذف'],
            'شيت الاكسل' =>  ['اضافة'],
            'الدفعات' => ['تعديل الكمية المطلوبة','تعديل الخياط', 'تعديل المشرف', 'الكمية المتبقية','انشاء جدول صادر'],
            ];

        foreach ($adminPermissions as $key => $values) {
            foreach ($values as $value) {
                Permission::firstOrCreate(['name' => $value . '-' . $key]);
            }
        }

        $adminRole->permissions()->attach(Permission::all());

        // Create Supervisor role with specific permissions
        $supervisorRole = Role::create([
            'id' => 2,
            'name' => 'المشرف',
            'description' => 'بعض الصلاحيات'
        ]);

        $supervisorPermissions = [
            'المنتجات' => ['قراءة', 'اضافة', 'تعديل', 'حذف'],
            'شيت الاكسل' =>  ['اضافة'],
            'الدفعات' => ['تعديل الكمية المطلوبة', 'تعديل المشرف','انشاء جدول صادر'],
            'المستخدمين' => ['قراءة'],
        ];

        foreach ($supervisorPermissions as $key => $values) {
            foreach ($values as $value) {
                $permission = Permission::firstOrCreate(['name' => $value . '-' . $key]);
                $supervisorRole->permissions()->attach($permission);
            }
        }

        // Create Tailor role with specific permissions
        $tailorRole = Role::create([
            'id' => 3,
            'name' => 'الخياط',
            'description' => 'قراءة المنتجات والطلبيات وتعديل الكميات المرسلة'
        ]);

        $tailorPermissions = [
            'المنتجات' => ['قراءة'],
            'الدفعات' => ['تعديل الخياط','انشاء جدول صادر'],
        ];

        foreach ($tailorPermissions as $key => $values) {
            foreach ($values as $value) {
                $permission = Permission::firstOrCreate(['name' => $value . '-' . $key]);
                $tailorRole->permissions()->attach($permission);
            }
        }
    }
}
