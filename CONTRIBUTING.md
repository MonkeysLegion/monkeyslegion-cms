# Contributing to MonkeysLegion

First of all, thank you for considering contributing! :fire:  
This document outlines the process and standards we use to keep the project consistent, maintainable, and welcoming.

---

## How to Contribute

You can contribute in several ways:

1. **Code contributions** – fixing bugs, adding features, or improving the core framework.
2. **Documentation** – improving guides, API references, or examples.
3. **Testing** – writing unit or integration tests to improve coverage.
4. **Ideas & Feedback** – suggesting improvements or reporting issues.

---

## Getting Started

1. **Fork the repository**  
2. **Clone your fork**:

```bash
git clone https://github.com/MonkeysLegion/monkeyslegion-cms.git
cd monkeyslegion-cms
```

3. **Install dependencies**:
```bash
composer install 
```

4. **Create a feature branch**:

```
git checkout -b feature/my-awesome-feature
```

5. Make your changes, then **commit with a clear message**:

```
git commit -m "Add feature X to module Y"
```

6. **Push your branch** and open a pull request (PR).

---

## Coding Standards

- **PHP** → PSR-12, strict types enabled, use typed properties/methods where possible.
- **Commit messages** → Clear, imperative tense (`Add`, `Fix`, `Refactor`, `Feat`).
- Keep code modular and DRY.
- Tests should cover new features or bug fixes.

---

## Pull Request Workflow

- PR must target the **`main` branch**.  
- Include a description of your change and why it’s needed.  
- Link any related issues if applicable.  
- All tests must pass before merging.  
- A reviewer from the core team will approve before merge.

---

## Reporting Issues

If you find a bug or have a feature request:

1. Check if it’s already reported.
2. Open a new issue with a clear title and description.
3. Provide steps to reproduce the bug if applicable.

---

## Code of Conduct

By participating, you agree to follow the [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md).  
We expect everyone to be respectful and professional.

---

Thank you for helping MonkeysLegion grow! Your contributions make the project stronger and better for everyone.
