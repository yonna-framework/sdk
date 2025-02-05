<?php
/**
 * 类名：AlipayNotify
 * 功能：支付宝通知处理类
 * 详细：处理支付宝各接口通知返回
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考

 *************************注意*************************
 * 调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
 */
namespace Yonna\Plugins\Alipay\WapV1;

class AlipayNotify extends AbstractLib{

	/**
	 * HTTPS形式消息验证地址
	 */
	private $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
	 * HTTP形式消息验证地址
	 */
	private $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	private $alipay_config;

	public function __construct($alipay_config = null){
		$alipay_config && $this->alipay_config = $alipay_config;
	}
	public function setConfig($alipay_config){
		$this->alipay_config = $alipay_config;
	}
	public function AlipayNotify($alipay_config) {
		$this->__construct($alipay_config);
	}

    /**
     * 针对 return_url / notify_url 验证消息是否是支付宝发出的合法消息
     * @param $data
     * @return boolean 验证结果
     */
    public function verifyNotify($data)
    {
        if(empty($data)) { // 判断数组是否为空
            return false;
        }
        //生成签名结果
        $isSign = $this->getSignVeryfy($data, $data["sign"]);
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'false';
        if (!empty($data["notify_id"])) {
            $responseTxt = $this->getResponse($data["notify_id"]);
        }
        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if (preg_match("/true$/i", $responseTxt) && $isSign) {
            return true;
        } else {
            return false;
        }
    }

	/**
	 * 获取返回时的签名验证结果
	 * @param $para_temp 通知返回来的参数数组
	 * @param $sign 返回的签名结果
	 * @return 签名验证结果
	 */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->getCore()->paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = $this->getCore()->argSort($para_filter);

		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->getCore()->createLinkstring($para_sort);

		$isSign = false;
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "RSA" :
				$isSign = $this->getRsa()->rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			default :
				$isSign = false;
		}

		return $isSign;
	}

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param string $notify_id 通知校验ID
     * @return string 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    public function getResponse($notify_id)
    {
        $transport = strtolower(trim($this->alipay_config['transport']));
        $partner = trim($this->alipay_config['partner']);
        if ($transport == 'https') {
            $veryfy_url = $this->https_verify_url;
        } else {
            $veryfy_url = $this->http_verify_url;
        }
        $veryfy_url = $veryfy_url . "partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->getCore()->getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
        return $responseTxt;
    }

}
?>