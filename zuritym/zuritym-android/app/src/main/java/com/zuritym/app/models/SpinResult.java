package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class SpinResult {
    @SerializedName("reward")          public Reward reward;
    @SerializedName("points_won")      public double pointsWon;
    @SerializedName("spins_remaining") public int spinsRemaining;
    @SerializedName("new_balance")     public double newBalance;
    public static class Reward {
        @SerializedName("id")     public int id;
        @SerializedName("label")  public String label;
        @SerializedName("points") public double points;
        @SerializedName("type")   public String type;
        @SerializedName("color")  public String color;
    }
}