# 🔧 Come&Fix

> **Connecting homeowners with skilled handymen for hassle-free home repairs**

A modern web platform that bridges customers and professional handymen (Tukang) through real-time communication, location-based search, and secure payment processing.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.1-38B2AC?style=flat&logo=tailwind-css)

---

## 🛠️ Built With

| Category | Technology |
|----------|-----------|
| **Backend** | Laravel 12, PHP 8.2 |
| **Frontend** | TailwindCSS, Alpine.js |
| **Real-time** | Laravel Reverb, Pusher |
| **Payment** | Midtrans |
| **Database** | MySQL |

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL (XAMPP recommended)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/benedictusyoga/ComeAndFix.git
cd ComeAndFix

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_DATABASE=comeandfix
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate --seed

# 6. Create storage link
php artisan storage:link
```

### Running the App

You need **3 terminal windows**:

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Frontend Assets
npm run dev

# Terminal 3: WebSocket Server
php artisan reverb:start --host=127.0.0.1 --port=8080
```

🎉 **Visit** http://localhost:8000

---

## 🌟 Configuration

### Environment Variables

Key settings in your `.env` file:

```env
# Database
DB_DATABASE=comeandfix
DB_USERNAME=root
DB_PASSWORD=

# Real-time (WebSocket)
BROADCAST_DRIVER=reverb
REVERB_HOST=127.0.0.1
REVERB_PORT=8080

# Payment (Midtrans)
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

---

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Benedictus Yogatama Favian Satyajati**  
GitHub: [@benedictusyoga](https://github.com/benedictusyoga)

**Bryan Hugh Giovandi**  
GitHub: [@BryanHugh8](https://github.com/BryanHugh8)

**Revaldo Apriyan Mahulae**  
GitHub: [@RevaldoAm](https://github.com/RevaldoAm)

---

## 🙏 Acknowledgments

Built with ❤️ using:
- [Laravel](https://laravel.com) - The PHP Framework
- [TailwindCSS](https://tailwindcss.com) - Utility-first CSS
- [Laravel Reverb](https://reverb.laravel.com) - Real-time WebSockets
- [Midtrans](https://midtrans.com) - Payment Gateway

---

<div align="center">

**⚠️ Note**: This is a thesis project for educational purposes.

</div>
