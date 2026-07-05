## How test?

**Linux:**

1. install docker:
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
```

2. install git:
```bash
sudo apt update && sudo apt install git -y
git config --global user.name "Ваше Ім'я"
git config --global user.email "your_email@example.com"
```

3. сlone the repository and navigate to the folder: 
```bash
git clone https://github.com/liubava333/digital_wallet
cd digital_wallet
```

4. install PHP dependencies (Composer) via Docker:
```bash
  docker run --rm \
-u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
laravelsail/php83-cli:latest \
composer install --ignore-platform-reqs
```

5. сreate the .env configuration file:
```bash
cp .env.example .env
```

6. start Laravel Sail containers:
```bash
./vendor/bin/sail up -d
```

7. configure the Laravel application (Key generation and Migrations):
```bash
sail artisan key:generate
sail artisan migrate
```

8. install frontend dependencies and start the development server:
```bash
sail npm install
sail npm run dev
```

**Windows:**

1. install WSL 2 and Ubuntu:
```bash
wsl --install
```

2. restart your computer if prompted.

3. install Docker Desktop:
   - Download and install Docker Desktop for Windows (https://www.docker.com/products/docker-desktop/)
   - During installation, ensure the "Use the WSL 2 based engine" option is checked.
   - Open Docker Desktop settings (Settings > Resources > WSL Integration), enable integration for your default Ubuntu distro, and click Apply & restart.

4. open your Linux Terminal.

5. continue run steps of 'Linux' from step 2.
---------------------------------------------
Биллинговая система (подписки и баланс кошелька)

1. Pinia 
2. Model relationship: One-To-Many (User -> LedgerTransaction), Many-To-Many (User <-> SubscriptionTier)
3. Dynamic accessor wallet_balance
4. Seeding

To populate the users and subscription_tiers tables with test data after completing all the steps, you need to run the seeding command inside the Laravel Sail container:
```bash
./vendor/bin/sail artisan make:seeder SubscriptionTierSeeder
```
Open database/seeders/SubscriptionTierSeeder.php and add the subscription tiers.
```bash
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionTier;

class SubscriptionTierSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionTier::create([
            'name' => 'Basic Plan',
            'price' => 9.99,
            'duration_days' => 30,
        ]);

        SubscriptionTier::create([
            'name' => 'Premium Plan',
            'price' => 29.99,
            'duration_days' => 90,
        ]);
    }
}
```
Open database/seeders/DatabaseSeeder.php and register user creation and run the subscription tiers seeder there:
```bash
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Створюємо одного тестового користувача для входу
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // пароль для входу
        ]);

        // Запускаємо сидер тарифних планів
        $this->call([
            SubscriptionTierSeeder::class,
        ]);
    }
}
```
Make seed:
```bash
./vendor/bin/sail artisan db:seed
```
Add funds:
```bash
./vendor/bin/sail artisan tinker
$user = \App\Models\User::find(1);
$user->ledgerTransactions()->create([
    'amount' => 100.00, 
    'type' => 'deposit'
]);
```
