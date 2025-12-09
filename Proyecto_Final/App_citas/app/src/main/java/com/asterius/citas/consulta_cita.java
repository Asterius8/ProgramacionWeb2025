package com.asterius.citas;

import android.app.AlertDialog;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

import controller.AnalizadorJSON;

public class consulta_cita extends AppCompatActivity {

    RecyclerView recyclerView;
    AdaptadorCitas adapter;
    RecyclerView.LayoutManager layoutManager;

    EditText filtro;

    static ArrayList<cita> listaCitas = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_consulta_cita);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        recyclerView = findViewById(R.id.recyclerCitas);
        recyclerView.setHasFixedSize(true);
        Intent intent = getIntent();
        filtro = findViewById(R.id.txt_filtro);

        layoutManager = new LinearLayoutManager(this);
        recyclerView.setLayoutManager(layoutManager);

        listaCitas.clear();

        cargarCitas(intent.getStringExtra("correo_usuario"));
    }

    private void cargarCitas(String correoUsuario) {
        new Thread(() -> {

            String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_citas_existencia.php";
            String metodo = "POST";

            AnalizadorJSON analizadorJSON = new AnalizadorJSON();

            String url2 = "http://10.0.2.2:80/api_php_mysql/api_consulta_paciente.php";

            JSONObject jsonObject = analizadorJSON.consultaPaciente(url2, metodo, correoUsuario);

            String id_paciente;

            try {
                id_paciente = jsonObject.getString("Id_Pacientes");
            } catch (JSONException e) {
                throw new RuntimeException(e);
            }

            JSONObject jsonObject2 = analizadorJSON.consultaCitas(url, metodo, id_paciente);

            try {
                boolean hayCitas = jsonObject2.getBoolean("hayCitas");

                if (!hayCitas) {
                    runOnUiThread(() -> {
                        Toast.makeText(consulta_cita.this,
                                "No tienes citas registradas",
                                Toast.LENGTH_LONG).show();

                        new android.os.Handler().postDelayed(() -> {
                            Intent intentLanding = new Intent(consulta_cita.this, landing_paciente.class);
                            intentLanding.putExtra("correo_usuario", correoUsuario);
                            startActivity(intentLanding);
                            finish();
                        }, 2000);
                    });
                    return;
                }

                JSONArray array = jsonObject2.getJSONArray("lista");

                Log.i("CITAS=>", array.toString());

                listaCitas.clear();

                for (int i = 0; i < array.length(); i++) {
                    JSONObject citaJson = array.getJSONObject(i);

                    cita cita = new cita(
                            citaJson.getInt("Id_Citas"),
                            citaJson.getString("Fecha"),
                            citaJson.getString("Hora"),
                            citaJson.getString("Especialidad_Nombre"),
                            citaJson.getInt("Medicos_Id_Medicos")
                    );

                    listaCitas.add(cita);
                }

                runOnUiThread(() -> {
                    adapter = new AdaptadorCitas(listaCitas, this);
                    recyclerView.setAdapter(adapter);

                    filtro.addTextChangedListener(new android.text.TextWatcher() {
                        @Override
                        public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

                        @Override
                        public void onTextChanged(CharSequence s, int start, int before, int count) {
                            adapter.filtrar(s.toString());
                        }

                        @Override
                        public void afterTextChanged(android.text.Editable s) {}
                    });
                });

            } catch (JSONException e) {
                e.printStackTrace();
                runOnUiThread(() -> {
                    Toast.makeText(consulta_cita.this,
                            "Error al cargar las citas",
                            Toast.LENGTH_SHORT).show();
                });
            }

        }).start();
    }

    public void eliminarCita(cita cita, int position) {
        new Thread(() -> {
            String url = "http://10.0.2.2:80/api_php_mysql/api_eliminar_cita.php";
            String metodo = "POST";

            AnalizadorJSON analizadorJSON = new AnalizadorJSON();

            // Llamar al método eliminarCita del AnalizadorJSON
            JSONObject respuesta = analizadorJSON.eliminarCita(url, metodo, String.valueOf(cita.getIdCita()));

            try {
                boolean success = respuesta.getBoolean("success");

                runOnUiThread(() -> {
                    if (success) {
                        Toast.makeText(consulta_cita.this,
                                "Cita eliminada correctamente",
                                Toast.LENGTH_SHORT).show();

                        // Eliminar de la lista y actualizar adaptador
                        listaCitas.remove(position);
                        adapter.notifyItemRemoved(position);
                        adapter.notifyItemRangeChanged(position, listaCitas.size());

                        // Si ya no hay citas, recargar
                        if (listaCitas.isEmpty()) {
                            new android.os.Handler().postDelayed(() -> {
                                Intent intent = new Intent(consulta_cita.this, landing_paciente.class);
                                intent.putExtra("correo_usuario", getIntent().getStringExtra("correo_usuario"));
                                startActivity(intent);
                                finish();
                            }, 1500);
                        }

                    } else {
                        String mensaje = null;
                        try {
                            mensaje = respuesta.getString("mensaje");
                        } catch (JSONException e) {
                            throw new RuntimeException(e);
                        }
                        Toast.makeText(consulta_cita.this,
                                "Error: " + mensaje,
                                Toast.LENGTH_SHORT).show();
                    }
                });

            } catch (JSONException e) {
                e.printStackTrace();
                runOnUiThread(() -> {
                    Toast.makeText(consulta_cita.this,
                            "Error al procesar la respuesta",
                            Toast.LENGTH_SHORT).show();
                });
            }

        }).start();
    }

}

class AdaptadorCitas extends RecyclerView.Adapter<AdaptadorCitas.MyViewHolder> {

    private ArrayList<cita> listaOriginal;
    private ArrayList<cita> listaFiltrada;
    private consulta_cita activity;

    public AdaptadorCitas(ArrayList<cita> datos, consulta_cita activity){
        this.listaOriginal = new ArrayList<>(datos);
        this.listaFiltrada = datos;
        this.activity = activity;
    }

    public void filtrar(String texto){
        texto = texto.toLowerCase();
        listaFiltrada.clear();

        if(texto.isEmpty()){
            listaFiltrada.addAll(listaOriginal);
        } else {
            for(cita item : listaOriginal){
                if(item.toString().toLowerCase().contains(texto)){
                    listaFiltrada.add(item);
                }
            }
        }

        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.textview_recyclerview, parent, false);
        return new MyViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, int position) {
        cita cita = listaFiltrada.get(position);
        holder.textView.setText(cita.toString());

        // Long click para eliminar
        holder.itemView.setOnLongClickListener(v -> {
            mostrarDialogoEliminar(cita, position);
            return true;
        });
    }

    private void mostrarDialogoEliminar(cita cita, int position) {
        new AlertDialog.Builder(activity)
                .setTitle("Eliminar cita")
                .setMessage("¿Estás seguro de que deseas eliminar esta cita?\n\n" +
                        "Fecha: " + cita.getFecha() + "\n" +
                        "Especialidad: " + cita.getEspecialidad())
                .setPositiveButton("Sí, eliminar", (dialog, which) -> {
                    activity.eliminarCita(cita, position);
                })
                .setNegativeButton("Cancelar", (dialog, which) -> {
                    dialog.dismiss();
                })
                .setIcon(android.R.drawable.ic_dialog_alert)
                .show();
    }

    @Override
    public int getItemCount() {
        return listaFiltrada.size();
    }

    public static class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView textView;
        public MyViewHolder(View itemView) {
            super(itemView);
            textView = itemView.findViewById(R.id.textView_recycler);
        }
    }
}