<img src="https://i.imgur.com/6Wi7eHS.png" alt="logo"/>

# Telegram Bot: Sticker Optimizer

[![link](https://img.shields.io/badge/bot-%40newstickeroptimizerbot-blue)](https://t.me/newstickeroptimizerbot)
![status](https://img.shields.io/badge/status-online-green)
[![link](https://img.shields.io/badge/news-%40LKS93C-blue)](https://t.me/LKS93C)
[![link](https://img.shields.io/badge/support-%40Lukasss93Support-orange)](https://t.me/Lukasss93Support)
![GitHub](https://img.shields.io/github/license/Lukasss93/telegram-stickeroptimizer)

> Optimize an image or sticker to a png file to make its size smaller than or equal to 512Kb,
> so that you will be able to add it to a sticker pack using the [@stickers](https://t.me/stickers) bot.

## 🛡 Requirements
- Apache / nginx
- SSL support
- PHP ≥ 8
    - ext-json
    - ext-pdo
    - ext-gd
    - webp support
- Imagick
- MariaDB ≥ 10.2.3 or Postgresql ≥ 9.5 or SQLite with JSON1 extension
- SystemD (to process async jobs)
- Crontab (to update cached statistics)
- GIT

## ⚙ First configuration
- Configure a cron:<br>
  `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`
- Configure a SystemD Unit:<br>
  `systemctl edit --force --full stickeroptimizer-news.service`
   ```shell
   [Unit]
   Description=stickeroptimizer.news
   StartLimitBurst=0
   
   [Service]
   Restart=always
   WorkingDirectory=<project-root>
   ExecStart=/bin/sh -c 'php artisan queue:work --queue=news --memory=512 >> <project-root>/storage/logs/worker-news.log'
   User=<user>
   Group=<group>
   
   [Install]
   WantedBy=default.target
   ```
  `sudo systemctl start stickeroptimizer-news.service`

## 🚀 First deploy
0. `cd <vhost-folder>`
1. `git clone https://github.com/<username>/telegram-stickeroptimizer.git`
2. `cd telegram-stickeroptimizer`
3. `composer install`
4. `php artisan migrate`
5. `cp .env.example .env`
6. `php artisan key:generate`
7. Edit the `.env` file with your preferences
8. `sudo chmod -R 775 bootstrap/`
9. `sudo chmod -R 775 storage/`
10. `php artisan storage:link`
11. `php artisan nutgram:register-commands`
12. `php artisan nutgram:hook:set https://<domain>.<tls>/hook`

## 🌠 Continuous deployment
This project will be updated in production at every pushed commit to master branch.<br>
Check this github workflow: [deploy.yml](.github/workflows/deploy.yml)
1. `php artisan down`
2. `git reset --hard`
3. `git pull "https://<username>:<token>@github.com/<username>/telegram-stickeroptimizer.git" master `
4. `php composer.phar install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress `
5. `php artisan migrate --force --step `
6. `php artisan optimize`
7. `php artisan up`

## 🛠 Built with
- Programming language: PHP 8
- Language framework: [Laravel 8](https://github.com/laravel/laravel)
- Bot framework: [Nutgram 0.15](https://github.com/SergiX44/Nutgram)

## ☑ TODO List
Check the [Projects](https://github.com/Lukasss93/telegram-stickeroptimizer/projects/2) page.

## 📃 Changelog
Please see the [changelog.md](changelog.md) for more information on what has changed recently.

## 🏅 Credits
- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](https://github.com/Lukasss93/telegram-stickeroptimizer/contributors)

## 📖 License
This is an open-source software licensed under the [MIT license](LICENSE.md).
