package com.asterius.citas;

public class cita {
    private int idCita;
    private String fecha;
    private String hora;
    private String especialidad;
    private int idMedico;

    public cita(int idCita, String fecha, String hora, String especialidad, int idMedico) {
        this.idCita = idCita;
        this.fecha = fecha;
        this.hora = hora;
        this.especialidad = especialidad;
        this.idMedico = idMedico;
    }

    public int getIdCita() {
        return idCita;
    }

    public String getFecha() {
        return fecha;
    }

    public String getHora() {
        return hora;
    }

    public String getEspecialidad() {
        return especialidad;
    }

    public int getIdMedico() {
        return idMedico;
    }

    @Override
    public String toString() {
        return fecha + " | " + especialidad;
    }
}