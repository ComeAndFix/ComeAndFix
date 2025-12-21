# Contributing to Come&Fix

Thank you for your interest in contributing to Come&Fix! This document provides guidelines for contributing to this thesis project.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [How to Contribute](#how-to-contribute)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)

---

## 🤝 Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on what is best for the project
- Show empathy towards other contributors

---

## 🚀 Getting Started

1. **Fork the repository**
   ```bash
   # Click the "Fork" button on GitHub
   ```

2. **Clone your fork**
   ```bash
   git clone https://github.com/YOUR_USERNAME/ComeAndFix.git
   cd ComeAndFix
   ```

3. **Add upstream remote**
   ```bash
   git remote add upstream https://github.com/ComeAndFix/ComeAndFix.git
   ```

4. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

5. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

---

## 💡 How to Contribute

### Reporting Bugs

If you find a bug, please create an issue with:
- Clear, descriptive title
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Environment details (PHP version, OS, etc.)

### Suggesting Features

Feature suggestions are welcome! Please include:
- Clear description of the feature
- Why it would be useful
- Possible implementation approach

### Code Contributions

We welcome contributions for:
- Bug fixes
- New features
- Documentation improvements
- Performance optimizations
- UI/UX enhancements

---

## 🔄 Development Workflow

1. **Create a new branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

2. **Make your changes**
   - Write clean, readable code
   - Follow existing code style
   - Add comments where necessary

3. **Test your changes**
   ```bash
   php artisan test
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: add new feature"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Pull Request**
   - Go to the original repository
   - Click "New Pull Request"
   - Select your branch
   - Fill in the PR template

---

## 📝 Coding Standards

### PHP/Laravel

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use Laravel best practices
- Run Laravel Pint for code formatting:
  ```bash
  ./vendor/bin/pint
  ```

### JavaScript

- Use ES6+ syntax
- Keep functions small and focused
- Use meaningful variable names

### CSS/TailwindCSS

- Use Tailwind utility classes when possible
- Keep custom CSS minimal
- Follow mobile-first approach

### Database

- Always create migrations for schema changes
- Use descriptive migration names
- Add indexes for frequently queried columns

---

## 📌 Commit Guidelines

We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>: <description>

[optional body]
[optional footer]
```

### Types:

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

### Examples:

```bash
feat: add order filtering by status
fix: resolve chat message duplication issue
docs: update installation instructions
style: format payment controller
refactor: optimize tukang search query
```

---

## 🔍 Pull Request Process

1. **Before submitting:**
   - Ensure all tests pass
   - Run code formatter (`./vendor/bin/pint`)
   - Update documentation if needed
   - Rebase on latest `development` branch

2. **PR Requirements:**
   - Clear title describing the change
   - Detailed description of what and why
   - Reference related issues (if any)
   - Screenshots for UI changes

3. **Review Process:**
   - At least one maintainer approval required
   - Address review comments
   - Keep PR focused on single concern

4. **After Approval:**
   - Maintainers will merge your PR
   - Your contribution will be credited

---

## 🌿 Branch Naming

- `feature/feature-name` - New features
- `fix/bug-description` - Bug fixes
- `docs/what-changed` - Documentation
- `refactor/what-refactored` - Code refactoring

---

## ✅ Checklist Before Submitting

- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings or errors
- [ ] Tests added/updated
- [ ] All tests passing
- [ ] Branch is up to date with `development`

---

## 🧪 Testing

Run tests before submitting:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TestName

# Clear cache before testing
php artisan config:clear
php artisan cache:clear
```

---

## 📞 Questions?

If you have questions:
- Open an issue for discussion
- Check existing issues and PRs
- Review the README.md

---

## 🙏 Thank You!

Your contributions help make Come&Fix better for everyone. We appreciate your time and effort!

---

**Note**: This is a thesis project. All contributions are for educational purposes and will be properly credited.
