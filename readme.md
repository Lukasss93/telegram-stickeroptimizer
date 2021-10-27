<img src="https://i.imgur.com/6Wi7eHS.png" alt="logo"/>

# Telegram Bot: Sticker Optimizer

[![link](https://img.shields.io/badge/bot-%40newstickeroptimizerbot-blue)](https://t.me/newstickeroptimizerbot)
![status](https://img.shields.io/badge/status-online-green)
[![link](https://img.shields.io/badge/news-%40LKS93C-blue)](https://t.me/LKS93C)
[![link](https://img.shields.io/badge/support-%40Lukasss93Support-orange)](https://t.me/Lukasss93Support)

> Optimize an image or sticker to a png file to make its size smaller than or equal to 512Kb,
> so that you will be able to add it to a sticker pack using the [@stickers](https://t.me/stickers) bot.

## Requirements
- Apache / nginx
- SSL support
- PHP >= 8
    - ext-json
    - ext-pdo
    - ext-gd
    - webp support
- Imagick
- MariaDB >= 10.2.3 or Postgresql >= 9.5 or SQLite with JSON1 extension
- SystemD
- Crontab

## First deploy
1. `git clone https://github.com/<username>/telegram-stickeroptimizer.git`
2. `composer install`
3. `php artisan migrate`
4. `cp .env.example .env`
5. `php artisan key:generate`
6. Edit the `.env` file with your preferences
7. `php artisan storage:link`
8. `php artisan nutgram:register-commands`
9. `php artisan nutgram:hook:set https://<domain>.<tls>/hook`

## CD
1. `php artisan down`
2. `git reset --hard`
3. `git pull "https://<username>:<token>@github.com/<username>/telegram-stickeroptimizer.git" master `
4. `php composer.phar install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress `
5. `php artisan migrate --force --step `
6. `php artisan optimize`
7. `php artisan up`

## License
This is an open-source software licensed under the [MIT license](LICENSE.md).
