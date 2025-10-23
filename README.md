


# **Ethnic and Migrant Minorities (EMM) Survey Registry System**

[![License: CC BY-NC 4.0](https://img.shields.io/badge/License-CC--BY--NC%204.0-blue.svg)](https://creativecommons.org/licenses/by-nc/4.0/)
[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.17227389.svg)](https://doi.org/10.5281/zenodo.17227389)
![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)
![Laravel](https://img.shields.io/badge/Laravel-11.x-ff2d20?logo=laravel)
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql)
![Elasticsearch](https://img.shields.io/badge/Elasticsearch-8.x-005571?logo=elasticsearch)
![Docker](https://img.shields.io/badge/Docker-ready-blue?logo=docker)
![Node](https://img.shields.io/badge/Node.js-18+-green?logo=node.js)
![Composer](https://img.shields.io/badge/Composer-2.x-orange?logo=composer)

**A free, searchable registry of quantitative surveys conducted with ethnic and migrant minority (EMM) (sub)populations across 34 European countries (2000–present).**

---

## **General Overview**

The **EMM Survey Registry** is a free online tool that allows users to search for and learn about existing quantitative surveys undertaken with EMM (sub)populations from **2000 onwards** in **34 European countries**, via compiled **survey-level metadata**.  
**Open front-end interface:** [https://registry.ethmigsurveydatahub.eu/](https://registry.ethmigsurveydatahub.eu/)

The registry enables researchers to identify, explore, and compare existing surveys through a structured, searchable interface. Each survey appears as a record described by detailed metadata. The interface follows established registry patterns so users can **search, filter, and retrieve** information efficiently.  
The system is designed to **facilitate discovery and curation** without imposing unnecessary complexity.

---

## **Technical Description**

The system is built on **Laravel** (open-source PHP framework) for a stable, extensible backend.  
The administrative UI uses **Laravel Nova**, providing controlled data entry, revision, and validation.

**Core technologies:**
- **Search & indexing:** Elasticsearch (full-text search and fast retrieval)  
- **Database:** MySQL  
- **Containers:** Docker for reproducible, portable development environments  

---

## **Core Functionality**

- Create, edit, and manage survey records via an administrative interface  
- Bulk import/export in CSV and Excel formats; incremental updates via the web UI  
- Powerful search and filtering (country, year, population group, survey status, etc.)  
- Full-text search across metadata fields (Elasticsearch integration)  
- User authentication with role-based access control  
- Email verification and optional ORCID validation for contributors  

---

## **License and Citation**

The software is distributed under the **CC BY-NC 4.0** license.  
You may use, share, and adapt this work for **non-commercial purposes with attribution**.  
Commercial use is not permitted without prior permission.  

**Full license:** [https://creativecommons.org/licenses/by-nc/4.0/legalcode](https://creativecommons.org/licenses/by-nc/4.0/legalcode)

**Suggested citation:**

> Popescu, T., Morales, L., Saji, A., & Taut, B. (2025). *Ethnic and Migrant Minorities (EMM) Survey Registry: Source code (Version 1)* [Computer software]. Zenodo.  
> [https://doi.org/10.5281/zenodo.17227389](https://doi.org/10.5281/zenodo.17227389)

---

## **Installation and Deployment**


1) Clone the repository

Run the following commands in your terminal:

```git clone https://github.com/your-repo/ethmig.git```

```cd ethmig```

2) Install dependencies

```composer install```

```npm install```

3) Configure the environment

Copy the example environment file and generate a new application key:

```cp .env.example .env```

```php artisan key:generate```

Edit the .env file to configure database, mail, and search (Elasticsearch) settings.

4) Run migrations and seed initial data

```php artisan migrate```

```php artisan db:seed```

5) Build frontend assets

```npm run dev```

6) Start the development server

```php artisan serve```

Docker Deployment

Copy the example Docker environment file:

```cp .docker.env.example .docker.env```

Start containers and run the same installation steps inside the Docker environment.

Production Mode

Set the following in your .env file:

```APP_ENV=production```

```APP_DEBUG=false```

Caches for performance:

```php artisan config:cache```

```php artisan route:cache```

```php artisan view:cache```

Database and Search Configuration

The system uses MySQL for persistence (configured in .env).
For enhanced search, configure Elasticsearch locally or via Docker.

Initialize search mappings and rebuild index:

```php artisan ethmig:generate-mapping```

```php artisan ethmig:rebuild-index```

These commands create the necessary mappings and synchronize database contents with the search index.

Data Import and Sample Database

A sample dataset is provided for testing.

To import survey metadata:

```php artisan ethmig:survey-import path/to/file.csv```

For spreadsheets with multiple sheets:

```php artisan ethmig:survey-import path/to/file.xlsx --multiple-sheets```

Sample files are located in the repository’s misc/ directory.

Testing and Quality Assurance

Run the test suite:

```php artisan test```

Run a specific test class:

```php artisan test --filter SurveyTest```

Documentation and User Resources
	•	General introduction: https://ethmigsurveydatahub.eu/emmregistry/
	•	Conditions of use of metadata: https://ethmigsurveydatahub.eu/conditions-of-use-of-the-emm-survey-registry-and-its-metadata/


Support

For questions or technical support, contact:
ethmigsurveydata@sciencespo.fr
bogdan@youngminds.ro
