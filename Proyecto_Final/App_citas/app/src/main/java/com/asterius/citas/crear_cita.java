package com.asterius.citas;

import android.app.DatePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.Network;
import android.os.Bundle;
import android.util.Log;
import android.widget.ArrayAdapter;
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

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import controller.AnalizadorJSON;

public class crear_cita extends AppCompatActivity {

    EditText fecha, hora;

    Spinner medicos;

    Button agregar;

    String metodo = "POST";

    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_crear_cita);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        Intent intent = getIntent();

        fecha = findViewById(R.id.txt_fecha_c);
        hora = findViewById(R.id.txt_hora_c);
        medicos = findViewById(R.id.spn_medicos);
        agregar = findViewById(R.id.btn_agregar);

        ArrayList<Integer> listaIds = new ArrayList<>();
        ArrayList listaMedicoEspecialidad = new ArrayList();

        fecha.setOnClickListener(v -> {

            Calendar cal = Calendar.getInstance();
            int dia = cal.get(Calendar.DAY_OF_MONTH);
            int mes = cal.get(Calendar.MONTH);
            int año = cal.get(Calendar.YEAR);

            DatePickerDialog datePicker = new DatePickerDialog(
                    crear_cita.this,  // ← tu activity actual
                    (view, year, month, dayOfMonth) -> {

                        String fechax = String.format("%04d-%02d-%02d", year, month + 1, dayOfMonth);
                        fecha.setText(fechax);

                    },
                    año, mes, dia
            );

            // PERMITIR SOLO FECHAS FUTURAS
            // Hoy + 1 día (para evitar seleccionar el mismo día si así lo deseas)
            cal.add(Calendar.DAY_OF_MONTH, 1);
            datePicker.getDatePicker().setMinDate(cal.getTimeInMillis());

            datePicker.show();

        });

        AnalizadorJSON analizadorJSON = new AnalizadorJSON();

        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        Network net = cm.getActiveNetwork();

        if(net!=null && cm.getNetworkCapabilities(cm.getActiveNetwork())!=null){

            String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_medicos_existencia.php";

            new Thread(new Runnable() {
                @Override
                public void run() {

                    JSONObject jsonObject = analizadorJSON.peticionHTTPExisteMedico(url,metodo);

                    try {
                        //Verificamos que existan medicos en la bd
                        boolean hay = jsonObject.getBoolean("hayMedicos");

                        if (hay) {
                            //Array donde estan todos los medicos
                            JSONArray arrayMedicos = jsonObject.getJSONArray("lista");

                            for (int i = 0; i < arrayMedicos.length(); i++) {
                                //Saca el primer medico del JSONArray
                                JSONObject medico = arrayMedicos.getJSONObject(i);

                                //Creamos una variable para guardar el id por cada vuelta
                                int idMedico = medico.getInt("Id_Medicos");
                                //Creamos una variable para guardar el nombre por cada vuelta
                                String medicoCompleto = medico.getString("Nombre") + " " + medico.getString("Apellido_Paterno") + " " + medico.getString("Apellido_Materno") + " - " + medico.getString("Especialidad");

                                //Agregamos el valor de cada vuelta a un arreglo
                                listaIds.add(idMedico);
                                listaMedicoEspecialidad.add((medicoCompleto));

                            }//for

                        }else{

                            runOnUiThread(() -> {

                                Toast.makeText(crear_cita.this, "No hay médicos registrados.", Toast.LENGTH_SHORT).show();

                                // Redirigir a landing_paciente
                                Intent intent = new Intent(crear_cita.this, landing_paciente.class);
                                startActivity(intent);
                                finish(); //para evitar que regresen a esta pantalla

                            });

                        }

                    } catch (Exception e) {
                        e.printStackTrace();
                    }//try

                    runOnUiThread(() -> {

                        ArrayAdapter<String> adapter = new ArrayAdapter<>(

                                crear_cita.this,
                                android.R.layout.simple_spinner_item,
                                listaMedicoEspecialidad

                        );

                        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

                        medicos.setAdapter(adapter);

                    });

                }
            }).start();

        }

        agregar.setOnClickListener(v -> {

                if (validarCampos()) {

                    //CONTINUAR AQUI, PARA ENVIAR EL ID MEDICO RECUERDA QUE PUEDES USAR LA POCISION DEL SPINNER Y EL ID PACIENTE OBTENER DESDE EL API CON EL MISMO NOMBRE
                    if(net!=null && cm.getNetworkCapabilities(cm.getActiveNetwork())!=null){

                        String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_paciente.php";

                        new Thread(new Runnable() {

                            @Override
                            public void run() {

                                JSONObject jsonObject = analizadorJSON.consultaPaciente(url, metodo, intent.getStringExtra("correo_usuario"));

                                String id_paciente;

                                try {

                                    id_paciente = jsonObject.getString("Id_Pacientes");

                                } catch (JSONException e) {

                                    throw new RuntimeException(e);

                                }

                                String url2 = "http://10.0.2.2:80/api_php_mysql/api_alta_cita.php";

                                ArrayList datosCita = new ArrayList();

                                datosCita.add(fecha.getText().toString().trim());
                                datosCita.add(hora.getText().toString().trim());
                                datosCita.add(id_paciente);
                                datosCita.add(String.valueOf(listaIds.get(medicos.getSelectedItemPosition())));
                                datosCita.add(medicos.getSelectedItem().toString());

                                JSONObject jsonObject2 = analizadorJSON.altaCita(url2,metodo,datosCita);

                                try {

                                    boolean resDupli = jsonObject2.getBoolean("success");

                                    if(resDupli) {
                                        Log.i("JSON Recibido", jsonObject.toString());

                                        Log.i("MSJ Creacion", "Crecion Correcta");

                                        runOnUiThread(new Runnable() {
                                            @Override
                                            public void run() {

                                                Toast.makeText(crear_cita.this, "Crecion correcta", Toast.LENGTH_LONG).show();
                                                fecha.setText("");
                                                hora.setText("");
                                                medicos.setSelection(0);
                                            }
                                        });

                                    }else{

                                        runOnUiThread(new Runnable() {
                                            @Override
                                            public void run() {

                                                Toast.makeText(crear_cita.this, "Fallo en fecha u hora seleccionada", Toast.LENGTH_LONG).show();

                                            }
                                        });

                                    }

                                } catch (JSONException e) {

                                    throw new RuntimeException(e);

                                }


                            }

                        }).start();

                    }


                } else {

                    Toast.makeText(crear_cita.this, "Corrige los campos marcados", Toast.LENGTH_SHORT).show();

                }

        });

    }

    private boolean validarCampos() {

        // Reiniciar errores
        fecha.setError(null);
        hora.setError(null);

        boolean valido = true;

        String f = fecha.getText().toString().trim();
        String h = hora.getText().toString().trim();

        // ===========================
        // VALIDACIÓN DE FECHA FUTURA
        //===========================
        if (f.isEmpty()) {
            fecha.setError("La fecha es obligatoria");
            valido = false;
        } else {

            // Validar formato YYYY-MM-DD
            if (!f.matches("^\\d{4}-\\d{2}-\\d{2}$")) {
                fecha.setError("Formato incorrecto (AAAA-MM-DD)");
                valido = false;
            } else {
                try {
                    java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("yyyy-MM-dd");
                    sdf.setLenient(false);

                    java.util.Date fechaIngresada = sdf.parse(f);

                    // Obtener fecha de HOY (sin horas)
                    Calendar hoy = Calendar.getInstance();
                    hoy.set(Calendar.HOUR_OF_DAY, 0);
                    hoy.set(Calendar.MINUTE, 0);
                    hoy.set(Calendar.SECOND, 0);
                    hoy.set(Calendar.MILLISECOND, 0);

                    // Debe ser estrictamente futura
                    if (!fechaIngresada.after(hoy.getTime())) {
                        fecha.setError("Debe ser una fecha futura");
                        valido = false;
                    }

                } catch (Exception e) {
                    fecha.setError("Fecha inválida");
                    valido = false;
                }
            }
        }


        // ===========================
        // VALIDACIÓN DE HORA
        //===========================
        if (h.isEmpty()) {
            hora.setError("La hora es obligatoria");
            valido = false;
        } else {

            // Validar formato HH:mm
            if (!h.matches("^\\d{2}:\\d{2}$")) {
                hora.setError("Formato inválido (HH:MM)");
                valido = false;
            } else {

                try {
                    java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat("HH:mm");
                    sdf.setLenient(false);

                    java.util.Date horaIngresada = sdf.parse(h);
                    java.util.Date horaMin = sdf.parse("08:00");
                    java.util.Date horaMax = sdf.parse("18:00");

                    if (horaIngresada.before(horaMin) || horaIngresada.after(horaMax)) {
                        hora.setError("La hora debe ser entre 08:00 y 18:00");
                        valido = false;
                    }

                } catch (Exception e) {
                    hora.setError("Hora inválida");
                    valido = false;
                }
            }
        }

        return valido;
    }

}