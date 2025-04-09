# Online Users Integration Guide

This guide explains how to integrate the online users tracking feature with your mobile app.

## Overview

The online users tracking system uses Firebase Realtime Database to track user presence in real-time. This allows the admin dashboard to display which users are currently online, what screens they're viewing, and other useful analytics.

## API Endpoint

Mobile apps should call the following API endpoint periodically to update user online status:

```
POST /api/update-online-status
```

Authorization: Bearer token required (user must be authenticated)

### Request Body (optional but recommended)

```json
{
  "app_version": "1.2.3",
  "device_info": "iPhone 13, iOS 15.4",
  "screen": "MovieDetails"
}
```

- `app_version`: The version of the mobile app
- `device_info`: Information about the user's device
- `screen`: The current screen the user is viewing

### Response

```json
{
  "success": true
}
```

## Implementation Guidelines

### Frequency of Updates

- Call the endpoint when the app starts
- Call the endpoint when a user navigates to a new screen
- Set up a periodic heartbeat (every 2-3 minutes) while the app is in the foreground
- Update status when the app goes to background/foreground

### Background State

When the app goes to the background, you can either:

1. Send one last update with a special flag indicating background state:

```json
{
  "screen": "background",
  "app_state": "background"
}
```

2. Or stop sending updates, which will automatically mark the user as offline after 5 minutes of inactivity.

### Logout Handling

When a user logs out, you should send one final update to mark them as offline:

```
POST /api/update-online-status
```

With body:

```json
{
  "status": "offline"
}
```

## Benefits

This integration enables:

1. Real-time user online status on the admin dashboard
2. Analytics about most active screens and user engagement
3. Better customer support as admins can see who is currently using the app
4. Improved targeting for notifications based on active users

## Firebase Configuration

The app already uses the correct Firebase project. No additional configuration is needed for the mobile app to interact with this feature as it uses the existing authentication mechanism.

## Privacy Considerations

Users are not informed explicitly about online status tracking. If required by your privacy policy, you may want to add a notification or toggle in the settings to allow users to opt out of online status tracking.

## Testing

To test this integration, you can:

1. Log in to the mobile app
2. Check the admin dashboard to see if your user appears in the "Online Users" section
3. Navigate between screens and verify that the "Last Active" and screen information updates
4. Put the app in the background and verify that you eventually appear offline
5. Log out and verify that you immediately appear offline