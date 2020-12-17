# Тестовое задание SimpleApi

Сайт доступен по ссылке: https://simple-api-hw.herokuapp.com/categories

## Инструкция для запуска

1. Клонируем репозиторий
2. Создаем базу данных
3. Создаем файл .env и копируем в него .env.example
4. В .env меняем настройки базы данных под свои
5. Последовательно прописываем в папке проекта
   ```
   composer install
   php artisan key:generate
   php artisan migrate --seed
   ```
6. Запускаем Web Server

## Данные для входа

Для работы доступны 3 аккаунта

- test1@gmail.com
- test2@gmail.com
- test3@gmail.com

У всех 3 аккаунтов пароль **password**

## Авторизация

Для получения токена авторизации нужно сделать *POST* запрос с выбранным email и password по адресу `/auth/token`

Для доступа к защищенным действиям нужно передавать с каждым запросом заголовок следующего формата
`Authorization: Bearer <token>`
