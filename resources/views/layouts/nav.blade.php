
<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#example-navbar-collapse">
                <span class="sr-only">切换导航</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            {{--<a class="navbar-brand" href="{{ route('dashboard.index')  }}">石斑鱼</a>--}}
            <a href="{{ route('dashboard.index') }}" class="navbar-left">
                <img src="{{ asset('awoara-logo.jpg') }}" alt="logo" style="width: 120px; height: 30px; ">
            </a>
        </div>

        <div class="collapse navbar-collapse" id="example-navbar-collapse">
            <ul class="nav navbar-nav">

                {{--用户--}}
                @role('superadmin')
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        用户管理
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('user.create') }}">创建用户</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('user.index') }}">浏览用户</a></li>
                    </ul>
                </li>
                @endrole

                @if (true)
                {{--设备--}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        设备管理
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        @role('superadmin')
                        <li><a href="{{ route('device.create') }}">创建设备</a></li>
                        @endrole
                        {{--<li class="divider"></li>--}}
                        <li><a href="{{ route('device.index') }}">浏览设备</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('device.refresh_state') }}">刷新状态</a></li>
                    </ul>
                </li>

                {{--套餐--}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        套餐管理
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('goods.create') }}">创建套餐</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('goods.index') }}">浏览套餐</a></li>
                    </ul>
                </li>

                {{--支付记录--}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        支付记录
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('trade.index') }}">所有记录</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('trade.index', ['type' => App\Models\Trade::PaymentType_WeChat]) }}">微信</a></li>
                        <li><a href="{{ route('trade.index', ['type' => App\Models\Trade::PaymentType_Alipay]) }}">支付宝</a></li>
                        <li><a href="{{ route('trade.index', ['type' => App\Models\Trade::PaymentType_VipCard]) }}">会员卡</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        提现记录
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('withdraw.index') }}">所有记录</a></li>
                    </ul>
                </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            会员管理
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('card.index') }}">会员卡</a></li>
                        </ul>
                    </li>

                {{--报表--}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        运营中心
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('center.index') }}">按月浏览</a></li>
                    </ul>
                </li>

                {{--个人中心--}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        个人中心
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        @role('superadmin')
                        <li><a class="dropdown-item" href="{{ url('log-viewer') }}">系统日志</a></li>
                        <li><a class="dropdown-item" href="{{ route('log.user.login', optional(\Illuminate\Support\Facades\Auth::user())->id) }}">登录记录</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.show', optional(\Illuminate\Support\Facades\Auth::user())->id) }}">账号详情</a></li>
                        @endrole

                        <li><a class="dropdown-item" href="{{ route('user.reset_password', optional(\Illuminate\Support\Facades\Auth::user())->id) }}">重置密码</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.edit', optional(\Illuminate\Support\Facades\Auth::user())->id) }}">个人配置</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">安全退出</a></li>

                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </ul>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
