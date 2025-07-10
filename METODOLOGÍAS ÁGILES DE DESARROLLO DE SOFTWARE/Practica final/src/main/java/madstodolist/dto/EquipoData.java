package madstodolist.dto;

import java.time.LocalDateTime;
import java.util.Objects;

public class EquipoData {
    private Long id;
    private String nombre;
    private String creador;
    private LocalDateTime fechaCreacion;

    public void setId(Long id) {
        this.id = id;
    }

    public Long getId() {
        return id;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getNombre() {
        return nombre;
    }

    public void setCreador(String creador) {
        this.creador = creador;
    }

    public String getCreador() {
        return creador;
    }
    // Getters y setters
    public LocalDateTime getFechaCreacion() {
        return fechaCreacion;
    }

    public void setFechaCreacion(LocalDateTime fechaCreacion) {
        this.fechaCreacion = fechaCreacion;
    }


    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;
        EquipoData equipo = (EquipoData) o;
        if (id != null && equipo.id != null)
            return Objects.equals(id, equipo.id);
        return nombre.equals(equipo.nombre);
    }

    @Override
    public int hashCode() {
        return Objects.hash(nombre);
    }

}
