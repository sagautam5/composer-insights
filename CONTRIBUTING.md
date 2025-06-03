# Contributing

Contributions are **welcome** and will be fully **credited**.

Please read and understand the contribution guide before creating an issue or submitting a pull request.

## Etiquette

This project is open source, and its maintainers generously volunteer their time to build and maintain the codebase. They make it freely available in the hope that it will be helpful to others.

Please be respectful and considerate when opening issues or submitting pull requests. Let’s demonstrate that developers are thoughtful, professional, and collaborative.

Maintainers are responsible for ensuring that all submissions meet the quality standards of the project. Developers have different skill sets and perspectives—respect the maintainer’s decisions and avoid negative or abusive behavior if your contribution isn’t accepted.

## Viability

Before requesting or submitting a new feature, consider whether it will be useful to others. Open source projects serve a wide range of developers with diverse needs, so it's important to think about how your suggestion benefits the broader community.

## Procedure

### Before filing an issue

- Try to reproduce the issue to confirm it wasn't a one-time error.
- Ensure your feature request or issue hasn’t already been addressed in the project.
- Check the **Pull Requests** tab to see if a fix or feature is already in progress.

### Before submitting a pull request

1. Install the required dependencies:

    ```sh
    composer install
    ```

2. Make sure your code doesn't break the existing workflow by running the test suite:

    ```sh
    vendor/bin/pest
    ```

3. Ensure the following:
    - Your feature or fix doesn't already exist in the codebase.
    - No one else has already submitted the same feature or fix.
    - The code passes all tests in different environments.
    <!-- - The CI pipeline passes after your updates. -->
    <!-- - Send pull request to the `dev` branch only. -->

## Requirements

If the project maintainer has additional requirements, they will be listed here.
<!-- 
- **Add tests!** – Your patch won’t be accepted if it doesn’t include tests.
 -->

- **Document any behavior changes** – Update the `README.md` or any other relevant documentation accordingly.
- **One pull request per feature** – If you’re implementing multiple features, submit them as separate pull requests.
- **Send a clean commit history** – Each commit should be meaningful. If you've made several intermediate commits, [squash them](https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

**Happy coding!** 🚀
