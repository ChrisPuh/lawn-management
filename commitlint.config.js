// commitlint.config.js
module.exports = {
    extends: ["@commitlint/config-conventional"],
    rules: {
        "type-enum": [
            2,
            "always",
            [
                "feat",
                "fix",
                "docs",
                "style",
                "refactor",
                "perf",
                "test",
                "chore",
                "ci",
                "revert",
            ],
        ],
        "scope-enum": [
            2,
            "always",
            ["auth", "tasks", "lawn", "garden", "ui", "db", "config"],
        ],
        "scope-empty": [2, "never"],
        "subject-empty": [2, "never"],
        "subject-full-stop": [2, "never", "."],
        "subject-case": [
            2,
            "never",
            ["sentence-case", "start-case", "pascal-case", "upper-case"],
        ],
    },
};
