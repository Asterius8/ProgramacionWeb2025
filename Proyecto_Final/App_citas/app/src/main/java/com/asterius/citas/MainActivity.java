package com.asterius.citas;

import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.Network;
import android.os.Bundle;
import android.util.Log;
import android.util.Patterns;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

import controller.AnalizadorJSON;

public class MainActivity extends AppCompatActivity {

    Button btn_aceptar;
    EditText correo, contra;
    String metodo = "POST";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_main);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        //Abrir la activity de crear cuenta mediante el click a la etiqueta
        TextView tv = findViewById(R.id.lbl_abrir_registro);
        tv.setOnClickListener(v -> {
            Intent intent = new Intent(MainActivity.this, crear_cuenta.class);
            startActivity(intent);
        });

        // Inicializar componentes
        btn_aceptar = findViewById(R.id.btn_ingresar);
        correo = findViewById(R.id.txt_email);
        contra = findViewById(R.id.txt_password);

        btn_aceptar.setOnClickListener(v -> {

            String email = correo.getText().toString();
            String contraseña = contra.getText().toString();

            //Validaciones correo
            if (!Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
                correo.setError("Correo inválido");
                return;
            } else if (email.isEmpty()) {
                correo.setError("El correo no puede estar vacío");
                return;
            }

            //Validaciones contraseña
            if(contraseña.isEmpty()){
                contra.setError("La contraseña no puede estar vacia");
                return;
            } else if (contraseña.length() < 6) {
                contra.setError("La contraseña no tener menos de 6");
                return;
            } else if (contraseña.length() > 20) {
                contra.setError("La contraseña no tener mas de 20");
                return;
            }

            //Verificacion de cuenta y contraseña en bd
            AnalizadorJSON analizadorJSON = new AnalizadorJSON();

            ArrayList datos = new ArrayList();
            datos.add(email);
            datos.add(contraseña);

            ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
            Network net = cm.getActiveNetwork();

            if(net != null && cm.getNetworkCapabilities(cm.getActiveNetwork()) != null){

                String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_cuenta_ingresar.php";

                //Nuevo hilo
                new Thread(() -> {

                    JSONObject jsonObject = analizadorJSON.altaCuenta(url, metodo, datos);

                    try {

                        boolean res = jsonObject.getBoolean("LOGIN");

                        if(res){

                            // Obtener el rol del usuario
                            String rolUsuario = jsonObject.getString("ROL");

                            // Verificar si es Admin
                            if(rolUsuario.equalsIgnoreCase("Admin")){

                                runOnUiThread(() -> {
                                    Toast.makeText(MainActivity.this,
                                            "Esa cuenta solo es accesible desde web...",
                                            Toast.LENGTH_LONG).show();
                                            correo.setText("");
                                            contra.setText("");
                                });

                                // No continuar con el flujo normal
                                return;
                            }

                            // Si NO es Admin, continuar con el flujo normal para Paciente/Usuario
                            String url2 = "http://10.0.2.2:80/api_php_mysql/api_consulta_paciente_id.php";

                            JSONObject jsonObject2 = analizadorJSON.peticionHTTPConsultasCuenta(url2, metodo, email);

                            boolean resPa = jsonObject2.getBoolean("EXISTE_PACIENTE");

                            if(resPa){

                                runOnUiThread(() -> {
                                    Toast.makeText(MainActivity.this,
                                            "Ingresando...",
                                            Toast.LENGTH_LONG).show();
                                            correo.setText("");
                                            contra.setText("");
                                });

                                //Redirreccionar a landing_paciente
                                Intent intent = new Intent(MainActivity.this, landing_paciente.class);

                                //Pasar el correo a la clases cambio_paciente
                                intent.putExtra("correo_usuario", email);

                                startActivity(intent);

                            } else {

                                runOnUiThread(() -> {
                                    Toast.makeText(MainActivity.this,
                                            "No completo sus datos...",
                                            Toast.LENGTH_LONG).show();
                                            correo.setText("");
                                            contra.setText("");
                                });

                                //Redirreccionar a alta_paciente
                                Intent intent = new Intent(MainActivity.this, crear_paciente.class);
                                startActivity(intent);

                            }//if resultado consulta paciente

                        } else {

                            runOnUiThread(() -> {
                                Toast.makeText(MainActivity.this,
                                        "Datos erroneos...",
                                        Toast.LENGTH_LONG).show();
                            });

                        }//If resultado consulta correo y contraseña

                    } catch (JSONException e) {

                        Log.e("LOGIN_ERROR", "Error al procesar JSON: " + e.getMessage());
                        runOnUiThread(() -> {
                            Toast.makeText(MainActivity.this,
                                    "Error al procesar respuesta",
                                    Toast.LENGTH_SHORT).show();
                        });

                    }//Try

                }).start();

            } else {
                Toast.makeText(MainActivity.this,
                        "No hay conexión a internet",
                        Toast.LENGTH_SHORT).show();
            }//If conexion internet

        });

    }

}