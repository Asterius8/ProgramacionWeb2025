package com.asterius.citas;

import android.content.Intent;
import android.os.Bundle;
import android.widget.TextView;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class crear_cuenta extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_crear_cuenta);

        // Abrir MainActivity cuando den clic en "Iniciar sesión"
        TextView tv = findViewById(R.id.lbl_iniciar_sesion);
        tv.setOnClickListener(v -> {
            Intent intent = new Intent(crear_cuenta.this, MainActivity.class);
            startActivity(intent);
            finish(); // Opcional
        });
    }
}
