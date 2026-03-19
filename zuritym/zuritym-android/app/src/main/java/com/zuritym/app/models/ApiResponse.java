package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class ApiResponse<T> {
    @SerializedName("success") public boolean success;
    @SerializedName("message") public String message;
    @SerializedName("data")    public T data;
}