<?php
//
//namespace App\Http\Controllers\Test;
//
//use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
//
//require(__DIR__."/../../../Packages/Alipay/AopSdk.php");
//
//class AlipayUserInfoShareController extends Controller
//{
//    public function index()
//    {
//        if (config('alipay.sandbox'))
//            $url = "https://openauth.alipaydev.com";
//        else
//            $url = "https://openauth.alipay.com";
//        $url .= "/oauth2/publicAppAuthorize.htm";
//        $url .= "?app_id=" . config('alipay.app_id');
//        $url .= "&scope=user_base";
//        $url .= "&redirect_uri=" . urlencode(config('alipay.redirect_url'));
////        return $url;
////        return redirect($url);
//        return view('test.login', compact('url'));
//    }
//
//    public function hello()
//    {
//        $accessToken = '';
//        $aop = new \AopClient();
//        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
//        $aop->appId = config('alipay.app_id');
//        $aop->rsaPrivateKey = config('alipay.private_key');
//        $aop->alipayrsaPublicKey = config('alipay.ali_public_key');
//        $aop->apiVersion = '1.0';
//        $aop->signType = 'RSA2';
//        $aop->postCharset = 'GBK';
//        $aop->format = 'json';
//        $request = new \AlipayUserInfoShareRequest();
//        $result = $aop->execute($request, $accessToken);
//
//        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
//        $resultCode = $result->$responseNode->code;
//        if (!empty($resultCode) && $resultCode == 10000) {
//            echo "成功";
//        }
//    }
//}
