<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/5/23 12:26 PM
 * description:
 */

namespace yiier\translate;

use yiier\translate\exceptions\TranslateException;

class Translate extends Factory
{
    private $from;
    private $to;

    /**
     * 翻译
     * @param $string
     * @return string
     * @throws TranslateException
     */
    public function translate($string)
    {
        $platform = $this->platform($this->platformName);
        $from = $this->from ?: $this->config->get('from', 'cn');
        $to = $this->to ?: $this->config->get('to', 'en');
        return $platform->translate($string, $from, $to);
    }

    /**
     * @param string $to
     * @return Translate
     */
    public function setTo(string $to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param string $from
     * @return Translate
     */
    public function setFrom(string $from)
    {
        $this->from = $from;
        return $this;
    }
}
