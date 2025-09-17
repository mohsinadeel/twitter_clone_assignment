# Twitter Mini Assignment

A simplified Twitter/X clone built with **Laravel 12**, **MySQL**, and **Laravel Sanctum**.

---

## Features

- User registration & login (via Laravel Breeze + Sanctum)
- User profile page with basic info (username, avatar placeholder)
- Post feed (max 280 characters per post)
- Create new posts
- RESTful API with consistent response format
- MySQL migrations & seeders
- Swagger/OpenAPI documentation (TBD)
- Tested with Pest (unit + feature tests)

---

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade with JS
- **Auth:** Laravel Sanctum (API tokens & session-based)
- **Database:** MySQL (via Laravel migrations/seeders)
- **Testing:** Pest
- **Containerization:** Laravel Sail (Docker)
- **Docs:** Swagger (via `darkaonline/l5-swagger` package) (TBD)

## GIT Clone and Installation

- **Clone the repository**

git clone <https://github.com/mohsinadeel/twitter_clone_assignment.git>

- **Go into the project directory**

cd twitter_clone_assignment

- **Install dependencies**

composer install
npm install

- **Copy environment file**

cp .env.example .env

- **Generate application key**

php artisan key:generate

- **Start Docker containers using Laravel Sail**

./vendor/bin/sail up -d

- **Run migrations (and seed if available)**

./vendor/bin/sail artisan migrate --seed

- **(Optional) Run tests**

./vendor/bin/sail artisan test

## Useful Links

- **Deployed URL**: <#>
- **Repository**: <https://github.com/mohsinadeel/twitter_clone_assignment>
