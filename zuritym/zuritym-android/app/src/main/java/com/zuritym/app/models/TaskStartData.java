package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class TaskStartData {
    @SerializedName("user_task_id") public int userTaskId;
    @SerializedName("timer")        public int timer;
    @SerializedName("action_url")   public String actionUrl;
}