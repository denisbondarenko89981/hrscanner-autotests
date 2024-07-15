<?php
// config.php

return [
    'urlToken' => 'https://api.telegram.org/bot<ваш token>/sendMessage',
    'chatId' => '<ваш chatId>',
    'chromeDriverPath' => __DIR__ . '/../chromedriver.exe',
    'chromeDriverUrl' => 'http://localhost:9515',
    'emojiSuccess' => "✅",
    'emojiFailure' => "❌",
];