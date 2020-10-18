<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');
//
        $item = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'superadmin',
        ]);
        $item['display_name'] = '超级管理员';
        $item->save();

        $item = Role::firstOrCreate([
            'guard_name' => 'web',
            'name' => 'admin',
        ]);
        $item['display_name'] = '管理员';
        $item->save();

////
//        $item = Permission::firstOrCreate([
//            'guard_name' => 'web',
//            'name' => 'device.all',
//        ]);
//        $item['display_name'] = '所有权限';
//        $item->save();
//
////
//        $item = Permission::firstOrCreate([
//            'guard_name' => 'web',
//            'name' => 'device.update',
//        ]);
//        $item['display_name'] = '更新权限';
//        $item->save();
//
////
//        $item = Permission::firstOrCreate([
//            'guard_name' => 'web',
//            'name' => 'user.all',
//        ]);
//        $item['display_name'] = '所有权限';
//        $item->save();
//
////
//        $item = Permission::firstOrCreate([
//            'guard_name' => 'web',
//            'name' => 'goods.update',
//        ]);
//        $item['display_name'] = '更新权限';
//        $item->save();
//
//        //
//        $item = Permission::firstOrCreate([
//            'guard_name' => 'web',
//            'name' => 'goods.all',
//        ]);
//        $item['display_name'] = '所有权限';
//        $item->save();

////
//        $item = Permission::firstOrCreate([
//            'guard_name' => 'web',
//            'name' => 'user.update',
//        ]);
//        $item['display_name'] = '更新权限';
//        $item->save();
//
////        给所有的超级管理员授权
//        $superAdmins = \App\Models\User::withTrashed()->role('superadmin')->get();
//        foreach ($superAdmins as $item) {
//            $item->syncPermissions([
//                'device.all', 'device.update', 'user.all', 'user.update', 'goods.all', 'goods.update'
//            ]);
//        }
//
////        给所有的管理员授权
//        $admins = \App\Models\User::withTrashed()->role('admin')->get();
//        foreach ($admins as $item) {
//            $item->syncPermissions([
//                'device.update', 'user.update', 'goods.update'
//            ]);
//        }

//
        $root = \App\Models\User::where('phone', '18571559495')->first();
        $root->syncRoles('superadmin');

        $admin = \App\Models\User::where('phone', '18971204632')->first();
        $admin->syncRoles('admin');
    }
}
