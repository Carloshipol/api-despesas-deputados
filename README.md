# Câmara Federal Deputies Expense Tracker

This project consists of a fullstack application built with **Laravel (API)** and **Vue.js (frontend)**. It synchronizes data from the [Câmara dos Deputados Open Data API](https://dadosabertos.camara.leg.br/) to track and display federal deputies' expenses.

## 📦 Project Structure

```
.
├── api-despesas-deputados/   # Laravel Backend (API)
├── camara-front/             # Vue.js Frontend
├── docker-compose.yml        # Docker configuration
└── README.md
```

---

## 🚀 Requirements

- Docker & Docker Compose
- Git

---

## 🛠️ Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/your-username/seu-repo.git
cd seu-repo
```

### 2. Start with Docker

```bash
docker-compose up -d
```

### 3. Run Laravel setup (inside container)

```bash
docker exec -it app bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
exit
```

### 4. Install frontend dependencies

```bash
cd camara-front
npm install
npm run dev
```

---

## ✅ Features

- 🔁 Synchronize all deputies from the Câmara API
- 💸 Fetch and store deputies' expense data
- 🔍 Filter deputies by name, party, and state (UF)
- ⚙️ Queue jobs for background processing via Laravel

---

## 🧪 Example

To synchronize expenses of a specific deputy:
```bash
POST /api/deputados/{id}/sincronizar-despesas
```

To trigger the general synchronization:
```bash
POST /api/sincronizar-deputados
```

---

## 🤝 Contributing

Feel free to fork and improve! Pull requests are welcome.

---

## 📄 License

MIT License