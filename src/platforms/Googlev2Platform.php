<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/7/21 1:25 PM
 * description:
 */

namespace yiier\translate\platforms;

use yii\helpers\ArrayHelper;
use yiier\translate\exceptions\TranslateException;

/**
 * 谷歌v2版本翻译
 * https://cloud.google.com/translate/docs/quickstart-client-libraries#client-libraries-usage-php
 * Class Googlev2Platform
 * @package yiier\translate\platforms
 */
class Googlev2Platform extends Platform
{
    const ENDPOINT = "https://translation.googleapis.com/language/translate/v2";

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
            'q' => $string,
            'target' => $to,
            'source' => $from,
            'key' => $this->config->get('key'),
        ];

        $response = $this->client->post($this->endpoint, ['form_params' => $params]);
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
        $result = ArrayHelper::getValue($rsp, 'data.translations.0.translatedText', '');

        if ($result) {
            return $result;
        }
        throw new TranslateException('翻译返回结果失败', (array)$result, 400);
    }
}
