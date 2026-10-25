# Contributing

## How changes reach this repository

This repository is published from a private repository: every commit named "Sync from private repository" replaces the
whole tree. A pull request here is therefore not merged directly. If it is accepted, the change is applied in the
private repository, arrives here with the next sync, and the pull request is closed with a note pointing to that sync.

Issues are the easiest way to report a bug or a wrong answer in an exercise.

## What can be contributed

- **Code** (PHP, JavaScript, CSS, tests, tooling) is licensed AGPL-3.0. By opening a pull request you agree that your
  contribution is released under the same license.
- **Teaching content** (`app/config/pek/**`, `app/config/formulas/**`, `app/config/bahasa/**`) is all rights reserved
  (`CONTENT-LICENSE.md`). Please report mistakes in an issue instead of sending a pull request.

## Before opening a pull request

CI runs these, so running them first saves a round trip:

```bash
find app dev -name '*.php' -print0 | xargs -0 -n1 php -l   # PHP syntax (8.2 and 8.3)
node tests/enjin.test.js app                                # formula engine against real Excel results
php app/alat/semak-kandungan.php                            # Malay and English content have the same structure
cd tailwind && npm install && npm run build && npm run icons   # only if you changed classes or icons; commit the output
```

Every interface string exists in Malay and English (`app/config/bahasa/{ms,en}.php`); a change to one needs the other.
Writing rules for both languages (written in Malay): `docs/STYLE-GUIDE.md`.

## Security

Do not open public issues for vulnerabilities. See `SECURITY.md`.
