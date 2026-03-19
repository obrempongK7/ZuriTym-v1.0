package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
import java.util.List;
public class SpinConfig {
    @SerializedName("segments")    public List<SpinSegment> segments;
    @SerializedName("daily_limit") public int dailyLimit;
    public static class SpinSegment {
        @SerializedName("id")          public int id;
        @SerializedName("label")       public String label;
        @SerializedName("points")      public double points;
        @SerializedName("type")        public String type;
        @SerializedName("color")       public String color;
        @SerializedName("probability") public int probability;
    }
}