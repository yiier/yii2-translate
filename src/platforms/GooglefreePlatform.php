<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/7/21 1:25 PM
 * description:
 */

namespace yiier\translate\platforms;

use yiier\translate\exceptions\TranslateException;

/**
 * 谷歌免费翻译
 * https://github.com/statickidz/php-google-translate-free
 * Class GooglefreePlatform
 * @package yiier\translate\platforms
 */
class GooglefreePlatform extends Platform
{
    const ENDPOINT = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";

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
            'sl' => urlencode($from),
            'tl' => urlencode($to),
            'q' => urlencode($string)
        ];
        if (strlen($params['q']) >= 5000) {
            throw new TranslateException("Maximum number of characters exceeded: 5000");
        }

        $response = $this->client->post($this->endpoint, ['form_params' => $params]);
        $result = $this->parseResult((string)$response->getBody());
        return $result;
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
        $sentencesArray = json_decode($result, true);
        $sentences = "";

        if (!$sentencesArray) {
            throw new TranslateException("Google detected unusual traffic from your computer network, try again later (2 - 48 hours)");
        }
        foreach ($sentencesArray["sentences"] as $s) {
            $sentences .= isset($s["trans"]) ? urldecode($s["trans"]) : '';
        }
        return $sentences;
    }
}
