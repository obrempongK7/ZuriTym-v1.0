package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class PaymentMethod {
    @SerializedName("id")             public int id;
    @SerializedName("name")           public String name;
    @SerializedName("slug")           public String slug;
    @SerializedName("icon_url")       public String iconUrl;
    @SerializedName("min_withdrawal") public double minWithdrawal;
    @SerializedName("conversion_rate") public double conversionRate;
}