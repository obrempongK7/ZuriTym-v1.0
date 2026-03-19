package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
import java.util.List;
public class HomeData {
    @SerializedName("user")             public HomeUser user;
    @SerializedName("featured_tasks")   public List<TaskData> featuredTasks;
    @SerializedName("banner")           public String banner;
    @SerializedName("announcement")     public String announcement;
    @SerializedName("spin_available")   public boolean spinAvailable;
    @SerializedName("scratch_available") public boolean scratchAvailable;
    public static class HomeUser {
        @SerializedName("name")       public String name;
        @SerializedName("balance")    public double balance;
        @SerializedName("rank")       public int rank;
        @SerializedName("avatar_url") public String avatarUrl;
    }
}