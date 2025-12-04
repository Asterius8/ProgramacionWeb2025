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


}
