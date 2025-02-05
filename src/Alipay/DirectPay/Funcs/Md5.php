<?php
/* *
 * MD5
 * 详细：MD5加密
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
namespace Yonna\Plugins\Alipay\DirectPay\Funcs;


class Md5{

	/**
	 * 签名字符串
	 * @param string $prestr
	 * @param string $key
	 * @return string 签名结果
	 */
	public function md5Sign($prestr, $key) {
		$prestr = $prestr . $key;
		return md5($prestr);
	}

	/**
	 * 验证签名
	 * @param string $prestr 需要签名的字符串
	 * @param string $sign 签名结果
	 * @param string $key 私钥
	 * @return bool
	 */
	public function md5Verify($prestr, $sign, $key) {
		$prestr = $prestr . $key;
		$mysgin = md5($prestr);

		if($mysgin == $sign) {
			return true;
		}
		else {
			return false;
		}
	}

}
?>