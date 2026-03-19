package com.zuritym.app.network;

import com.zuritym.app.models.*;
import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import retrofit2.Call;
import retrofit2.http.*;
import java.util.Map;

public interface ApiService {

    // ── Auth ─────────────────────────────────────────────────────
    @POST("auth/register")
    Call<ApiResponse<AuthData>> register(@Body Map<String, String> body);

    @POST("auth/login")
    Call<ApiResponse<AuthData>> login(@Body Map<String, String> body);

    @POST("auth/google")
    Call<ApiResponse<AuthData>> googleAuth(@Body Map<String, String> body);

    @GET("auth/me")
    Call<ApiResponse<UserData>> getProfile();

    @PUT("auth/profile")
    @Multipart
    Call<ApiResponse<UserData>> updateProfile(
            @Part("name") RequestBody name,
            @Part("username") RequestBody username,
            @Part MultipartBody.Part avatar
    );

    @POST("auth/logout")
    Call<ApiResponse<Object>> logout();

    // ── Home ─────────────────────────────────────────────────────
    @GET("home")
    Call<ApiResponse<HomeData>> getHome();

    @GET("app-settings")
    Call<ApiResponse<AppSettings>> getAppSettings();

    // ── Wallet ───────────────────────────────────────────────────
    @GET("wallet/balance")
    Call<ApiResponse<WalletData>> getWalletBalance();

    @GET("wallet/transactions")
    Call<ApiResponse<TransactionListData>> getTransactions(
            @Query("page") int page,
            @Query("type") String type
    );

    @POST("wallet/redeem-promo")
    Call<ApiResponse<PromoResult>> redeemPromoCode(@Body Map<String, String> body);

    @Multipart
    @POST("wallet/withdraw")
    Call<ApiResponse<Object>> requestWithdrawal(
            @Part("amount_points") RequestBody amount,
            @Part("payment_method") RequestBody method,
            @Part("payment_details[phone]") RequestBody phone,
            @Part MultipartBody.Part screenshot
    );

    @GET("wallet/withdrawals")
    Call<ApiResponse<WithdrawalListData>> getWithdrawals(@Query("page") int page);

    @GET("wallet/payment-methods")
    Call<ApiResponse<java.util.List<PaymentMethod>>> getPaymentMethods();

    // ── Tasks ────────────────────────────────────────────────────
    @GET("tasks")
    Call<ApiResponse<java.util.List<TaskData>>> getTasks(@Query("type") String type);

    @POST("tasks/{id}/start")
    Call<ApiResponse<TaskStartData>> startTask(@Path("id") int taskId);

    @Multipart
    @POST("tasks/{userTaskId}/complete")
    Call<ApiResponse<TaskCompleteData>> completeTask(
            @Path("userTaskId") int userTaskId,
            @Part MultipartBody.Part screenshot
    );

    // ── Spin ─────────────────────────────────────────────────────
    @GET("spin/config")
    Call<ApiResponse<SpinConfig>> getSpinConfig();

    @POST("spin/spin")
    Call<ApiResponse<SpinResult>> spin();

    // ── Scratch ──────────────────────────────────────────────────
    @POST("scratch/issue")
    Call<ApiResponse<ScratchCardData>> issueScratchCard();

    @POST("scratch/{id}/scratch")
    Call<ApiResponse<ScratchResult>> scratchCard(@Path("id") int cardId);

    // ── Offerwalls ───────────────────────────────────────────────
    @GET("offerwalls")
    Call<ApiResponse<java.util.List<OfferwallData>>> getOfferwalls();

    @GET("offerwalls/{id}/url")
    Call<ApiResponse<OfferwallUrlData>> getOfferwallUrl(@Path("id") int id);

    // ── Leaderboard ──────────────────────────────────────────────
    @GET("leaderboard")
    Call<ApiResponse<LeaderboardData>> getLeaderboard(@Query("period") String period);

    // ── Chat ─────────────────────────────────────────────────────
    @GET("chat/messages")
    Call<ApiResponse<java.util.List<ChatMessage>>> getChatMessages();

    @POST("chat/send")
    Call<ApiResponse<ChatMessage>> sendMessage(@Body Map<String, String> body);
}
