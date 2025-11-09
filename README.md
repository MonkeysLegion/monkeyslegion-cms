# MonkeysLegion CMS

**Framework-agnostic backend engine** for building modular, headless CMS applications.  
Provides flexible API-first architecture, strong RBAC, Git-native content support, and scalable infrastructure for creating content-driven apps while keeping the core reusable and maintainable.

---

## 🚀 Features

- Headless-first, API-first design  
- Modular architecture & reusable packages  
- Optional Git-native content support  
- Strong Role-Based Access Control (JWT/OAuth2)  
- Scalable CI/CD-ready infrastructure  
- Developer-friendly tools for maintainability  

---

## ⚙️ Requirements

- PHP **8.4+**  
- MySQL **8 (InnoDB)**  
- Redis (cache & queues)  
- Meilisearch (Elasticsearch adapter for future milestones)  
- Docker Compose (php-fpm, nginx, mysql, redis, meilisearch) for development  

---

## 🧩 Installation / Setup

1. Clone the repository:
   ```bash
   git clone git@github.com:MonkeysLegion/monkeyslegion-cms.git
   cd monkeyslegion-cms
   composer install
   ```
2. Copy and configure environment variables:
   ```bash
   cp .env.example .env
   ```
3. Start development containers:
   ```bash
   docker compose up -d
   ```
4. Access the health check endpoint:
   ```
   http://localhost/healthz
   ```

---

## 🏗️ Project Structure

| Folder | Description |
|---------|--------------|
| **app/** | MVC-related code: controllers, entities, repositories |
| **config/** | All configuration files (`.mlc`, `.php`) |
| **src/** | Core package |
| **public/** | Front controller and static assets |
| **resources/** | Templates and layouts |
| **tests/** | Unit and integration tests |
| **var/** | Runtime logs and temp files |
| **ml** | CLI entry points for MonkeysLegion commands |

---

## 📘 Documentation

- [Contributing Guide](./CONTRIBUTING.md)  
- [Code of Conduct](./CODE_OF_CONDUCT.md)  
- [Security Policy](./SECURITY.md)  
- [Framework README](./FRAMEWORK_README.md)  

---

## 🧠 License

This project is licensed under the [Apache-2.0 License](./LICENSE).  
See also:
- [ENGINE_LICENSE](./ENGINE_LICENSE)
- [FRAMEWORK_LICENSE](./FRAMEWORK_LICENSE)

---

## 🤝 Contributing

Contributions are welcome! Please see the [Contributing Guide](./CONTRIBUTING.md) before submitting a PR.  
You can also discuss ideas or RFCs in the [GitHub Discussions](https://github.com/orgs/MonkeysLegion/discussions).

---

## 🛡️ Security

If you discover a vulnerability, please review the [Security Policy](./SECURITY.md) for instructions on responsible disclosure.

---

## 🧭 Roadmap

The roadmap and milestones are tracked in the [Docs Repository](https://github.com/MonkeysLegion/monkeyslegion-cms-docs) and [GitHub Projects](https://github.com/orgs/MonkeysLegion/projects).

---

© 2025 MonkeysLegion. Built with precision and intent.
