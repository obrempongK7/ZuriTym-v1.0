package com.zuritym.app.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.*;
import android.widget.*;
import androidx.fragment.app.Fragment;
import com.bumptech.glide.Glide;
import com.zuritym.app.R;
import com.zuritym.app.activities.LoginActivity;
import com.zuritym.app.models.UserData;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.PrefManager;
import de.hdodenhof.circleimageview.CircleImageView;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.zuritym.app.models.ApiResponse;

public class ProfileFragment extends Fragment {
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_profile, container, false);
        CircleImageView avatar = v.findViewById(R.id.ivAvatar);
        TextView tvName  = v.findViewById(R.id.tvName);
        TextView tvEmail = v.findViewById(R.id.tvEmail);
        TextView tvRef   = v.findViewById(R.id.tvReferralCode);

        UserData user = PrefManager.getUser();
        if (user != null) {
            tvName.setText(user.name);
            tvEmail.setText(user.email);
            tvRef.setText("Referral: " + user.referralCode);
            if (user.avatarUrl != null)
                Glide.with(this).load(user.avatarUrl).placeholder(R.drawable.ic_avatar).into(avatar);
        }

        v.findViewById(R.id.btnLogout).setOnClickListener(x -> {
            ApiClient.getApiService().logout().enqueue(new Callback<ApiResponse<Object>>() {
                @Override public void onResponse(Call<ApiResponse<Object>> c, Response<ApiResponse<Object>> r) {}
                @Override public void onFailure(Call<ApiResponse<Object>> c, Throwable t) {}
            });
            PrefManager.logout();
            ApiClient.resetClient();
            startActivity(new Intent(getContext(), LoginActivity.class));
            requireActivity().finishAffinity();
        });
        return v;
    }
}
