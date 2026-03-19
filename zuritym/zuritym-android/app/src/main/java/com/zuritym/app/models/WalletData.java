package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class WalletData {
    @SerializedName("balance")            public double balance;
    @SerializedName("total_earned")       public double totalEarned;
    @SerializedName("total_withdrawn")    public double totalWithdrawn;
    @SerializedName("pending_withdrawal") public double pendingWithdrawal;
    @SerializedName("bonus_balance")      public double bonusBalance;
    @SerializedName("is_locked")          public boolean isLocked;
    @SerializedName("lock_reason")        public String lockReason;
}