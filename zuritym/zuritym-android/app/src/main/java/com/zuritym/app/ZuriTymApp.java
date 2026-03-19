package com.zuritym.app;

import android.app.Application;
import android.content.Context;
import androidx.multidex.MultiDex;
import com.google.android.gms.ads.MobileAds;
import com.zuritym.app.utils.PrefManager;
import com.zuritym.app.utils.AdManager;

public class ZuriTymApp extends Application {

    private static ZuriTymApp instance;

    public static ZuriTymApp getInstance() { return instance; }

    @Override
    public void onCreate() {
        super.onCreate();
        instance = this;

        // Initialize AdMob
        MobileAds.initialize(this, initializationStatus -> {});

        // Load app settings from server on first launch
        PrefManager.init(this);
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }
}
