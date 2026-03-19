package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class TaskData {
    @SerializedName("id")                 public int id;
    @SerializedName("title")              public String title;
    @SerializedName("description")        public String description;
    @SerializedName("type")               public String type;
    @SerializedName("reward_points")      public double rewardPoints;
    @SerializedName("action_url")         public String actionUrl;
    @SerializedName("timer_seconds")      public int timerSeconds;
    @SerializedName("daily_limit")        public int dailyLimit;
    @SerializedName("today_completed")    public int todayCompleted;
    @SerializedName("is_completable")     public boolean isCompletable;
    @SerializedName("requires_screenshot") public boolean requiresScreenshot;
    @SerializedName("icon_url")           public String iconUrl;
}