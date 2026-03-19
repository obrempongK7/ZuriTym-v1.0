package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
import java.util.List;
public class LeaderboardData {
    @SerializedName("leaders") public List<LeaderEntry> leaders;
    @SerializedName("my_rank") public int myRank;
    @SerializedName("period")  public String period;
    public static class LeaderEntry {
        @SerializedName("rank")         public int rank;
        @SerializedName("user_id")      public int userId;
        @SerializedName("name")         public String name;
        @SerializedName("username")     public String username;
        @SerializedName("avatar_url")   public String avatarUrl;
        @SerializedName("total_points") public double totalPoints;
    }
}