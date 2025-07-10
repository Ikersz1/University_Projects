package madstodolist.dto;

import java.io.Serializable;
import java.util.Objects;

// Data Transfer Object para la clase Subtarea
public class SubtareaData implements Serializable {

    private static final long serialVersionUID = 1L;

    private Long id;
    private String nombre;
    private Long tareaId; // ID de la tarea asociada

    // Getters y setters

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public Long getTareaId() {
        return tareaId;
    }

    public void setTareaId(Long tareaId) {
        this.tareaId = tareaId;
    }

    // Sobrescribimos equals y hashCode para que dos subtareas sean iguales si tienen el mismo ID
    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof SubtareaData)) return false;
        SubtareaData that = (SubtareaData) o;
        return Objects.equals(id, that.id);
    }

    @Override
    public int hashCode() {
        return Objects.hash(id);
    }
}
