<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/5/19 10:55 AM
 * description:
 */

namespace yiier\translate\contracts;

use GuzzleHttp\Client;
use yiier\translate\exceptions\TranslateException;

interface PlatformInterface
{

    /**
     * Get platform name.
     * @return string
     */
    public function getName();


    /**
     * Get platform client.
     * @return string|Client
     */
    public function getClient();


    /**
     * 翻译
     * @param string $string
     * @param string $from
     * @param string $to
     * @throws TranslateException
     * @return string
     */
    public function translate(string $string, string $from, string $to);
}
