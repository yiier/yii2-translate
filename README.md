文本翻译
====
腾讯翻译君翻译，百度翻译，谷歌翻译, 彩云小译

[![Latest Stable Version](https://poser.pugx.org/yiier/yii2-translate/v/stable)](https://packagist.org/packages/yiier/yii2-translate) 
[![Total Downloads](https://poser.pugx.org/yiier/yii2-translate/downloads)](https://packagist.org/packages/yiier/yii2-translate) 
[![Latest Unstable Version](https://poser.pugx.org/yiier/yii2-translate/v/unstable)](https://packagist.org/packages/yiier/yii2-translate) 
[![License](https://poser.pugx.org/yiier/yii2-translate/license)](https://packagist.org/packages/yiier/yii2-translate)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-translate "*"
```

or add

```
"yiier/yii2-translate": "*"
```

to the require section of your `composer.json` file.


Usage
-----

```php
<?php

$config = [
    // HTTP 请求的超时时间（秒）
	'timeout' => 5.0,
	'from' => 'zh', // 源本语言，可选参数，默认是 zh
	'to' => 'en', // 要翻译成的语言，可选参数，默认是 en

    // 可用的平台配置
	'platforms' => [
		'qqtranslation' => [
			'app_id' => '你的 QQ AI 应用ID',
			'app_key' => '你的 QQ AI 应用密钥',
		],
		'baidu' => [
			'app_id' => '百度翻译应用 ID',
			'app_key' => '百度翻译应用秘钥',
		],
		'google_v2' => [
			'key' => 'xxx', // https://console.cloud.google.com/apis/credentials
		],
		'google_v3' => [
			'project_id' => 'xxxx', // Optional
             // https://github.com/googleapis/google-cloud-php/blob/master/AUTHENTICATION.md
			'key_json' => '{"type":"xx","project_id":"xx","private_key_id":"xx","private_key":"","client_email":"xx","client_id":"xx","auth_uri":"xx","token_uri":"xx","auth_provider_x509_cert_url":"xx","client_x509_cert_url":"xx"}',
		],
		'caiyun' => [
			'token' => 'xxx', // https://fanyi.caiyunapp.com/#/api
		],
	],
];

$translate = new \yiier\translate\Translate($config, 'qqtranslation');

$translate->translate('今天天气怎么样');

$translate->setFrom('zh')->translate('今天天气怎么样');

$translate->setFrom('zh')->setTo('de')->translate('今天天气怎么样');

$translate->setTo('de')->translate('今天天气怎么样');

```

如果使用 `setFrom` 或者 `setTo`，会忽略配置文件中的配置。


⚠ 注意：如果要使用 `google_v3`，必须要安装官方依赖包：

```
composer require google/cloud-translate
```


PS：语言支持以 Google 的[《语言支持》](https://cloud.google.com/translate/docs/languages) 为标准。

## 参考文档

- [[译] 使用谷歌Cloud Translation API翻译文本](https://segmentfault.com/a/1190000014205232)
- [Translate Text with the Translation API](https://codelabs.developers.google.com/codelabs/cloud-translation-intro/#0)
- [为服务器到服务器的生产应用设置身份验证](https://cloud.google.com/docs/authentication/production)
- [翻译文本（高级版）](https://cloud.google.com/translate/docs/advanced/translating-text-v3)

