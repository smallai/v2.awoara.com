<?php

namespace App\Http\Controllers\Wap;

use App\Models\PhoneVerificationCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('wap.login');
    }

    public function login(Request $request)
    {
        //检查输入是否正确
        $this->validateRequest($request);

        //检查验证码是否正确
        $verificationCode = $this->checkVerificationCode($request);
        if (isset($verificationCode))
        {
            $user = $this->findOrNewUser($request);
            return $this->redirectUser($user);
        }
        else
        {
            flash()->error('手机号码或验证码错误');
            return redirect()->route('wap.showLoginForm');
        }
    }

    protected function validateRequest(Request $request)
    {
        $this->validate($request, [
            'phone' => [
                'required',
                'regex:/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/',
            ],
            'code' => [
                'required',
                'regex:/^[0-9]{6}$/'
            ],
        ], [
            'phone.required' => '手机号码必须输入',
            'phone.regex' => '手机号码格式错误',
            'code.required' => '验证码必须输入',
            'code.regex' => '验证码格式错误',
        ]);
    }

    protected function checkVerificationCode(Request $request)
    {
        $verificationCode = PhoneVerificationCode::where('phone', $request->input('phone'))->latest()->first();
        if (isset($verificationCode) && ($verificationCode->code === $request->input('code')))
        {
            return $verificationCode;
        }

        return null;
    }

    protected function findOrNewUser(Request $request)
    {
        $user = User::withTrashed()->where('phone', $request->input('phone'))->first();
        if (!isset($user))
        {
            $password = $request->input('password') ?? Str::orderedUuid()->toString();
            $user = new User([
                'phone' => $request,
                'password' => bcrypt($password),
            ]);
        }
        return $user;
    }

    protected function redirectUser($user)
    {
        if (isset($user->updated_at))   //是老用户
        {
            debugbar()->debug('old user');
            flash()->success('欢迎您再来哟！');
        }
        else if (isset($user->deleted_at))  //是已删除的用户那么恢复用户信息
        {
            $user->restore();
            debugbar()->debug('old user');
            flash()->success('注册成功！');
        }
        else //新注册的用户
        {
            $user->register_device_id = Session::get('device_id');
            $user->name = $user->phone;
            debugbar()->debug('new user');
            flash()->success('注册成功！');
        }
        $user->api_token = Str::uuid()->toString();
        $user->save();

        Auth::loginUsingId($user->id);

        $goods = Session::get('goods');
        if (isset($goods))
        {
            return redirect()->route('payment.index', [
                'id' => Session::get('device_id')
            ]);
        }
        else
        {
            $vipCard = UserVipCard::where('user_id', Auth::user()->id)
                ->where('expiration', '<', Carbon::now())
                ->whereColumn('used_count', '<', 'total_count')->first();
            if (isset($vipCard))
            {

            }
            else
            {

            }
        }
        return 'aaaa'.json_encode($user);
    }

    //发送验证码
    public function sendCode(Request $request)
    {
        $phoneCode = PhoneVerificationCode::findOrNew([
            'phone' => $request->input('phone')
        ]);
        $phoneCode->code = str_random(6);
        $phoneCode->expiration = Carbon::now()->addMinutes(5);
        $phoneCode->saveOrFail();
    }

    //使用手机验证码登录
    public function loginByPhone(Request $request)
    {
        $input = $request->input(['phone', 'code']);
        $phoneCode = PhoneVerificationCode::findOrFail([
            'phone' => $request->input('phone')
        ]);
        if ($phoneCode->code == $input['code'])
        {
            $this->validate($request, [
                'phone' => [
                    'required',
                    'regex:/^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/',
                ],
            ], [
                'phone.required' => '手机号码必须输入',
                'phone.regex' => '手机号码格式错误',
            ]);

            $user = User::findOrNew([
                'phone' => $request->input('phone'),
            ]);
            $user->password = bcrypt(Str::uuid()->toString());
            $user->saveOrFail();
            Auth::loginUsingId($user->id);
        }
    }
}
