package com.zuritym.app.utils;

import android.app.Activity;
import android.util.Log;
import com.google.android.gms.ads.*;
import com.google.android.gms.ads.interstitial.InterstitialAd;
import com.google.android.gms.ads.interstitial.InterstitialAdLoadCallback;
import com.google.android.gms.ads.rewarded.RewardedAd;
import com.google.android.gms.ads.rewarded.RewardedAdLoadCallback;

public class AdManager {
    private static final String TAG = "AdManager";
    private static InterstitialAd interstitialAd;
    private static RewardedAd rewardedAd;

    public static void loadInterstitial(Activity activity, String adUnitId) {
        AdRequest req = new AdRequest.Builder().build();
        InterstitialAd.load(activity, adUnitId, req, new InterstitialAdLoadCallback() {
            @Override public void onAdLoaded(InterstitialAd ad) { interstitialAd = ad; }
            @Override public void onAdFailedToLoad(LoadAdError e) { interstitialAd = null; Log.e(TAG, e.getMessage()); }
        });
    }

    public static void showInterstitial(Activity activity) {
        if (interstitialAd != null) { interstitialAd.show(activity); interstitialAd = null; }
    }

    public static void loadRewarded(Activity activity, String adUnitId) {
        AdRequest req = new AdRequest.Builder().build();
        RewardedAd.load(activity, adUnitId, req, new RewardedAdLoadCallback() {
            @Override public void onAdLoaded(RewardedAd ad) { rewardedAd = ad; }
            @Override public void onAdFailedToLoad(LoadAdError e) { rewardedAd = null; }
        });
    }

    public static void showRewarded(Activity activity, OnUserEarnedRewardListener listener) {
        if (rewardedAd != null) { rewardedAd.show(activity, listener); rewardedAd = null; }
    }

    public static boolean isRewardedReady() { return rewardedAd != null; }
}
