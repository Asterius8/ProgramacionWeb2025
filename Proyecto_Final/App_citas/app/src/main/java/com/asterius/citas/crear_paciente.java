package com.asterius.citas;

import android.app.DatePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.Network;
import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
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
import java.util.Calendar;

import controller.AnalizadorJSON;

public class crear_paciente extends AppCompatActivity {

    //TextView id, correo;
    EditText nombre, apellidoPa, apellidoMa, fecha_nac, telefono, nombreEmer, telefonoEmer;
    Spinner sexo, seguro;

    Button aceptar;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_crear_paciente);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        //id = findViewById(R.id.testid);
        //correo = findViewById(R.id.testemail);

        Intent intent = getIntent();

        //id.setText(String.valueOf(intent.getIntExtra("id_usuario", -1))); // -1 por si no viene nada;
        //correo.setText(intent.getStringExtra("correo_usuario"));//Se utiliza para recibir los valores de otra activity

        nombre = findViewById(R.id.txt_nombre);
        apellidoPa = findViewById(R.id.txt_apell_pat);
        apellidoMa = findViewById(R.id.txt_apell_mat);
        fecha_nac = findViewById(R.id.txt_fecha);
        sexo = findViewById(R.id.spn_sexo);
        telefono = findViewById(R.id.txt_tele);
        seguro = findViewById(R.id.spn_tipo_seg);
        nombreEmer = findViewById(R.id.txt_nombre_emer);
        telefonoEmer = findViewById(R.id.txt_telefo_emer);
        aceptar = findViewById(R.id.btnGuardar);

        EditText txtFecha = findViewById(R.id.txt_fecha);


        txtFecha.setOnClickListener(v -> {

            Calendar cal = Calendar.getInstance();
            int dia = cal.get(Calendar.DAY_OF_MONTH);
            int mes = cal.get(Calendar.MONTH);
            int año = cal.get(Calendar.YEAR);

            DatePickerDialog datePicker = new DatePickerDialog(
                    crear_paciente.this,
                    (view, year, month, dayOfMonth) -> {

                        String fecha = String.format("%04d-%02d-%02d", year, month + 1, dayOfMonth);

                        txtFecha.setText(fecha);

                    },
                    año, mes, dia
            );

            // PROHIBIR FECHAS FUTURAS
            datePicker.getDatePicker().setMaxDate(System.currentTimeMillis());

            datePicker.show();
        });

        aceptar.setOnClickListener(v -> {
            if (validarCampos()) {

                //Verificar que el paciente no exista ya
                AnalizadorJSON analizadorJSON = new AnalizadorJSON();

                ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
                Network net = cm.getActiveNetwork();

                //IF para verificar conexion de red
                if(net!=null && cm.getNetworkCapabilities(cm.getActiveNetwork())!=null){

                    String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_peciente_existencia.php";

                    String metodo = "POST";

                    new Thread(new Runnable() {
                        @Override
                        public void run() {

                            ArrayList datosPaciente = new ArrayList();

                            datosPaciente.add(nombre.getText().toString());
                            datosPaciente.add(apellidoPa.getText().toString());
                            datosPaciente.add(apellidoMa.getText().toString());
                            datosPaciente.add(fecha_nac.getText().toString());
                            datosPaciente.add(sexo.getSelectedItem().toString());
                            datosPaciente.add(telefono.getText().toString());

                            JSONObject jsonObject = analizadorJSON.consultaPacienteDuplicado(url,metodo,datosPaciente);

                            try {
                                boolean resDupli = jsonObject.getBoolean("EXISTE_PACIENTE");

                                if(resDupli) {
                                    Log.i("JSON Recibido", jsonObject.toString());

                                    Log.i("MSJ Consulta", "Consulta Correcta");

                                    runOnUiThread(new Runnable() {
                                        @Override
                                        public void run() {

                                            Toast.makeText(crear_paciente.this, "Este paciente ya existe", Toast.LENGTH_LONG).show();

                                        }
                                    });

                                }else{

                                    //Agregar paciente con datos completos

                                    String url2 = "http://10.0.2.2:80/api_php_mysql/api_alta_paciente.php";

                                    String metodo = "POST";

                                    datosPaciente.add(intent.getStringExtra("correo_usuario"));
                                    datosPaciente.add(seguro.getSelectedItem().toString());
                                    datosPaciente.add(nombreEmer.getText().toString());
                                    datosPaciente.add(telefonoEmer.getText().toString());

                                    JSONObject jsonObject2 = analizadorJSON.altaPaciente(url2,metodo,datosPaciente);

                                    boolean res = jsonObject2.getBoolean("ALTA_PACIENTE");

                                    if(res){

                                        runOnUiThread(new Runnable() {
                                            @Override
                                            public void run() {

                                                Toast.makeText(crear_paciente.this, "Paciente agregado", Toast.LENGTH_LONG).show();

                                            }
                                        });

                                    }
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
        nombre.setError(null);
        apellidoPa.setError(null);
        apellidoMa.setError(null);
        fecha_nac.setError(null);
        telefono.setError(null);
        nombreEmer.setError(null);
        telefonoEmer.setError(null);

        boolean valido = true;

        String n = nombre.getText().toString().trim();
        String ap = apellidoPa.getText().toString().trim();
        String am = apellidoMa.getText().toString().trim();
        String fn = fecha_nac.getText().toString().trim();
        String tel = telefono.getText().toString().trim();
        String sexoSel = sexo.getSelectedItem().toString();
        String segSel = seguro.getSelectedItem().toString();
        String cen = nombreEmer.getText().toString().trim();
        String cet = telefonoEmer.getText().toString().trim();

        // ---------- VALIDACIÓN NOMBRE ----------
        if (n.isEmpty()) {
            nombre.setError("El nombre es obligatorio");
            valido = false;
        } else if (!n.matches("^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$")) {
            nombre.setError("Solo letras y espacios");
            valido = false;
        }

        // ---------- PRIMER APELLIDO ----------
        if (ap.isEmpty()) {
            apellidoPa.setError("El primer apellido es obligatorio");
            valido = false;
        } else if (!ap.matches("^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$")) {
            apellidoPa.setError("Solo letras");
            valido = false;
        }

        // ---------- SEGUNDO APELLIDO ----------
        if (am.isEmpty()) {
            apellidoMa.setError("El segundo apellido es obligatorio");
            valido = false;
        } else if (!am.matches("^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$")) {
            apellidoMa.setError("Solo letras");
            valido = false;
        }

        // ---------- FECHA ----------
        if (fn.isEmpty()) {
            fecha_nac.setError("La fecha es obligatoria");
            valido = false;
        } else {

            // Validar formato YYYY-MM-DD
            if (!fn.matches("^\\d{4}-\\d{2}-\\d{2}$")) {
                fecha_nac.setError("Formato incorrecto (AAAA-MM-DD)");
                valido = false;
            } else {
                // Validar que NO sea futura
                try {
                    java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("yyyy-MM-dd");
                    sdf.setLenient(false);
                    java.util.Date fechaDate = sdf.parse(fn);

                    if (fechaDate.after(new java.util.Date())) {
                        fecha_nac.setError("No puede ser una fecha futura");
                        valido = false;
                    }
                } catch (Exception e) {
                    fecha_nac.setError("Fecha inválida");
                    valido = false;
                }
            }
        }

        // ---------- SEXO ----------
        String[] sexosValidos = {"M", "F", "O"};
        boolean sexoOk = false;

        for (String s : sexosValidos) {
            if (sexoSel.equals(s)) sexoOk = true;
        }

        if (!sexoOk) {
            // spinner no usa setError, así que usamos un toast
            Toast.makeText(this, "Sexo no válido", Toast.LENGTH_SHORT).show();
            valido = false;
        }

        // ---------- TELÉFONO ----------
        if (tel.isEmpty()) {
            telefono.setError("El teléfono es obligatorio");
            valido = false;
        } else if (!tel.matches("^[0-9]{10}$")) {
            telefono.setError("Debe tener 10 dígitos");
            valido = false;
        }

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