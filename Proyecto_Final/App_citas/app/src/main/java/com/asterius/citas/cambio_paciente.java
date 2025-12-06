package com.asterius.citas;

import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.Network;
import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Arrays;

import controller.AnalizadorJSON;

public class cambio_paciente extends AppCompatActivity {

    EditText nombreEmer, telefonoEmer;
    Spinner seguro;
    Button guardar;

    String metodo = "POST";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_cambio_paciente);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        Intent intent = getIntent();

        seguro = findViewById(R.id.spn_tipo_seg);
        nombreEmer = findViewById(R.id.txt_nombre_emer);
        telefonoEmer = findViewById(R.id.txt_telefo_emer);
        guardar = findViewById(R.id.btnGuardar);

        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        Network net = cm.getActiveNetwork();

        AnalizadorJSON analizadorJSON = new AnalizadorJSON();

        if(net!=null && cm.getNetworkCapabilities(cm.getActiveNetwork())!=null){

            String url2 = "http://10.0.2.2:80/api_php_mysql/api_consulta_paciente.php";

            new Thread(new Runnable() {
                @Override
                public void run() {

                    JSONObject jsonObject2 = analizadorJSON.consultaPaciente(url2,metodo,intent.getStringExtra("correo_usuario"));

                    String tiposeguro = null;
                    String nomEme = null;
                    String telEme = null;

                    try {
                        tiposeguro = jsonObject2.getString("Tipo_Seguro");
                        nomEme = jsonObject2.getString("Contacto_Emergencia_Nombre");
                        telEme = jsonObject2.getString("Contacto_Emergencia_Telefono");

                        String[] segurosValidos = {"Privado", "Aseguradora", "Gobierno", "Indigente", "Ninguno"};
                        int posicion = Arrays.asList(segurosValidos).indexOf(tiposeguro);

                        seguro.setSelection((posicion +1));
                        nombreEmer.setText(nomEme);
                        telefonoEmer.setText(telEme);

                    } catch (JSONException e) {
                        throw new RuntimeException(e);
                    }



                }
            }).start();
        }

        guardar.setOnClickListener(v -> {

            if(validarCampos()){

                if(net!=null && cm.getNetworkCapabilities(cm.getActiveNetwork())!=null){

                    String url = "http://10.0.2.2:80/api_php_mysql/api_cambio_paciente.php";

                    new Thread(new Runnable() {
                        @Override
                        public void run() {


                            ArrayList datosPaciente = new ArrayList();

                            datosPaciente.add(intent.getStringExtra("correo_usuario"));
                            datosPaciente.add(seguro.getSelectedItem().toString());
                            datosPaciente.add(nombreEmer.getText().toString());
                            datosPaciente.add(telefonoEmer.getText().toString());

                            JSONObject jsonObject = analizadorJSON.editPaciente(url,metodo,datosPaciente);

                            try {

                                boolean res = jsonObject.getBoolean("ACTUALIZAR_PACIENTE");

                                if(res){

                                    runOnUiThread(new Runnable() {
                                        @Override
                                        public void run() {

                                            Toast.makeText(cambio_paciente.this, "Paciente Editado", Toast.LENGTH_LONG).show();

                                        }
                                    });

                                }

                            } catch (JSONException e) {
                                throw new RuntimeException(e);
                            }

                        }
                    }).start();

                }

            }

        });

    }

    private boolean validarCampos() {

        // Reiniciar errores visuales
        nombreEmer.setError(null);
        telefonoEmer.setError(null);

        boolean valido = true;

        String segSel = seguro.getSelectedItem().toString();
        String cen = nombreEmer.getText().toString().trim();
        String cet = telefonoEmer.getText().toString().trim();

        // ---------- VALIDACIÓN NOMBRE ----------

        // ---------- SEGURO ----------
        String[] segurosValidos = {"Privado", "Aseguradora", "Gobierno", "Indigente", "Ninguno"};
        boolean seguroOk = false;

        for (String s : segurosValidos) {
            if (segSel.equals(s)) seguroOk = true;
        }

        if (!seguroOk) {
            Toast.makeText(this, "Tipo de seguro no válido", Toast.LENGTH_SHORT).show();
            valido = false;
        }

        // ---------- CONTACTO EMERGENCIA ----------
        if (cen.isEmpty()) {
            nombreEmer.setError("Obligatorio");
            valido = false;
        } else if (!cen.matches("^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$")) {
            nombreEmer.setError("Solo letras");
            valido = false;
        }

        // ---------- TELÉFONO EMERGENCIA ----------
        if (cet.isEmpty()) {
            telefonoEmer.setError("Obligatorio");
            valido = false;
        } else if (!cet.matches("^[0-9]{10}$")) {
            telefonoEmer.setError("Debe tener 10 dígitos");
            valido = false;
        }

        return valido;
    }
}