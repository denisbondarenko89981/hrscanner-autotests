<?php
require_once 'vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Exception\NoSuchElementException;

class RegistrationTest extends \Codeception\Test\Unit {
    private $config;
    private $driver;

    protected function setUp(): void {
        // Загрузка конфигурации
        $this->config = require __DIR__ . '/../config/config.php';

        // Путь к ChromeDriver из конфигурационного файла
        $chromeDriverPath = $this->config['chromeDriverPath'];

        // Настройка опций Chrome
        $options = new ChromeOptions();
        $options->addArguments(['--start-fullscreen']);

        // Инициализация WebDriver
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        // Использование URL ChromeDriver из конфигурационного файла
        $this->driver = RemoteWebDriver::create($this->config['chromeDriverUrl'], $capabilities);
    }

    public function testWebPage() {
        // Переход на страницу https://hrscanner.ru/
        $this->driver->get('https://hrscanner.ru/');

        // Проверка кнопки «Протестировать HRSCANNER»
        try {
            // Попытка найти элемент по XPath
            $button = $this->driver->findElement(WebDriverBy::xpath("//body[@class='body']//button[@class='btn__test popup-link']"));

            // Если элемент найден, кликаем по нему
            $button->click();
        } catch (NoSuchElementException $exception) {
            // Если элемент не найден, тест будет зафейлен с сообщением об ошибке
            $txt = "{$this->config['emojiFailure']} Тест проверки регистрации НЕ прошёл. Не найдена кнопка «Протестировать HRSCANNER».";
            $this->sendMessage($txt);
        }

        // Проверка поля «Ваш email»
        try {
            // Попытка найти поле ввода email по XPath
            $emailInput = $this->driver->findElement(WebDriverBy::xpath("//fieldset[@class='form__group']//input[@id='regEmail']"));
            $date = new DateTime();
            // Если поле ввода найдено, вводим данные
            $emailInput->sendKeys($date->getTimestamp() . '@hrscanner.ru');
        } catch (NoSuchElementException $exception) {
            // Если поле ввода не найдено, тест будет зафейлен с сообщением об ошибке
            $txt = "{$this->config['emojiFailure']} Тест проверки регистрации НЕ прошёл. Не найдено поле ввода email.";
            $this->sendMessage($txt);
        }

        // Проверка поля «Номер телефона»
        try {
            // Попытка найти поле ввода телефона по XPath
            $phoneInput = $this->driver->findElement(WebDriverBy::xpath("//fieldset[@class='form__group']//input[@id='regPhone']"));

            // Создание объекта DateTime
            $date = new DateTime();

            // Если поле ввода найдено, вводим временную метку
            $phoneInput->sendKeys("4" . $date->getTimestamp());
        } catch (NoSuchElementException $exception) {
            // Если поле ввода не найдено, тест будет зафейлен с сообщением об ошибке
            $txt = "{$this->config['emojiFailure']} Тест проверки регистрации НЕ прошёл. Не найдено поле ввода телефона.";
            $this->sendMessage($txt);
        }

        // Проверка кнопки «Зарегистрироваться»
        try {
            // Попытка найти кнопку по XPath
            $button = $this->driver->findElement(WebDriverBy::xpath("//div[@class='popup__form']//button[@class='btn btnForm__popup btnForm__popup_register']"));

            // Если кнопка найдена, осуществляем клик
            $button->click();
        } catch (NoSuchElementException $exception) {
            // Если кнопка не найдена, тест будет зафейлен с сообщением об ошибке
            $txt = "{$this->config['emojiFailure']} Тест проверки регистрации НЕ прошёл. Не найдена кнопка регистрации.";
            $this->sendMessage($txt);
        }

        // 20 секунд на загрузку страницы
        sleep(20);

        // Проверка конечного url
        $currentUrl = $this->driver->getCurrentURL();

        // Условие для проверки URL
        if ($currentUrl === 'https://hrscanner.ru/ru/user/home') {
            // Если URL совпадает, выводим сообщение об успешной проверке
            $txt = "{$this->config['emojiSuccess']} Тест проверки регистрации прошёл успешно";
            $this->sendMessage($txt);
        } else {
            // Если URL не совпадает, фейлим тест и выводим сообщение об ошибке
            $txt = "{$this->config['emojiFailure']} Тест проверки регистрации НЕ прошёл. Получен URL $currentUrl \n вместо ожидаемого.";
            $this->sendMessage($txt);
            $this->fail("Проверка URL не пройдена.");
        }

        // Закрытие браузера
        $this->driver->quit();
    }

    // Код телеграмм бота
    private function sendMessage($txt) {
        $urlParameters = http_build_query([
            'chat_id' => $this->config['chatId'],
            'text' => $txt
        ]);

        $ch = curl_init($this->config['urlToken']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlParameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertNotEmpty($response);
    }

    protected function tearDown(): void {
        // Закрытие браузера
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}
