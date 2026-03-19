package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
public class ScratchCardData {
    @SerializedName("card_id")         public int cardId;
    @SerializedName("cards_remaining") public int cardsRemaining;
}