package com.zuritym.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.MenuItem;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;
import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.zuritym.app.R;
import com.zuritym.app.fragments.*;

public class MainActivity extends AppCompatActivity {
    private BottomNavigationView bottomNav;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        bottomNav = findViewById(R.id.bottomNav);
        bottomNav.setOnItemSelectedListener(this::onNavItemSelected);
        loadFragment(new HomeFragment());
    }

    private boolean onNavItemSelected(@NonNull MenuItem item) {
        Fragment f;
        int id = item.getItemId();
        if (id == R.id.nav_home)        f = new HomeFragment();
        else if (id == R.id.nav_tasks)  f = new TasksFragment();
        else if (id == R.id.nav_earn)   f = new EarnFragment();
        else if (id == R.id.nav_wallet) f = new WalletFragment();
        else f = new ProfileFragment();
        return loadFragment(f);
    }

    private boolean loadFragment(Fragment f) {
        getSupportFragmentManager().beginTransaction().replace(R.id.fragmentContainer, f).commit();
        return true;
    }
}
