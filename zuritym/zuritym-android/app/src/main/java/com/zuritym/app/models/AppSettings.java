package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class AppSettings {
    @SerializedName("app_name")          public String appName;
    @SerializedName("min_withdrawal")    public double minWithdrawal;
    @SerializedName("referral_reward")   public double referralReward;
    @SerializedName("spin_daily_limit")  public int spinDailyLimit;
    @SerializedName("scratch_daily_limit") public int scratchDailyLimit;
    @SerializedName("privacy_url")       public String privacyUrl;
    @SerializedName("terms_url")         public String termsUrl;
    @SerializedName("contact_email")     public String contactEmail;
    @SerializedName("maintenance_mode")  public String maintenanceMode;
}