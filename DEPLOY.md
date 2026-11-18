# Dokploy deployment

Create a Compose application in Dokploy from this repository and use
`docker-compose.yml`. Configure the domain against the `app` service on port
`80`.

Set these required environment variables in Dokploy:

```dotenv
APP_KEY=base64:REPLACE_WITH_A_GENERATED_KEY
APP_URL=https://your-domain.example
DB_PASSWORD=REPLACE_WITH_A_LONG_RANDOM_PASSWORD
```

Generate `APP_KEY` locally instead of inventing one:

```bash
php artisan key:generate --show
```

Optional variables include `APP_NAME`, `APP_TIMEZONE`, `DB_DATABASE`,
`DB_USERNAME`, `LOG_LEVEL`, `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, and
`MAIL_FROM_NAME`. The Compose defaults use PostgreSQL for the database and for
Laravel sessions, cache, and queues.

The application container runs migrations on startup. Do not run database
seeders in production. PostgreSQL and application storage use named volumes,
so configure backups for `postgres_data` and `app_storage` in Dokploy.

After deployment, verify `https://your-domain.example/up` and inspect the app
logs for successful migrations and Supervisor startup.
