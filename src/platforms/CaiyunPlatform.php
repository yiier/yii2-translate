<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/7/21 1:25 PM
 * description:
 */

namespace yiier\translate\platforms;

use Google\Cloud\Translate\V3\TranslationServiceClient;
use yii\helpers\ArrayHelper;
use yiier\translate\exceptions\TranslateException;

/**
 * 彩云小译 API
 * https://fanyi.caiyunapp.com/#/api
 * Class Googlev2Platform
 * @package yiier\translate\platforms
 */
class CaiyunPlatform extends Platform
{
    const ENDPOINT = "http://api.interpreter.caiyunai.com/v1/translator";

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36',
            'x-authorization' => 'token ' . $this->config->get('token'),
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
            'source' => [$string],
            'trans_type' => "{$from}2{$to}",
            'request_id' => 'yii2-translate-' . time(),
        ];

        $response = $this->client->post($this->endpoint, ['json' => $params]);
        return $this->parseResult((string)$response->getBody());
    }

    /**
     * Parse result
     *
     * @param string $result
     * @return string
     * @throws TranslateException
     */
    protected function parseResult($result)
    {
        $rsp = json_decode($result);
        $result = ArrayHelper::getValue($rsp, 'target.0', '');

        if ($result) {
            return $result;
        }
        throw new TranslateException('翻译返回结果失败', (array)$result, 400);
    }
}
