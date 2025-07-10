package madstodolist.model;


import javax.persistence.*;
import java.io.Serializable;
import java.time.LocalDateTime;
import java.util.Set;
import java.util.HashSet;


@Entity
@Table(name = "equipos")
public class Equipo implements Serializable {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private String nombre;

    private String creador;

    @Column(nullable = false, updatable = false)
    private LocalDateTime fechaCreacion;

    @OneToMany(fetch = FetchType.EAGER, mappedBy = "equipo")
    private Set<Tarea> tareas = new HashSet<>();

    public Equipo() {
        this.fechaCreacion = LocalDateTime.now();
    }

    public Equipo(String nombre) {
        this.nombre = nombre;
        this.fechaCreacion = LocalDateTime.now();
    }

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

    public String getCreador() {
        return creador;
    }

    public void setCreador(String creador) {
        this.creador = creador;
    }

    public Set<Tarea> getTareas() {
        return tareas;
    }

    public void addTarea(Tarea tarea) {
        tareas.add(tarea);
        tarea.setEquipo(this); // Establece la relación inversa
    }

    public void removeTarea(Tarea tarea) {
        tareas.remove(tarea);
        tarea.setEquipo(null); // Rompe la relación inversa
    }

    @ManyToMany(fetch = FetchType.LAZY)
    @JoinTable(name = "equipo_usuario",
            joinColumns = {@JoinColumn(name = "fk_equipo")},
            inverseJoinColumns = {@JoinColumn(name = "fk_usuario")})
    private Set<Usuario> usuarios = new HashSet<>();

    public Set<Usuario> getUsuarios() {
        return usuarios;
    }

    public void addUsuario(Usuario usuario) {
        this.usuarios.add(usuario);
        usuario.getEquipos().add(this);
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;
        Equipo equipo = (Equipo) o;

        if (id != null && equipo.id != null) {
            return id.equals(equipo.id);
        }
        return nombre.equals(equipo.nombre);
    }

    @Override
    public int hashCode() {
        return id != null ? id.hashCode() : nombre.hashCode();
    }

    public LocalDateTime getFechaCreacion() {
        return fechaCreacion;
    }

    public void setFechaCreacion(LocalDateTime fechaCreacion) {
        this.fechaCreacion = fechaCreacion;
    }
}

