package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class AuthData {
    @SerializedName("token")   public String token;
    @SerializedName("user")    public UserData user;
    @SerializedName("is_new")  public boolean isNew;
}