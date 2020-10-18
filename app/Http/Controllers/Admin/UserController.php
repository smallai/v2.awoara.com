<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected function users()
    {
        $user = Auth::user();
        if ($user->hasRole('superadmin'))
        {
            return User::with('registerDevice')->latest()->paginate();
        }
        else
        {
            return User::where('id', $user->id)->with('registerDevice')->latest()->paginate();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->users();
        return view('admin.user.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required:string|min:2|max:16',
            'email' => 'required|email',
            'phone' => [
                'required',
                'regex:/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/',
            ],
            'password' => 'required|string|min:6|max:16',
            'role' => 'required|string',
//            'devices' => [
//                'nullable',
//                'regex:/^([0-9]{1,10}[,]{0,1}){1,100}$/',
//            ],
        ], [
            'name.required' => '昵称是必须的',
            'name.string' => '昵称格式错误',
            'name.min' => '昵称长度太短',
            'name.max' => '昵称长度太长',
            'email.required' => '邮箱必须输入',
            'email.email' => '邮箱格式错误',
            'phone.required' => '手机号码必须输入',
            'phone.regex' => '手机号码格式错误',
            'password.required' => '密码必须输入',
            'password.string' => '密码格式错误',
            'password.min' => '密码长度太短',
            'password.max' => '密码长度太长',
//            'devices.nullable' => '设备列表错误',
//            'devices.regex' => '设备列表错误',
        ]);

        if (Auth::user()->hasRole('superadmin'))
        {
            $input = $request->all();
            $user = User::where([
                'email' => $input['email'],
                'phone' => $input['phone'],
            ])->first();

            debugbar()->debug(['user1' => $user]);
            if (is_null($user))
            {
                $user = User::where([
                    'phone' => $input['phone']
                ])->first();
            }

            debugbar()->debug(['user2' => $user]);
            if (is_null($user))
            {
                $user = User::where([
                    'email' => $input['email']
                ])->first();
            }

            debugbar()->debug(['user3' => $user]);
            if (is_null($user))
            {
                $user = new User();
            }
            $user->name = $input['name'];
            $user->email = $input['email'];
            $user->phone = $input['phone'];
            $user->password = bcrypt($input['password']);
            $user->saveOrFail();

            try {
                Role::findByName($input['role']);
                $user->syncRoles($input['role']);
                flash('保存成功')->success();
            }
            catch (RoleDoesNotExist $exception)
            {
                flash('错误！角色不存在！')->error();
            }

//            if (strcmp($input['devices'], "") != 0)
//            {
//                $devices = explode(',', $input['devices']);
//                debugbar()->debug($devices);
//                if (count($devices) > 0)
//                {
//                    Device::whereIn('id', $devices)->update([
//                        'owner_id' => $user->id,
//                    ]);
//                }
//            }
        }
        else
        {
            flash('保存失败')->error();
        }

//        return 'hello';
        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->checkPermission($id);
        $item = User::withTrashed('loginLogs')->findOrFail($id);
        return view('admin.user.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkPermission($id);
        $item = User::findOrFail($id);
        return view('admin.user.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required:string|min:2|max:16',
//            'password' => 'required|string|min:6|max:16',
        ], [
            'name.required' => '昵称是必须的',
            'name.string' => '昵称格式错误',
            'name.min' => '昵称长度太短',
            'name.max' => '昵称长度太长',
//            'password.required' => '密码必须输入',
//            'password.string' => '密码格式错误',
//            'password.min' => '密码长度太短',
//            'password.max' => '密码长度太长',
        ]);

        $this->checkPermission($id);
        $item = User::findOrFail($id);
        $input = $request->only('name', 'password', 'payee', 'real_name');
        $item->name = $input['name'];
//        $item->password = bcrypt($input['password']);
        $item->payee = $input['payee'];
        $item->real_name = $input['real_name'];
        $item->save();
        \flash('修改成功')->success();
        return  redirect()->route('user.show', $item->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->hasRole('superadmin'))
        {
            $item = User::findOrFail($id);
            $item->delete();
            flash()->success('删除成功！');
            return redirect()->route('user.index');
        }
        else
        {
            die(403);
        }
    }

    public function checkPermission($id)
    {
        $user = Auth::user();
        if (!$user->hasRole('superadmin'))
        {
            if ($user->id != $id) {
                abort(403);
            }
        }
    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function dataTableIndex()
    {
        return view('admin.user.dataTable');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataTableData()
    {
        $query = User::query();
        return DataTables::of($query)->make(true);
    }
}
