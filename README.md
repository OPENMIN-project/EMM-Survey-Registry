# Ethnic and Migrant Minorities Survey Registry

A Laravel-based survey registry system that allows users to manage, import, export, and search through survey data. The application features a Laravel Nova admin panel for comprehensive survey management.

## 🚀 Features

- **Survey Management**: Create, edit, and manage surveys with comprehensive metadata
- **Import/Export**: Bulk import surveys from Excel/CSV files and export data
- **Search & Filter**: Advanced search capabilities with Elasticsearch integration
- **User Management**: Role-based access control with user authentication
- **Admin Panel**: Laravel Nova-powered admin interface
- **API Support**: RESTful API endpoints for data access
- **Email Verification**: User email verification system
- **ORCID Integration**: ORCID ID validation for researchers

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 7.4
- **Composer** >= 2.0
- **Node.js** >= 14.0
- **MySQL** >= 5.7
- **Elasticsearch** >= 7.17.2 (optional, for search functionality)
- **Docker** & **Docker Compose** (for containerized development)

## 🛠️ Installation

### Option 1: Local Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ethmig
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your environment**
   Edit `.env` file with your database and application settings:
   ```env
   APP_NAME=Ethmig
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ethmig
   DB_USERNAME=ethmig
   DB_PASSWORD=ethmig
   
   # Elasticsearch (optional)
   SCOUT_DRIVER=elasticsearch
   ELASTICSEARCH_HOST=localhost:9200
   
   # Mail settings
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="noreply@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets**
   ```bash
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

### Option 2: Docker Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ethmig
   ```

2. **Copy environment file**
   ```bash
   cp .docker.env.example .docker.env
   ```

3. **Start Docker services**
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

5. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

6. **Run migrations and seed**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run dev
   ```

## 🔧 Configuration

### Database Configuration

The application uses MySQL as the primary database. Ensure your database is properly configured in the `.env` file.

### Elasticsearch Configuration (Optional)

For search functionality, configure Elasticsearch:

1. **Install Elasticsearch**
   ```bash
   # Using Docker
   docker run -d --name elasticsearch -p 9200:9200 -p 9300:9300 elasticsearch:7.17.2
   
   # Or using the provided docker-compose
   docker-compose up elk
   ```

2. **Configure Scout**
   ```bash
   php artisan scout:import "App\Survey"
   ```

### Mail Configuration

Configure your mail settings in `.env` for email verification and notifications.

## 👥 User Management

### User Roles

The application supports the following user roles:

- **Master**: Full administrative access
- **Admin**: Administrative access to surveys and users
- **Editor**: Can create and edit surveys
- **User**: Basic access to view surveys

### Creating Users

1. **Via Nova Admin Panel**
   - Navigate to `/nova`
   - Go to Users section
   - Click "Create User"

2. **Via Artisan Command**
   ```bash
   php artisan tinker
   User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'role' => 'admin']);
   ```

## 📊 Survey Management

### Importing Surveys

1. **Using Artisan Command**
   ```bash
   php artisan ethmig:survey-import path/to/survey-file.csv
   ```

2. **Using Nova Admin Panel**
   - Navigate to Surveys in Nova
   - Click "Import Surveys" action
   - Upload your CSV/Excel file

### Survey Status

Surveys can have the following statuses:

- **Draft**: Work in progress
- **Ready**: Ready for review
- **Published**: Available to the public

### Survey Types

- **National**: Country-wide surveys
- **Sub-national**: Regional or local surveys

## 🔍 Search Functionality

The application provides advanced search capabilities:

- **Full-text search** across survey content
- **Filter by country** and survey type
- **Date range filtering**
- **Status-based filtering**

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter SurveyTest

# Run browser tests (requires Chrome)
php artisan dusk
```

## 📦 Development

### Available Artisan Commands

```bash
# Survey management
php artisan ethmig:survey-import {file} {--multiple-sheets}

# Field management
php artisan ethmig:generate-field-mapping
php artisan ethmig:change-field-type
php artisan ethmig:replace-field-placeholders

# Index management
php artisan ethmig:rebuild-index

# User management
php artisan ethmig:update-users-orcid-id
```

### Frontend Development

```bash
# Start Vite development server
npm run dev

# Build for production
npm run prod

# Build Nova components
npm run build-text-choice
npm run build-smart-heading
npm run build-indent
npm run build-nova-table-of-contents-field
```

## 🚀 Deployment

### Production Deployment

1. **Set environment to production**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize for production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   npm run prod
   ```

3. **Set up queue workers**
   ```bash
   php artisan queue:work
   ```

### Docker Production

```bash
# Build production image
docker build -t ethmig .

# Run with production configuration
docker run -d -p 80:80 ethmig
```

## 📁 Project Structure

```
ethmig/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # Web controllers
│   ├── Nova/                # Nova admin panel resources
│   ├── Imports/             # Survey import classes
│   └── ...
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeds/              # Database seeders
│   └── factories/          # Model factories
├── resources/
│   ├── js/                 # Vue.js components
│   ├── views/              # Blade templates
│   └── ...
├── routes/                 # Application routes
├── tests/                  # Test files
└── nova-components/        # Custom Nova components
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This work is licensed under the Creative Commons Attribution-NonCommercial 4.0 International License (CC BY-NC 4.0).
⦁	✅ Free for academic and research use
⦁	✅ You may share and adapt with attribution
⦁	❌ Commercial use is not permitted without prior permission
You are free to use, share, and adapt this work for non-commercial purposes, provided that attribution is given.
Reference: Popescu, T., Morales, L., Saji, A., & Taut, B. (2025). Ethnic and Migrant Minorities (EMM) Survey Registry: Source code (Version 1) [Computer software]. Zenodo. https://doi.org/10.5281/zenodo.17227389
Commercial use is not permitted without prior permission.
Full license text: https://creativecommons.org/licenses/by-nc/4.0/legalcode

## 🆘 Support

For support and questions:

- **Email**: ethmigsurveydata@sciencespo.fr
- **Documentation**: Check the `/nova` admin panel for detailed guides
- **Issues**: Report bugs and feature requests through the issue tracker

## 🔗 Links

- **Application**: [http://localhost:8000](http://localhost:8000)
- **Admin Panel**: [http://localhost:8000/nova](http://localhost:8000/nova)
- **API Documentation**: Available through the application

---

**Note**: This application is part of the SSHOC project and is designed for managing migration survey data across European countries.
