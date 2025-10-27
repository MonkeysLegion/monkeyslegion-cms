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
