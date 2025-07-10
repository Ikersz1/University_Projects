package madstodolist.model;

import javax.persistence.*;
import javax.validation.constraints.NotNull;
import java.io.Serializable;
import java.util.Date;
import java.util.HashSet;
import java.util.Objects;
import java.util.Set;

@Entity
@Table(name = "usuarios")
public class Usuario implements Serializable {

    private static final long serialVersionUID = 1L;

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    @NotNull
    private String email;
    private String nombre;
    private String password;

    @Column(name = "fecha_nacimiento")
    @Temporal(TemporalType.DATE)
    private Date fechaNacimiento;

    private boolean administrador = false; // Campo para el rol de administrador

    private boolean bloqueado = false; // Nuevo campo para bloquear el usuario

    // La relación es lazy por defecto,
    // es necesario acceder a la lista de tareas para que se carguen
    @OneToMany(mappedBy = "usuario")
    Set<Tarea> tareas = new HashSet<>();

    // Constructor vacío necesario para JPA/Hibernate.
    public Usuario() {}

    // Constructor público con los atributos obligatorios. En este caso el correo electrónico.
    public Usuario(String email) {
        this.email = email;
    }

    // Getters y setters

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public Date getFechaNacimiento() {
        return fechaNacimiento;
    }

    public void setFechaNacimiento(Date fechaNacimiento) {
        this.fechaNacimiento = fechaNacimiento;
    }

    public boolean isAdministrador() {
        return administrador;
    }

    public void setAdministrador(boolean administrador) {
        this.administrador = administrador;
    }

    public boolean isBloqueado() { // Getter para bloqueado
        return bloqueado;
    }

    public void setBloqueado(boolean bloqueado) { // Setter para bloqueado
        this.bloqueado = bloqueado;
    }

    // Getters y setters de la relación

    public Set<Tarea> getTareas() {
        return tareas;
    }

    // Método helper para añadir una tarea a la lista y establecer la relación inversa
    public void addTarea(Tarea tarea) {
        if (tareas.contains(tarea)) return;
        tareas.add(tarea);
        if (tarea.getUsuario() != this) {
            tarea.setUsuario(this);
        }
    }

    @ManyToMany(mappedBy = "usuarios")
    private Set<Equipo> equipos = new HashSet<>();

    public Set<Equipo> getEquipos() {
        return equipos;
    }

    public void addEquipo(Equipo equipo) {
        this.equipos.add(equipo);
        equipo.getUsuarios().add(this); // Mantiene la relación bidireccional
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;
        Usuario usuario = (Usuario) o;
        if (id != null && usuario.id != null)
            return Objects.equals(id, usuario.id);
        return email.equals(usuario.email);
    }

    @Override
    public int hashCode() {
        return Objects.hash(email);
    }
}
