@extends('layouts.alipay')

@section('content')

    <div class="weui-msg">
        <div class="weui-msg__text-area">
			<div class="weui-cell">
				如果机器有清水/泡沫切换按钮，请使用按钮切换。否则，请按照下面的示意图操作。
			</div>
            <p class="weui-msg__desc">
            <ul>
                <div class="weui-cells">
                    <div class="weui-cells__title">清水泡沫切换示意图</div>
                    
					<div class="weui-cell">
						松开扳机，翻转切换清水和泡沫。
					</div>
					<div class="weui-cell">
						不要旋转水枪喷头！！！
					</div>
                    <div class="weui-cell">
						<img src="/water_switch.gif" alt="清水泡沫切换示意图" width="100%">
                    </div>
                </div>
            </ul>
        </div>
    </div>
    </div>

@endsection
