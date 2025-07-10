package madstodolist.dto;

import java.io.Serializable;
import java.util.List;
import java.util.Objects;

public class TareaData implements Serializable {

    private static final long serialVersionUID = 1L;

    private Long id;
    private String titulo;
    private Long usuarioId;  // Esta es la ID del usuario asociado
    private boolean destacado;
    private List<SubtareaData> subtareas;  // Nuevo atributo para almacenar las subtareas

    private Long equipoId;

    private String prioridad;
    // Getters y setters

    public Long getEquipoId(){return equipoId;}

    public void setEquipoId(Long equipoId){this.equipoId = equipoId;}
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getTitulo() {
        return titulo;
    }

    public void setTitulo(String titulo) {
        this.titulo = titulo;
    }

    public Long getUsuarioId() {
        return usuarioId;
    }

    public void setUsuarioId(Long usuarioId) {
        this.usuarioId = usuarioId;
    }

    public boolean isDestacado() {
        return destacado;
    }

    public void setDestacado(boolean destacado) {
        this.destacado = destacado;
    }

    // Métodos para manejar las subtareas

    public List<SubtareaData> getSubtareas() {
        return subtareas;
    }

    public void setSubtareas(List<SubtareaData> subtareas) {
        this.subtareas = subtareas;
    }

    // Sobreescribimos equals y hashCode para que dos tareas sean iguales
    // si tienen el mismo ID (ignoramos el resto de atributos)
    public String getPrioridad() { // Getter para prioridad
        return prioridad;
    }

    public void setPrioridad(String prioridad) { // Setter para prioridad
        this.prioridad = prioridad;
    }
    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof TareaData)) return false;
        TareaData tareaData = (TareaData) o;
        return Objects.equals(id, tareaData.id);
    }

    @Override
    public int hashCode() {
        return Objects.hash(id);
    }
}
