# Commit Message Convention

## Format

Every commit message consists of:

-   **header** (mandatory)
-   **body** (optional)
-   **footer** (optional)

```
<type>(<scope>): <subject>

[optional body]

[optional footer(s)]
```

## Header: `<type>(<scope>): <subject>`

### Type (mandatory)

Must be one of:

-   `feat`: A new feature
-   `fix`: A bug fix
-   `docs`: Documentation only changes
-   `style`: Changes that do not affect the meaning of the code (white-space, formatting, etc)
-   `refactor`: A code change that neither fixes a bug nor adds a feature
-   `perf`: A code change that improves performance
-   `test`: Adding missing tests or correcting existing tests
-   `chore`: Changes to the build process or auxiliary tools
-   `ci`: Changes to CI configuration files and scripts
-   `revert`: Reverts a previous commit

### Scope (mandatory)

Must be one of:

-   `auth`: Authentication related
-   `tasks`: Task management
-   `lawn`: Lawn features
-   `garden`: Garden features
-   `ui`: User interface
-   `db`: Database
-   `config`: Configuration

### Subject (mandatory)

-   Use the imperative, present tense: "change" not "changed" nor "changes"
-   Don't capitalize the first letter
-   No dot (.) at the end
-   Maximum 50 characters

## Body (optional)

-   Use imperative, present tense
-   Include motivation for the change
-   Contrast this with previous behavior
-   Wrap at 72 characters

## Footer (optional)

-   Reference issues the commit closes
-   Mark breaking changes
-   Note deprecated features

## Examples

### Simple Feature

```
feat(lawn): add automatic watering detection
```

### Bug Fix with Body

```
fix(auth): resolve login timeout issue

The session token wasn't being refreshed properly,
causing unexpected logouts. Updated the token
refresh logic to handle background requests.

Fixes #123
```

### Breaking Change

```
feat(api): update lawn monitoring endpoint

BREAKING CHANGE: lawn monitoring now requires weather data
input. Previous implementations need to be updated to
include temperature and humidity data.
```

### Multiple Fixes

```
fix(tasks): correct multiple scheduling issues

- Fixed overlapping tasks
- Resolved timezone conversion
- Updated validation logic

Fixes #234, #235
```

## Common Errors & Solutions

### ❌ Invalid

```
"Added new feature"          // Missing type and scope
"feat: new feature"         // Missing scope
"FEAT(lawn): New Feature"   // Wrong capitalization
"fix(lawn): fixed bug."     // Wrong tense and ends with period
```

### ✅ Valid

```
"feat(lawn): add weather monitoring"
"fix(auth): resolve login issues"
"docs(readme): update installation steps"
"refactor(garden): extract plant service"
```

## Setup & Tools

### Install Dependencies

```bash
npm install --save-dev @commitlint/cli @commitlint/config-conventional husky lint-staged
```

### Initialize Commit Validation

```bash
# Create an npm script for husky install
npm pkg set scripts.prepare="husky"

# Initialize husky
npx husky install

# Create commit-msg hook
echo '#!/usr/bin/env sh
. "$(dirname -- "$0")/_/husky.sh"

npx --no-install commitlint --edit "$1"' > .husky/commit-msg

# Make it executable
chmod +x .husky/commit-msg
```

## Editor Integration

### VSCode

Install the "Conventional Commits" extension for:

-   Commit message templates
-   Type/scope validation
-   Real-time feedback

### IntelliJ/WebStorm

Install the "Conventional Commit" plugin for:

-   Commit message formatting
-   Template selection
-   Validation

## Additional Resources

-   [Conventional Commits](https://www.conventionalcommits.org/)
-   [Angular Commit Message Guidelines](https://github.com/angular/angular/blob/master/CONTRIBUTING.md#commit)
-   [Semantic Versioning](https://semver.org/)
