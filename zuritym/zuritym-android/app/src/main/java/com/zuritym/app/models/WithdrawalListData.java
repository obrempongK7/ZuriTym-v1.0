package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
import java.util.List;
public class WithdrawalListData {
    @SerializedName("withdrawals") public List<WithdrawalData> withdrawals;
}