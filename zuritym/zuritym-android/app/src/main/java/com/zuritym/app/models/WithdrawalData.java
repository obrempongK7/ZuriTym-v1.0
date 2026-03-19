package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class WithdrawalData {
    @SerializedName("id")             public int id;
    @SerializedName("withdrawal_id")  public String withdrawalId;
    @SerializedName("amount_points")  public double amountPoints;
    @SerializedName("amount_cash")    public double amountCash;
    @SerializedName("payment_method") public String paymentMethod;
    @SerializedName("status")         public String status;
    @SerializedName("admin_note")     public String adminNote;
    @SerializedName("created_at")     public String createdAt;
}