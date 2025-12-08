package com.asterius.citas;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.TextView;

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

    RecyclerView recyclerView;                      // Recycler
    AdaptadorCitas adapter;                    // Adaptador
    RecyclerView.LayoutManager layoutManager;       // Layout vertical

    EditText filtro;

    static ArrayList listaCitas = new ArrayList<>();  // Lista donde guardaremos cada registro

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_consulta_cita);

        // Ajuste visual Edge-to-Edge
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // ---------------------------------
        // CONFIGURAR EL RECYCLER VIEW
        // ---------------------------------
        recyclerView = findViewById(R.id.recyclerCitas);
        recyclerView.setHasFixedSize(true);
        Intent intent = getIntent();
        filtro = findViewById(R.id.txt_filtro);

        layoutManager = new LinearLayoutManager(this);
        recyclerView.setLayoutManager(layoutManager);

        listaCitas.clear();

        // ---------------------------------
        // HILO PARA CONSULTAR LA API
        // ---------------------------------
        new Thread(() -> {

            String url = "http://10.0.2.2:80/api_php_mysql/api_consulta_citas_existencia.php";
            String metodo = "POST";

            AnalizadorJSON analizadorJSON = new AnalizadorJSON();

            String url2 = "http://10.0.2.2:80/api_php_mysql/api_consulta_paciente.php";

            JSONObject jsonObject = analizadorJSON.consultaPaciente(url2, metodo, intent.getStringExtra("correo_usuario"));

            String id_paciente;

            try {

                id_paciente = jsonObject.getString("Id_Pacientes");

            } catch (JSONException e) {

                throw new RuntimeException(e);

            }

            JSONObject jsonObject2 = analizadorJSON.consultaCitas(url, metodo, id_paciente);

            try {

                JSONArray array = jsonObject2.getJSONArray("lista");

                Log.i("CITAS=>", array.toString());

                // Construimos cada línea que va en el RecyclerView
                for (int i = 0; i < array.length(); i++) {

                    JSONObject cita = array.getJSONObject(i);

                    StringBuilder cadena = new StringBuilder();

                    cadena.append(

                            cita.getString("Fecha")    + " | " + cita.getString("Especialidad_Nombre")

                    );

                    listaCitas.add(cadena.toString());
                }

                // Refrescar vista en UI
                runOnUiThread(() -> {

                    adapter = new AdaptadorCitas(listaCitas);
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
            }

        }).start();


    }

} // FIN CLASS

class AdaptadorCitas extends RecyclerView.Adapter<AdaptadorCitas.MyViewHolder> {

    private ArrayList<String> listaOriginal;
    private ArrayList<String> listaFiltrada;

    public AdaptadorCitas(ArrayList<String> datos){
        this.listaOriginal = new ArrayList<>(datos); // copia
        this.listaFiltrada = datos;                 // referencia visible
    }

    public void filtrar(String texto){
        texto = texto.toLowerCase();
        listaFiltrada.clear();

        if(texto.isEmpty()){
            listaFiltrada.addAll(listaOriginal);
        } else {
            for(String item : listaOriginal){
                if(item.toLowerCase().contains(texto)){
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
        holder.textView.setText(listaFiltrada.get(position));
    }

    @Override
    public int getItemCount() {
        return listaFiltrada.size();
    }

    // CLASS VIEW HOLDER
    public static class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView textView;
        public MyViewHolder(View itemView) {
            super(itemView);
            textView = itemView.findViewById(R.id.textView_recycler);
        }
    }
}


