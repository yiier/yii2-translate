<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/7/21 1:25 PM
 * description:
 */

namespace yiier\translate\platforms;

use yiier\translate\exceptions\TranslateException;

/**
 * QQ 翻译君
 * https://ai.qq.com/doc/nlptrans.shtml
 * Class QqtranslationPlatform
 * @package yiier\translate\platforms
 */
class QqtranslationPlatform extends Platform
{
    const ENDPOINT = 'https://api.ai.qq.com/fcgi-bin/nlp/nlp_texttranslate';

    /**
     * @var string
     */
    protected $endpoint;

    protected static $language = [
        'auto' => 'auto', // 中英互译
        'zh' => 'zh',   //中文
        'en' => 'en',   //英文
        'ja' => 'jp',   //日文
        'ko' => 'kr',  //韩文
        'fr' => 'fr',  //法文
        'ru' => 'ru',   //俄文
        'es' => 'es',  //西班牙文
        'it' => 'it',  // 意大利文
        'de' => 'de',  // 德文
        'tr' => 'tr',  // 土耳其文
        'pt' => 'pt',  //葡萄牙文
        'th' => 'th',  // 泰文
    ];

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'timeout' => method_exists($this, 'getTimeout') ? $this->getTimeout() : 5.0,
        ]);

        $this->endpoint = $this->config->get('endpoint') ?: self::ENDPOINT;

        return $client;
    }


    public function translate(string $string, string $from, string $to)
    {
        $params = [
            'app_id' => $this->config->get('app_id'),
            'source' => self::dataGet(self::$language, $from, $from),
            'target' => self::dataGet(self::$language, $to, $to),
            'text' => $string,
            'time_stamp' => strval(time()),
            'nonce_str' => strval(rand()),
            'sign' => '',
        ];
        $params['sign'] = $this->makeSignature($params, $this->config->get('app_key'));
        $response = $this->client->post($this->endpoint, ['form_params' => $params]);
        $result = $this->parseResult((string)$response->getBody());
        return $result;
    }

    /**
     * @param $params
     * @param $appKey
     * @return string
     */
    private function makeSignature(array $params, string $appKey)
    {
        // 1. 字典升序排序
        ksort($params);

        // 2. 拼按URL键值对
        $str = '';
        foreach ($params as $key => $value) {
            if ($value !== '') {
                $str .= $key . '=' . urlencode($value) . '&';
            }
        }

        // 3. 拼接app_key
        $str .= 'app_key=' . $appKey;

        // 4. MD5运算+转换大写，得到请求签名
        $sign = strtoupper(md5($str));
        return $sign;
    }

    /**
     * Parse result
     *
     * @param  string $result
     * @throws TranslateException
     * @return string
     */
    protected function parseResult($result)
    {
        $arr = json_decode($result, true);
        if (empty($arr) || !isset($arr['ret'])) {
            throw new TranslateException('Invalid response: ' . $result, 400);
        }
        if ($arr['ret'] !== 0) {
            $message = $arr['msg'];
            throw new TranslateException($message, $arr, $arr['ret']);
        }

        return $arr['data']['target_text'];
    }
}
