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
 * 谷歌v3版本翻译
 * https://cloud.google.com/translate/docs/advanced/translating-text-v3
 * Class Googlev2Platform
 * @package yiier\translate\platforms
 */
class Googlev3Platform extends Platform
{
    const ENDPOINT = "https://translation.googleapis.com/v3/projects/%s/locations/global:translateText";

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
            'Authorization' => 'Bearer '.$this->config->get('token') ,
            'timeout' => method_exists($this, 'getTimeout') ? $this->getTimeout() : 5.0,
        ]);

        $this->endpoint = $this->config->get('endpoint') ?: self::ENDPOINT;

        return $client;
    }


    public function translate(string $string, string $from, string $to)
    {
        $projectId = $this->config->get('project_id');
        $params = [
            'contents' => [$string],
            'targetLanguageCode' => $to,
            'sourceLanguageCode' => $from,
            'model' => sprintf('projects/%s/locations/global/models/general/base', $projectId),
        ];

        $response = $this->client->post(sprintf($this->endpoint,$projectId), ['json' => $params]);
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
