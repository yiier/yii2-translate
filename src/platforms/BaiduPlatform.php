<?php
/**
 * author     : forecho <zhenghaicai@hk01.com>
 * createTime : 2019/7/21 1:25 PM
 * description:
 */

namespace yiier\translate\platforms;

use yiier\translate\exceptions\TranslateException;

/**
 * 百度通用翻译API
 * https://api.fanyi.baidu.com/api/trans/product/prodinfo
 * Class BaiduPlatform
 * @package yiier\translate\platforms
 */
class BaiduPlatform extends Platform
{
    const ENDPOINT = 'https://fanyi-api.baidu.com/api/trans/vip/translate';

    /**
     * @var string
     */
    protected $endpoint;

    protected static $language = [
        'zh' => 'zh',   //中文
        'en' => 'en',   //英文
        'jp' => 'jp',   //日文
        'ko' => 'kor',  //韩文
        'fr' => 'fra',  //法文
        'ru' => 'ru',   //俄文
        'es' => 'spa',  //西班牙文
        'it' => 'it',  // 意大利文
        'de' => 'de',  // 德文
        'pt' => 'pt',  //葡萄牙文
        'th' => 'th',  // 泰文
    ];

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        $headers = [
            'Content-Type' => 'application/json; charset=utf8',
            'Accept' => 'text/json',
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
            'from' => self::dataGet(self::$language, $from, $from),
            'to' => self::dataGet(self::$language, $to, $to),
            'appid' => $this->config->get('app_id'),
            'q' => $string,
            'salt' => strval(time()),
        ];
        $params['sign'] = $this->makeSignature($params, $this->config->get('app_key'));
        $response = $this->client->post($this->endpoint, ['form_params' => $params]);
        $result = $this->parseResult((string)$response->getBody());
        return $result;
    }

    /**
     * @param array $params
     * @param string $appKey
     * @return string
     */
    private function makeSignature(array $params, string $appKey)
    {
        return md5($params['appid'] . $params['q'] . $params['salt'] . $appKey);
    }

    /**
     * Parse result
     *
     * @param  string $result
     * @throws TranslateException
     * @return array
     */
    protected function parseResult($result)
    {
        $arr = json_decode($result, true);
        if (empty($arr)) {
            throw new TranslateException('Invalid response: ' . $result, 400);
        }
        if (!empty($arr['error_code'])) {
            $message = $arr['error_msg'];
            throw new TranslateException($message, $arr, $arr['error_code']);
        }

        return $arr['trans_result'][0]['dst'];
    }
}