## How test?

**Linux:**

- install docker:
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
```

- install git:
```bash
sudo apt update && sudo apt install git -y
git config --global user.name "Ваше Ім'я"
git config --global user.email "your_email@example.com"
```

- сlone the repository and navigate to the folder: 
```bash
git clone https://github.com/liubava333/digital_wallet
cd digital_wallet
```

- install PHP dependencies (Composer) via Docker:
```bash
  docker run --rm \
-u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
laravelsail/php83-cli:latest \
composer install --ignore-platform-reqs
```

- сreate the .env configuration file:
```bash
cp .env.example .env
```

- start Laravel Sail containers:
```bash
./vendor/bin/sail up -d
```

- configure the Laravel application (Key generation and Migrations):
```bash
sail artisan key:generate
sail artisan migrate
```

- install frontend dependencies and start the development server:
```bash
sail npm install
sail npm run dev
```

