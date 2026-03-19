package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class OfferwallData {
    @SerializedName("id")              public int id;
    @SerializedName("name")            public String name;
    @SerializedName("slug")            public String slug;
    @SerializedName("type")            public String type;
    @SerializedName("icon_url")        public String iconUrl;
    @SerializedName("conversion_rate") public double conversionRate;
    @SerializedName("url")             public String url;
}