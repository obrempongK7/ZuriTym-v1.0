package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class TransactionData {
    @SerializedName("id")          public int id;
    @SerializedName("txn_id")      public String txnId;
    @SerializedName("amount")      public double amount;
    @SerializedName("formatted")   public String formatted;
    @SerializedName("type")        public String type;
    @SerializedName("status")      public String status;
    @SerializedName("description") public String description;
    @SerializedName("created_at")  public String createdAt;
}