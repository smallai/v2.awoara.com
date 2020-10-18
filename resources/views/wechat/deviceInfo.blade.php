@if (isset($device))
    <div class="weui-panel weui-panel_access">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_appmsg">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box_thumb" src="{{ asset('logo.png?t=20190428091303') }}" alt="logo.png"  style="width: 64px"/>
                </div>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title">
                        <div class="weui-flex">
                            <div class="weui-flex__item">编号: <span>{{ $device->id }}</span></div>
                        </div>
                    </h4>
                    <h4 class="weui-media-box__title">
                        <div class="weui-flex">
                            <div class="weui-flex__item">网点: <span>{{ $device->name }}</span></div>
                        </div>
                    </h4>
                    <h4 class="weui-media-box__title">
                        <div class="weui-flex">
                            <div class="weui-flex__item"><a href="/tutorial">查看设备使用教程</a></span></div>
                        </div>
                    </h4>
                </div>
            </div>
        </div>
    </div>
@endif
