# Online Users Integration Guide

This guide explains how to integrate the online users tracking feature with your Flutter app using WebSockets.

## Overview

The online users tracking system uses a custom WebSocket implementation that stores status in the database. This allows the admin dashboard to display which users are currently online, what screens they're viewing, and other useful analytics.

## API Endpoints

Mobile apps should call the following API endpoints to manage user online status:

```
POST /api/update-online-status
```
- Updates user online status
- Authorization: Bearer token required (user must be authenticated)

```
POST /api/logout-status
```
- Marks user as offline when logging out
- Authorization: Bearer token required (user must be authenticated)

```
GET /api/online-users-count
```
- Gets the current number of online users
- No authentication required

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

## Flutter Integration

### Setup in your Flutter app

Add the following dependencies to your `pubspec.yaml`:

```yaml
dependencies:
  web_socket_channel: ^2.4.0
  http: ^1.1.0
```

### Sample Implementation

```dart
import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class OnlineStatusService {
  static const String baseUrl = 'https://your-api-domain.com/api';
  static const Duration heartbeatInterval = Duration(minutes: 2);
  late String _token;
  late Timer _heartbeatTimer;
  
  // Initialize with user token
  void initialize(String token) {
    _token = token;
    _startHeartbeat();
  }
  
  // Start periodic heartbeat
  void _startHeartbeat() {
    // Send initial status update
    updateOnlineStatus();
    
    // Set up periodic timer
    _heartbeatTimer = Timer.periodic(heartbeatInterval, (timer) {
      updateOnlineStatus();
    });
  }
  
  // Update online status
  Future<void> updateOnlineStatus({String? currentScreen}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/update-online-status'),
        headers: {
          'Authorization': 'Bearer $_token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'app_version': '1.0.0', // Replace with your app version
          'device_info': 'Flutter App', // Replace with device info
          'screen': currentScreen ?? 'Unknown',
        }),
      );
      
      if (response.statusCode != 200) {
        print('Failed to update online status: ${response.body}');
      }
    } catch (e) {
      print('Error updating online status: $e');
    }
  }
  
  // Mark user as offline on logout
  Future<void> markOffline() async {
    try {
      // Cancel heartbeat timer
      _heartbeatTimer.cancel();
      
      // Send offline status
      await http.post(
        Uri.parse('$baseUrl/logout-status'),
        headers: {
          'Authorization': 'Bearer $_token',
          'Content-Type': 'application/json',
        },
      );
    } catch (e) {
      print('Error marking user offline: $e');
    }
  }
  
  // Clean up resources
  void dispose() {
    _heartbeatTimer.cancel();
  }
}
```

### Usage in your app

```dart
class MyApp extends StatefulWidget {
  @override
  _MyAppState createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> with WidgetsBindingObserver {
  final OnlineStatusService _onlineStatusService = OnlineStatusService();
  
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    
    // Initialize with user token
    _onlineStatusService.initialize('YOUR_USER_TOKEN');
  }
  
  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _onlineStatusService.dispose();
    super.dispose();
  }
  
  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      // App came to foreground
      _onlineStatusService.updateOnlineStatus();
    } else if (state == AppLifecycleState.paused) {
      // App went to background
      _onlineStatusService.updateOnlineStatus(currentScreen: 'background');
    }
  }
  
  // When navigating to a new screen
  void _navigateToScreen(String screenName) {
    // Update current screen
    _onlineStatusService.updateOnlineStatus(currentScreen: screenName);
    
    // Navigate to screen
    // ...
  }
  
  // When logging out
  void _logout() {
    // Mark user as offline
    _onlineStatusService.markOffline();
    
    // Perform logout actions
    // ...
  }
  
  @override
  Widget build(BuildContext context) {
    // Build your app UI
    return MaterialApp(
      // ...
    );
  }
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

When a user logs out, you should send one final request to mark them as offline:

```
POST /api/logout-status
```

## Benefits

This integration enables:

1. Real-time user online status on the admin dashboard
2. Analytics about most active screens and user engagement
3. Better customer support as admins can see who is currently using the app
4. Improved targeting for notifications based on active users

## Privacy Considerations

Users are not informed explicitly about online status tracking. If required by your privacy policy, you may want to add a notification or toggle in the settings to allow users to opt out of online status tracking.

## Testing

To test this integration, you can:

1. Log in to the mobile app
2. Check the admin dashboard to see if your user appears in the "Online Users" section
3. Navigate between screens and verify that the "Last Active" and screen information updates
4. Put the app in the background and verify that you eventually appear offline
5. Log out and verify that you immediately appear offline