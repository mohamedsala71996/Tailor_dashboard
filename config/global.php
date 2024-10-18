<?php

use App\Models\Setting;

return [
    'roles' => [
        'المستخدمين' => ['قراءة', 'اضافة', 'تعديل', 'حذف'],
        'المتاجر' => ['قراءة', 'اضافة', 'تعديل', 'حذف','تبديل'],
        'الادوار' =>  ['قراءة', 'تعديل', 'حذف'],
        'المنتجات' =>  ['قراءة', 'اضافة', 'تعديل', 'حذف'],
        'شيت الاكسل' =>  ['اضافة'],
        'الدفعات' => ['تعديل الكمية المطلوبة','تعديل الخياط', 'تعديل المشرف', 'الكمية المتبقية','انشاء جدول صادر'],
    ],
    // 'roles' => [
    //     'users' => ['read', 'create', 'update', 'delete'],
    //     'roles' => ['read', 'create', 'update', 'delete'],
    //     'products' => ['read', 'create', 'update', 'delete'],
    //     'orders' => ['read', 'create', 'update', 'delete'],
    //     'masterOrders' => ['read', 'edit_tailor', 'edit_supervisor', 'end_master_order'],
    // ],
]
?>
