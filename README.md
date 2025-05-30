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
+-------------------+--------------+----------------+-----------------+-------+-------+-----------+-------------+----------------+
| Package           | License      | Latest Version | Current Version | Stars | Forks | Downloads | Open Issues | Last Updated   |
+-------------------+--------------+----------------+-----------------+-------+-------+-----------+-------------+----------------+
| guzzlehttp/guzzle | MIT          | 7.9.3          | 7.9.3           | 23.4k | 2.4k  | 843M      | 31          | 1 day ago      |
| symfony/console   | MIT          | v7.3.0-RC1     | v7.2.6          | 9.8k  | 264   | 930.7M    | 1           | 22 hours ago   |
| vlucas/phpdotenv  | BSD-3-Clause | v5.6.2         | v5.6.2          | 13.4k | 644   | 502.6M    | 13          | 48 minutes ago |
| pestphp/pest      | MIT          | v3.8.2         | v3.8.2          | 10.3k | 386   | 32.8M     | 167         | 1 hour ago     |
+-------------------+--------------+----------------+-----------------+-------+-------+-----------+-------------+----------------+

✅ Done

```

---

## 📥 Installation

You can install it in any Composer-based local PHP project as dev dependency:

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

### 🔧 Command Options

#### Development Dependencies

You can control whether to include or exclude development dependencies using these options:

- `--dev`: Include development dependencies in the analysis
- `--no-dev`: Exclude development dependencies from the analysis

By default, all dependencies will be considered


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
🔗 [github.com/sagautam5](https://github.com/sagautam5)

---

## 🙌 Contributors

- **Sagar Gautam** – Creator & Maintainer
- **ALL Contributors** 



Want to contribute? Feel free to submit a PR or open an issue!

---

## 🪪 License

This package is open-sourced under the [MIT License](LICENSE).

---

## Contributing
Want to contribute to Composer Insights? Please read our [Contributing Guide](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

We welcome all contributions, whether it's:

- Reporting a bug
- Discussing the current state of the code
- Submitting a fix
- Proposing new features
- Becoming a maintainer


## 💬 Feedback & Issues

If you find a bug or want to suggest an improvement:

- Open an issue on GitHub
- Email me at [sagautam5@gmail.com](mailto:sagautam5@gmail.com)

---

Enjoy analyzing your dependencies! 🎉

