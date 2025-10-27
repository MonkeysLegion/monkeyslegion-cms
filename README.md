# MonkeysLegion CMS

**Framework-agnostic backend engine** for building modular, headless CMS applications.  
Provides flexible API-first architecture, strong RBAC, Git-native content support, and scalable infrastructure for creating content-driven apps while keeping the core reusable and maintainable.

---

## Features

- Headless-first, API-first design  
- Modular architecture & reusable packages  
- Optional Git-native content support
- Strong Role-Based Access Control (JWT/OAuth2)  
- Scalable CI/CD-ready infrastructure  
- Developer-friendly tools for maintainability  

---

## Requirements

- PHP 8.4+  
- MySQL 8 (InnoDB)  
- Redis (for caching and queues)  
- Meilisearch (Elasticsearch adapter for next milstones)  
- Docker Compose (php-fpm, nginx, mysql, redis, meilisearch) for development  

---

## Installation / Setup

1. Clone the repo:  
   ```bash
   git@github.com:MonkeysLegion/monkeyslegion-cms.git


# Project Structure

This document explains the purpose of each folder in the `monkeyslegion-cms` project.

---

## app/
Contains the main MVC-related code.

- **Controller/** → Handles HTTP requests and routes, delegates logic to services.
- **Entity/** → Core domain entities/models.
- **Repository/** → Data access layer for entities, abstracts database operations.

---

## config/
All configuration files for the engine and modules.

- `.mlc` → MonkeysLegion-specific config files (module/app-level)
- `.php` → Global engine configuration

---

## src/
Core engine code and modular components.

- **Core/** → Bootstrapping, kernel, HTTP handling, DI container.
- **Modules/** → Self-contained modules (Auth, Content, RBAC), reusable across apps.
- **Services/** → Business/domain logic, e.g.
- **Events/** → Event system to decouple modules
- **Template/** → Helper functions for rendering or ML templates.
- **Utils/** → Generic utility functions and constants.

---

## public/
Public web root.

- **index.php** → Front controller.
- **assets/** → Static assets, manifest files, etc.

---

## tests/
Unit and integration tests.

---

## var/log/
Runtime log files (temporary runtime logs, separated from storage/logs).

---

## ml/
Framework-specific ML commands cli entry point.

---

## LICENSE / LICENSES
- LICENSE → Main license for the repo (Apache-2.0 recommended).  
- LICENSES/ → Sub-licenses or component licenses (ENGINE_LICENSE, FRAMEWORK_LICENSE).

---

## README.md / FRAMEWORK_README.md
Documentation for the repo and framework usage.

