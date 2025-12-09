package controller;

import android.util.Log;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

public class AnalizadorJSON {

    //Atributos
    private InputStream is = null;
    private OutputStream os = null;
    private JSONObject jsonObject = null;
    private HttpURLConnection conexion = null;
    private URL url;
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //Codigo para PETICIONES HTTP (Request)
    public JSONObject peticionHTTPConsultasCuenta(String cadenaURL, String metodo, String filtro){

        //peticion para realizar un Consulta
        String cadenaJSON = "{ \"email_cell\":\"" + filtro + "\" }";

        Log.i("MSJ cadena armada--------->", cadenaJSON);

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            Log.d("MSJ---------------------------------->",cadena.toString());

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public JSONObject altaCuenta(String Curl, String metodo, ArrayList<String> datos){

        String cadenaJSON = "{ " +
                "\"email_cell\":\"" + datos.get(0) +
                "\", \"password_cell\":\"" + datos.get(1) +
                "\"}";

        try {
            url = new URL(Curl);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {
            Log.e("MSJ--------->", "Error en la direccion URL");
        } catch (IOException e) {
            Log.e("MSJ--------->", "Error en la Conexion");
        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            String line;
            StringBuilder fila = new StringBuilder();
            while ((line = br.readLine()) != null) {
                fila.append(line);
            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(fila));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;
    }
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public JSONObject consultaId_cuenta(String cadenaURL, String metodo, String filtro){

        //peticion para realizar un Consulta
        String cadenaJSON = "{ \"email_cell\":\"" + filtro + "\" }";

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public JSONObject consultaPacienteDuplicado(String cadenaURL, String metodo, ArrayList<String> datos){

        //Completar cadenaJSON que enviar
        String cadenaJSON = "{ " +
                "\"nombre_cel\":\"" + datos.get(0) +
                "\", \"ap_cel\":\"" + datos.get(1) +
                "\", \"am_cel\":\"" + datos.get(2) +
                "\", \"fecha_cel\":\"" + datos.get(3) +
                "\", \"sexo_cel\":\"" + datos.get(4) +
                "\", \"telefono_cel\":\"" + datos.get(5) +
                "\"}";

        Log.i("MSJ cadena armada--------->", cadenaJSON);

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public JSONObject altaPaciente(String cadenaURL, String metodo, ArrayList<String> datos){

        //Completar cadenaJSON que enviar
        String cadenaJSON = "{ " +
                "\"nombre_cel\":\"" + datos.get(0) +
                "\", \"ap_cel\":\"" + datos.get(1) +
                "\", \"am_cel\":\"" + datos.get(2) +
                "\", \"fecha_cel\":\"" + datos.get(3) +
                "\", \"sexo_cel\":\"" + datos.get(4) +
                "\", \"telefono_cel\":\"" + datos.get(5) +
                "\", \"email_cel\":\"" + datos.get(6) +
                "\", \"ts_cel\":\"" + datos.get(7) +
                "\", \"cen_cel\":\"" + datos.get(8) +
                "\", \"cet_Cel\":\"" + datos.get(9) +
                "\"}";

        Log.i("MSJ cadena armada--------->", cadenaJSON);

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public JSONObject editPaciente(String cadenaURL, String metodo,ArrayList<String> datos){

        //Completar cadenaJSON que enviar
        String cadenaJSON = "{ " +
                "\"email_cel\":\"" + datos.get(0) +
                "\", \"ts_cel\":\"" + datos.get(1) +
                "\", \"cen_cel\":\"" + datos.get(2) +
                "\", \"cet_Cel\":\"" + datos.get(3) +
                "\"}";

        Log.i("MSJ cadena armada--------->", cadenaJSON);

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

    public JSONObject consultaPaciente(String cadenaURL, String metodo, String filtro){

        //peticion para realizar un Consulta
        String cadenaJSON = "{ \"email_cel\":\"" + filtro + "\" }";

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

    public JSONObject peticionHTTPExisteMedico(String cadenaURL, String metodo) {

        String cadenaJSON = "{}"; // Esta API no recibe parámetros

        Log.i("API_MEDICOS -> JSON enviado:", cadenaJSON);

        try {

            URL url = new URL(cadenaURL);
            HttpURLConnection conexion = (HttpURLConnection) url.openConnection();

            // Tipo de petición (POST)
            conexion.setRequestMethod(metodo);
            conexion.setDoOutput(true);

            // Establecer formato correcto (OJO: corregido)
            conexion.setRequestProperty("Content-Type", "application/json; charset=UTF-8");

            // Enviar JSON
            OutputStream os = new BufferedOutputStream(conexion.getOutputStream());
            os.write(cadenaJSON.getBytes("UTF-8"));
            os.flush();
            os.close();

            // ======================
            // RECIBIR RESPUESTA
            // ======================
            InputStream is = new BufferedInputStream(conexion.getInputStream());
            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila;

            while ((fila = br.readLine()) != null) {
                cadena.append(fila);
            }

            is.close();

            Log.d("API_MEDICOS -> RESPUESTA:", cadena.toString());

            return new JSONObject(cadena.toString());

        } catch (Exception e) {
            Log.e("API_MEDICOS_ERROR", "Error en la petición: " + e.getMessage());
            return null;
        }
    }

    public JSONObject altaCita(String cadenaURL, String metodo, ArrayList<String> datos){

        //Completar cadenaJSON que enviar
        String cadenaJSON = "{ " +
                "\"fecha\":\"" + datos.get(0) +
                "\", \"hora\":\"" + datos.get(1) +
                "\", \"id_paciente\":\"" + datos.get(2) +
                "\", \"id_medico\":\"" + datos.get(3) +
                "\", \"especialidad\":\"" + datos.get(4) +
                "\"}";

        Log.i("MSJ cadena armada--------->", cadenaJSON);

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

    public JSONObject consultaCitas(String cadenaURL, String metodo, String filtro){

        //peticion para realizar un Consulta
        String cadenaJSON = "{ \"id_paciente\":\"" + filtro + "\" }";

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

    public JSONObject eliminarCita(String cadenaURL, String metodo, String idCita){

        //peticion para eliminar una cita
        String cadenaJSON = "{ \"id_cita\":\"" + idCita + "\" }";

        Log.i("MSJ cadena armada--------->", cadenaJSON);

        try {

            url = new URL(cadenaURL);
            conexion = (HttpURLConnection) url.openConnection();

            //Indicar el envio a traces de HTTP
            conexion.setDoOutput(true);

            //Indicar el envio a traces de HTTP
            conexion.setRequestMethod(metodo);

            //Indicar el tamaño prestablecido o fijo de la cadena a enviar
            conexion.setFixedLengthStreamingMode(cadenaJSON.length());

            //Establecer el formato de comunicacion
            conexion.setRequestProperty("Content-Type", "application/x-www.form-urlencoded");

            //preparar el envio de la Peticion
            os = new BufferedOutputStream(conexion.getOutputStream());

            os.write(cadenaJSON.getBytes());

            os.flush();

            os.close();

        } catch (MalformedURLException e) {

            Log.e("MSJ--------->", "Error en la direccion URL");

        } catch (IOException e) {

            Log.e("MSJ--------->", "Error en la Conexion");

        }

        //----------------- Recibir y Analizar Respuesta (response) -------------------------

        try {

            is = new BufferedInputStream(conexion.getInputStream());

            BufferedReader br = new BufferedReader(new InputStreamReader(is));

            StringBuilder cadena = new StringBuilder();
            String fila = null;
            while ((fila = br.readLine())  != null ){

                cadena.append(fila+"\n");

            }

            Log.d("MSJ ELIMINAR CITA--------->", cadena.toString());

            is.close();

            jsonObject = new JSONObject(String.valueOf(cadena));

        } catch (IOException e) {
            throw new RuntimeException(e);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }

        return jsonObject;

    }

}
