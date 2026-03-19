package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class ChatMessage {
    @SerializedName("id")      public int id;
    @SerializedName("message") public String message;
    @SerializedName("user")    public ChatUser user;
    @SerializedName("time")    public String time;
    public static class ChatUser {
        @SerializedName("id")         public int id;
        @SerializedName("name")       public String name;
        @SerializedName("avatar_url") public String avatarUrl;
    }
}