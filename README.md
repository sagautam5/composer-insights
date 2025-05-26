# 📦 Composer Insights

[![GitHub stars](https://img.shields.io/github/stars/sagautam5/composer-insights?style=social)](https://github.com/sagautam5/composer-insights/stargazers)
[![Packagist Version](https://img.shields.io/packagist/v/composer-insights/composer-insights.svg)](https://packagist.org/packages/composer-insights/composer-insights)
[![Packagist Downloads](https://img.shields.io/packagist/dt/composer-insights/composer-insights.svg)](https://packagist.org/packages/composer-insights/composer-insights)
[![GitHub Actions Status](https://github.com/sagautam5/composer-insights/actions/workflows/ci.yml/badge.svg)](https://github.com/sagautam5/composer-insights/actions/workflows/ci.yml)

**Composer Insights** is a CLI tool that analyzes your PHP project's composer dependencies and provides insightful GitHub and Packagist statistics for each top-level dependency.

---

## ✨ Features

- 🔍 Analyze direct dependencies from composer
- ⭐ Fetch GitHub repository data: stars, forks, open issues, last update
- 📈 Get download stats from Packagist
- 🧹 Clean tabular output in the terminal
- 🛑 Gracefully skips non-GitHub packages
- ⚡ Fast, dependency-free CLI experience

---

## 💡Example Output

```
🔍 Fetching Composer Dependency Insights

+-------------------+-------+-------+-------------+-----------+----------------+
| Package           | Stars | Forks | Open Issues | Downloads | Last Updated   |
+-------------------+-------+-------+-------------+-----------+----------------+
| guzzlehttp/guzzle | 23.4k | 2.4k  | 31          | 842M      | 23 minutes ago |
| symfony/console   | 9.8k  | 264   | 1           | 929.4M    | 2 days ago     |
| vlucas/phpdotenv  | 13.4k | 644   | 13          | 501.9M    | 7 hours ago    |
| pestphp/pest      | 10.3k | 386   | 167         | 32.6M     | 5 hours ago    |
+-------------------+-------+-------+-------------+-----------+----------------+

✅ Done
```

---

## 📥 Installation

You can install it in any Composer-based local PHP project:

```bash
composer require composer-insights/composer-insights --dev
```

Make sure your project has both `composer.json` and `composer.lock` files.

To avoid GitHub API rate limits and to enable access to private repos, set your GitHub token as an environment variable:

```bash
export GITHUB_TOKEN=your_github_token
```

You can generate a personal access token from [https://github.com/settings/tokens](https://github.com/settings/tokens)

---

## 🧪 Usage

Run the following command to start the analysis:

```bash
vendor/bin/composer-insights analyze
```

This will:

- Parse your composer file
- Check each direct dependency (ignores transitive)
- Fetch GitHub and Packagist stats
- Display a beautiful CLI table

---

## 📋 Requirements

- PHP 8.0 or higher
- A Composer-based project
- Internet connection

---

## 🧑‍💻 Author

**Sagar Gautam**  
📧 [sagautam5@gmail.com](mailto:sagautam5@gmail.com)  
🏠 Pokhara, Nepal  
🔗 [github.com/sagautam5](https://github.com/sagautam5)

---

## 🙌 Contributors

Thank you to the following contributor(s):

- **Sagar Gautam** – Creator & Maintainer

Want to contribute? Feel free to submit a PR or open an issue!

---

## 🪪 License

This package is open-sourced under the [MIT License](LICENSE).

---

## 💬 Feedback & Issues

If you find a bug or want to suggest an improvement:

- Open an issue on GitHub
- Email me at [sagautam5@gmail.com](mailto:sagautam5@gmail.com)

---

Enjoy analyzing your dependencies! 🎉
