<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        // $this->call(UsersTableSeeder::class);
//        $device = factory(App\Models\Device::class)->make();
////        var_dump($device);
//        $device->save();

//       $item = factory(\App\Models\Goods::class)->make();
//       $item->save();
//
//        $item = factory()->make(\App\Models\Trade::class);

        $user = factory(User::class)->make();
        $user->name = 'Test';
        $user->payee = 'ovhtuy5698@sandbox.com';
        $user->real_name = '沙箱环境';
        $user->email = 'admin@test.com';
        $user->phone = '18571559495';
        $user->password = bcrypt('ahj321');
        $user->save();

        factory(\App\Models\Device::class)->create();
        factory(\App\Models\Goods::class)->create();

//        factory(\App\UserProfile::class)->create();

//        $item = factory(\App\Models\Trade::class)->make();
//        $item->save();
//
//        $item = null;
//
//        for ($i=0; $i<10; $i++)
//        {
//            factory(\App\Models\LogUserLogin::class)->make()->save();
//        }
//
//        for ($i=0; $i<10; $i++)
//        {
//            factory(\App\Models\WithdrawMoney::class)->make()->save();
//        }
//
//        for ($i=0; $i<10; $i++)
//        {
//            factory(\App\Models\Trade::class)->make()->save();
//        }
//
//        for ($i=0; $i<3; $i++)
//        {
//            factory(\App\Models\Goods::class)->make()->save();
//        }
//
//        for ($i=0; $i<10; $i++)
//        {
//            factory(\App\WashCar::class)->make()->save();
//        }

        app()['cache']->forget('spatie.permission.cache');

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

        $user->syncRoles(['superadmin']);
    }


    public function permissions()
    {
        \Spatie\Permission\Models\Permission::create([
            'guard_name' => 'web',
            'name' => 'device.view',
        ]);
    }
}
