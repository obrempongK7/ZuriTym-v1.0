package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class UserData {
    @SerializedName("id")             public int id;
    @SerializedName("name")           public String name;
    @SerializedName("username")       public String username;
    @SerializedName("email")          public String email;
    @SerializedName("phone")          public String phone;
    @SerializedName("avatar_url")     public String avatarUrl;
    @SerializedName("referral_code")  public String referralCode;
    @SerializedName("total_referrals") public int totalReferrals;
    @SerializedName("is_verified")    public boolean isVerified;
    @SerializedName("country")        public String country;
    @SerializedName("wallet")         public WalletData wallet;
    @SerializedName("rank")           public int rank;
    @SerializedName("joined_at")      public String joinedAt;
}