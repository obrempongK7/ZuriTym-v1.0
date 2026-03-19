package com.zuritym.app.utils;

import android.content.Context;
import android.content.SharedPreferences;
import com.google.gson.Gson;
import com.zuritym.app.models.UserData;

public class PrefManager {
    private static final String PREF_NAME = "ZuriTymPrefs";
    private static final String KEY_TOKEN   = "auth_token";
    private static final String KEY_USER    = "user_data";
    private static final String KEY_LOGGED  = "is_logged_in";
    private static final String KEY_DEVICE  = "device_id";

    private static SharedPreferences prefs;
    private static Gson gson = new Gson();

    public static void init(Context context) {
        prefs = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
    }

    public static void saveToken(String token) {
        prefs.edit().putString(KEY_TOKEN, token).apply();
    }

    public static String getToken() {
        return prefs.getString(KEY_TOKEN, "");
    }

    public static void saveUser(UserData user) {
        prefs.edit().putString(KEY_USER, gson.toJson(user)).putBoolean(KEY_LOGGED, true).apply();
    }

    public static UserData getUser() {
        String json = prefs.getString(KEY_USER, null);
        return json != null ? gson.fromJson(json, UserData.class) : null;
    }

    public static boolean isLoggedIn() {
        return prefs.getBoolean(KEY_LOGGED, false) && !getToken().isEmpty();
    }

    public static void saveDeviceId(String id) {
        prefs.edit().putString(KEY_DEVICE, id).apply();
    }

    public static String getDeviceId() {
        String id = prefs.getString(KEY_DEVICE, "");
        if (id.isEmpty()) {
            id = java.util.UUID.randomUUID().toString();
            saveDeviceId(id);
        }
        return id;
    }

    public static void logout() {
        String deviceId = getDeviceId();
        prefs.edit().clear().apply();
        saveDeviceId(deviceId);
    }

    public static double getBalance() {
        UserData u = getUser();
        return (u != null && u.wallet != null) ? u.wallet.balance : 0;
    }
}
