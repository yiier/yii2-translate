<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/7/21 1:25 PM
 * description:
 */

namespace yiier\translate\platforms;

use Google\Cloud\Translate\V3\TranslationServiceClient;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yiier\translate\exceptions\TranslateException;

/**
 * 谷歌v3版本翻译
 * https://cloud.google.com/translate/docs/advanced/translating-text-v3
 * Class Googlev3Platform
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
     * @return null
     */
    public function getClient()
    {
        return null;
    }

    /**
     * @param string $string
     * @param string $from
     * @param string $to
     * @return string
     * @throws TranslateException
     */
    public function translate(string $string, string $from, string $to)
    {
        $keyJson = $this->config->get('key_json');
        $key = Json::decode($keyJson, true);
        $projectId = $this->config->get('project_id') ?: ArrayHelper::getValue($key, 'project_id');
        $content = [$string];

        try {
            $translationServiceClient = new TranslationServiceClient(
                ['credentials' => $key]
            );
            $formattedParent = $translationServiceClient->locationName($projectId, 'global');
            $response = $translationServiceClient->translateText(
                $content,
                $to,
                $formattedParent
            );
            foreach ($response->getTranslations() as $translation) {
                return $translation->getTranslatedText();
            }
        } catch (\Exception $e) {
            throw new TranslateException('翻译返回结果失败' . $e->getMessage(), (array)$e, 400);
        }
    }
}
