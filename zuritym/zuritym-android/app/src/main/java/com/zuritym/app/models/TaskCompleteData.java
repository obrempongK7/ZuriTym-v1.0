package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class TaskCompleteData {
    @SerializedName("status")        public String status;
    @SerializedName("points_earned") public double pointsEarned;
    @SerializedName("new_balance")   public double newBalance;
}