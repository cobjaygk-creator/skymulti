# TikTok RSS scheduler

This module is isolated from Gnuboard and Rebuilder core files.

## Schedule

Run at midnight and noon (Asia/Seoul):

```cron
0 0,12 * * * /usr/bin/php /hosting/path/plugin/tiktok_rss/cron.php
```

If CLI cron is unavailable, register this HTTPS URL with a web-cron service:

```text
https://www.skymulty.co.kr/plugin/tiktok_rss/cron.php?token=TOKEN_FROM_DATA_CONFIG
```

The token is stored in `data/tiktok_rss.config.php`. Execution logs and the
non-blocking concurrency lock are created under `data/tiktok_rss/`.

## Rollback

1. Disable the scheduler.
2. Set `enabled` to `false` in `data/tiktok_rss.config.php`.
3. In the board skin, remove the new manual synchronization block and change
   the disabled legacy condition back to its original condition.
4. Remove this plugin directory and the config file when no longer needed.
