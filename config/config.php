<?php
// config.php

return [
    'emoji_success' => json_decode('"\u2705"'),
    'emoji_failure' => json_decode('"\u274C"'),
    'urlToken' => 'https://api.telegram.org/bot<ваш token>/sendMessage',
    'chatId' => '<ваш chatId>',
    'chromeDriverPath' => __DIR__ . '/../chromedriver.exe',
];