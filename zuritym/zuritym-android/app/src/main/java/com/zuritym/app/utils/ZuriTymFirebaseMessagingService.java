package com.zuritym.app.utils;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.os.Build;
import androidx.core.app.NotificationCompat;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import com.zuritym.app.R;
import com.zuritym.app.activities.MainActivity;

public class ZuriTymFirebaseMessagingService extends FirebaseMessagingService {
    private static final String CHANNEL_ID = "zuritym_channel";

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        String title = "ZuriTym";
        String body  = "";
        if (remoteMessage.getNotification() != null) {
            title = remoteMessage.getNotification().getTitle();
            body  = remoteMessage.getNotification().getBody();
        } else if (remoteMessage.getData().containsKey("title")) {
            title = remoteMessage.getData().get("title");
            body  = remoteMessage.getData().getOrDefault("body", "");
        }
        showNotification(title, body);
    }

    private void showNotification(String title, String body) {
        Intent intent = new Intent(this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        PendingIntent pi = PendingIntent.getActivity(this, 0, intent,
                PendingIntent.FLAG_ONE_SHOT | PendingIntent.FLAG_IMMUTABLE);

        NotificationCompat.Builder builder = new NotificationCompat.Builder(this, CHANNEL_ID)
                .setSmallIcon(R.drawable.ic_notification)
                .setContentTitle(title).setContentText(body)
                .setAutoCancel(true).setSound(RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION))
                .setContentIntent(pi);

        NotificationManager nm = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel ch = new NotificationChannel(CHANNEL_ID, "ZuriTym", NotificationManager.IMPORTANCE_HIGH);
            nm.createNotificationChannel(ch);
        }
        nm.notify((int) System.currentTimeMillis(), builder.build());
    }

    @Override
    public void onNewToken(String token) {
        // Update FCM token on server if user is logged in
        if (PrefManager.isLoggedIn()) {
            // TODO: call API to update FCM token
        }
    }
}
