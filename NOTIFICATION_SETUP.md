# Notification System Setup Guide

This guide explains how to configure the server to handle large volumes of notifications without crashing.

## Issue Fixed

The notification system was previously causing server crashes when sending notifications to 2000+ users because:
1. All users were loaded at once in memory
2. Notifications were sent in small batches of 10
3. All notifications were processed synchronously

## Solution Implemented

1. Improved chunking (500 users per batch - Firebase's maximum)
2. Queued processing using Laravel's queue system
3. Better error handling and retry logic
4. Resource consumption limits

## Setup Instructions

### 1. Update Environment Configuration

Edit your `.env` file to use database queue instead of sync:

```
QUEUE_CONNECTION=database
```

### 2. Run Database Migrations

Make sure the jobs table exists in your database:

```bash
php artisan migrate
```

### 3. Setup Queue Worker using Supervisor

Install Supervisor if not already installed:

```bash
# For Ubuntu/Debian
sudo apt-get install supervisor

# For CentOS/RHEL
sudo yum install supervisor
```

Copy the Supervisor configuration:

```bash
sudo cp /Users/popcorn/Desktop/cinemagickh/film_api_new/notification-worker.conf /etc/supervisor/conf.d/
```

Edit the path in the configuration file to match your server's path to the Laravel application.

Start the Supervisor process:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cinemagickh-notifications:*
```

### 4. Monitor the Queue

You can monitor the queue with:

```bash
# View queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed
```

### 5. Testing

Test the notification system with:

```bash
# Process notifications in the foreground for debugging
php artisan notifications:worker --queue=notifications
```

## Troubleshooting

If notifications are still not being processed:

1. Check the logs: `storage/logs/notification-worker.log`
2. Verify that the supervisor process is running
3. Make sure the firebase_credentials.json file exists and is valid
4. Verify database connection for the queue

## Performance Considerations

- The system now processes notifications in batches of 500
- Each batch is a separate queue job, limiting memory usage
- Failed notifications are logged and can be retried
- Supervisor automatically restarts workers if they crash

For very large user bases (10,000+), you may want to further optimize by:
1. Adding more queue workers (increase numprocs in supervisor config)
2. Using Redis for queue management instead of database
3. Setting up a separate server for notification processing