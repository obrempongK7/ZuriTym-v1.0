package com.zuritym.app.models;
import com.google.gson.annotations.SerializedName;
import java.util.List;
public class TransactionListData {
    @SerializedName("transactions") public List<TransactionData> transactions;
    @SerializedName("pagination")   public Pagination pagination;
    public static class Pagination {
        @SerializedName("current_page") public int currentPage;
        @SerializedName("last_page")    public int lastPage;
        @SerializedName("total")        public int total;
    }
}