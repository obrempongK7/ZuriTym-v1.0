package com.zuritym.app.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.*;
import android.widget.*;
import androidx.fragment.app.Fragment;
import com.zuritym.app.R;
import com.zuritym.app.activities.*;
import com.zuritym.app.utils.*;

public class WalletFragment extends Fragment {
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_wallet, container, false);
        TextView tvBalance = v.findViewById(R.id.tvBalance);
        tvBalance.setText(AppUtils.formatPoints(PrefManager.getBalance()));
        v.findViewById(R.id.btnViewWallet).setOnClickListener(x -> startActivity(new Intent(getContext(), WalletActivity.class)));
        v.findViewById(R.id.btnWithdraw).setOnClickListener(x -> startActivity(new Intent(getContext(), WithdrawActivity.class)));
        return v;
    }
}
