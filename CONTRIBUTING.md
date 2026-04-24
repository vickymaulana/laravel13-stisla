# Contributing

Thanks for contributing to this project.

## Local quality checks
Make sure your PHP CLI has SQLite testing support enabled:
```bash
php -m | grep -E "pdo_sqlite|sqlite3"
```

Run before opening PR:
```bash
php artisan route:list --except-vendor
php artisan test
./vendor/bin/pint --test
npm run build
```

## Pull Request checklist
- [ ] Feature/bug scope is clear
- [ ] Tests added/updated for behavior changes
- [ ] No broken routes (`route:list` passes)
- [ ] Docs updated (`README.MD` / `QUICKSTART.md` if needed)
- [ ] No sensitive data committed

## Coding guidelines
- Follow PSR-12
- Keep controllers focused and validate requests
- Preserve backward compatibility where practical
- Prefer route grouping by domain
- Keep dependency upgrades within the current major version unless a release is explicitly planned as breaking

## Reporting issues
Include:
- Reproduction steps
- Expected vs actual behavior
- Laravel/PHP versions
- Relevant logs or stack traces
