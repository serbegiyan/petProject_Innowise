<p>Это приложение для создания и управления товарами и услугами в магазине (в админ части), а также для покупок товаров и услуг в магазине.</p>

<p>Админка разработана на Laravel/Blade, магазин — на Inertia/React.</p>

<h3>Стек технологий</h3>
<ul>
    <li>PHP 8.5 (эталон — Docker-контейнер <code>php_fpm_petProject</code>)</li>
    <li>Laravel 12</li>
    <li>Inertia.js</li>
    <li>Tailwind CSS</li>
    <li>Vite</li>
    <li>RabbitMQ</li>
    <li>S3</li>
    <li>Redis</li>
    <li>Jenkins</li>
    <li>Telescope</li>
    <li>MySQL</li>
    <li>Docker</li>
</ul>

<h3>PHP: версии и команды</h3>
<p>Минимум для зависимостей: PHP <strong>8.4+</strong> (<code>composer.json</code>: <code>^8.4</code>).</p>
<p>В разработке используется <strong>PHP 8.5</strong> из Docker. Без Docker на хосте нужен PHP 8.4 или 8.5 — иначе <code>composer</code> и <code>artisan</code> не запустятся.</p>
<p>Рекомендуется выполнять PHP-команды в контейнере или через обёртки в <code>bin/</code> (PHP 8.5 из Docker, <code>bin/</code> добавляется в PATH через <code>.mise.toml</code> и <code>.vscode/settings.json</code>):</p>
<pre><code>./bin/php artisan migrate
./bin/composer install
./bin/composer test
./bin/composer lint
./bin/composer format
./bin/composer analyse

# либо напрямую через docker exec:
docker exec php_fpm_petProject php artisan migrate
docker exec php_fpm_petProject composer install
docker exec php_fpm_petProject composer test
docker exec php_fpm_petProject composer lint
docker exec php_fpm_petProject composer format
docker exec php_fpm_petProject composer analyse</code></pre>
<p>После клонирования один раз: <code>mise trust</code> (если установлен <a href="https://mise.jdx.dev">mise</a>).</p>
<p>Окружение: скопируйте <code>laravel-app/.env.example</code> в <code>.env</code> — в нём хосты Docker (MySQL, Redis, RabbitMQ, LocalStack/S3).</p>
<p>Стиль кода: <strong>Laravel Pint</strong> (<code>composer lint</code> / <code>composer format</code>). Статический анализ: <code>composer analyse</code> (PHPStan level 4, в Jenkins после Pint).</p>
<p>Удобные алиасы (опционально, в <code>~/.bashrc</code>):</p>
<pre><code>alias art='docker exec php_fpm_petProject php artisan'
alias comp='docker exec php_fpm_petProject composer'</code></pre>

<h3>Docker и фронтенд (Vite)</h3>
<p>Из корня репозитория:</p>
<pre><code>docker compose up -d</code></pre>
<p>Сервис <code>node</code> автоматически выполняет <code>npm ci</code> (при необходимости) и <code>npm run dev</code> — HMR на <a href="http://localhost:5173">http://localhost:5173</a>. Приложение: <a href="http://localhost:8080/catalog">http://localhost:8080/catalog</a> (nginx).</p>
<pre><code>docker compose logs -f node          # лог Vite
docker compose restart node        # перезапуск dev-сервера
docker exec node_petProject npm run build   # production-сборка</code></pre>
<p>Без контейнера <code>node</code> (только собранные ассеты): <code>docker exec node_petProject npm run build</code> или <code>npm run build</code> в <code>laravel-app/</code>.</p>

<p>Точка входа: <a href="http://localhost:8080/catalog">http://localhost:8080/catalog</a></p>
