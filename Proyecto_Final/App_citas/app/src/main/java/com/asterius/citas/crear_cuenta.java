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

public class crear_cuenta extends AppCompatActivity {

    Button btn_registrar;
    EditText txt_correo_reg, txt_contra_reg;
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

        // Inicializar componentes (FALTABA ESTO)
        btn_registrar   = findViewById(R.id.btn_registrar);
        txt_correo_reg  = findViewById(R.id.txt_correo_reg);
        txt_contra_reg  = findViewById(R.id.txt_contra_reg);


        //evento para obtener los datos de las cajas mediante el Onclick del boton
        btn_registrar.setOnClickListener(v -> {

            String correo = txt_correo_reg.getText().toString();
            String contraseña = txt_contra_reg.getText().toString();

            //Validaciones correo
            if (!Patterns.EMAIL_ADDRESS.matcher(correo).matches()) {//Valida que sea en forma de correo
                txt_correo_reg.setError("Correo inválido");//devuelve el error al componente
                return;
            } else if (correo.isEmpty()) {
                txt_correo_reg.setError("El correo no puede estar vacío");
                return;
            }

            //Validaciones contraseña
            if(contraseña.isEmpty()){
                txt_contra_reg.setError("La contraseña no puede estar vacia");
                return;
            } else if (contraseña.length() < 6) {
                txt_contra_reg.setError("La contraseña no tener menos de 6");
                return;
            }else if (contraseña.length() > 20) {
                txt_contra_reg.setError("La contraseña no tener mas de 20");
                return;
            }

            //Validacion de que no exista la cuenta ya
            AnalizadorJSON analizadorJSON = new AnalizadorJSON();

            ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
            Network net = cm.getActiveNetwork();

            if(net!=null && cm.getNetworkCapabilities(cm.getActiveNetwork())!=null){

                String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_cuenta_existencia.php";

                String metodo = "POST";

                new Thread(new Runnable() {
                    @Override
                    public void run() {

                        JSONObject jsonObject = analizadorJSON.peticionHTTPConsultasCuenta(url,metodo,correo);

                        try {
                            boolean res = jsonObject.getBoolean("EXISTE_CUENTA");

                            if(res){
                                Log.i("MSJ Consulta", "Consulta Correcta");

                                runOnUiThread(new Runnable() {
                                    @Override
                                    public void run() {
                                        Toast.makeText(getBaseContext(), "Ese correo ya existe", Toast.LENGTH_LONG).show();
                                        txt_contra_reg.setText("");

                                    }
                                });

                            }else {

                                ArrayList datosAlta = new ArrayList();

                                datosAlta.add(correo);
                                datosAlta.add(contraseña);

                                String url2 = "http://10.0.2.2:80/api_php_mysql/api_alta_cuenta.php";
                                JSONObject jsonObject2 = analizadorJSON.altaCuenta(url2,"POST", datosAlta);

                                boolean resAlta = jsonObject2.getBoolean("ALTA_CUENTA");

                                if(resAlta){
                                    runOnUiThread(new Runnable() {
                                        @Override
                                        public void run() {
                                            txt_contra_reg.setText("");
                                            txt_correo_reg.setText("");
                                            Toast.makeText(getBaseContext(), "Cuenta creada correctamente", Toast.LENGTH_LONG).show();
                                        }
                                    });
                                    //MANDAR A OTRA ACTIVITY JUNTO CON LOS DATOS DE CORREO Y ID CUENTA
                                    String urlId = "http://10.0.2.2:80/api_php_mysql/api_consulta_id_cuenta.php";

                                    JSONObject jsonObject3 = analizadorJSON.consultaId_cuenta(urlId,"POST", correo);

                                    int id_cuenta = jsonObject3.getInt("ID_CUENTA");

                                    Log.i("id cuenta--------->", String.valueOf(id_cuenta));

                                    Intent intent = new Intent(crear_cuenta.this, crear_paciente.class);
                                    intent.putExtra("correo_usuario", correo); // ← ENVIANDO EL CORREO
                                    intent.putExtra("id_usuario", id_cuenta); // ← ENVIANDO EL ID DE LA CUENTA
                                    startActivity(intent);
                                    finish();

                                }

                            }
                        } catch (JSONException e) {
                            throw new RuntimeException(e);
                        }

                    }
                }).start();

            }

        });
    }


}
