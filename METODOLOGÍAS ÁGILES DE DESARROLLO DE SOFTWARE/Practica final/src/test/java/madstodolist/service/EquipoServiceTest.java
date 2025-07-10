package madstodolist.service;

import madstodolist.dto.TareaData;
import madstodolist.dto.UsuarioData;
import madstodolist.model.Equipo;
import madstodolist.model.Tarea;
import madstodolist.model.Usuario;
import madstodolist.repository.EquipoRepository;
import madstodolist.repository.TareaRepository;
import madstodolist.repository.UsuarioRepository;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.test.context.jdbc.Sql;
import madstodolist.dto.EquipoData;
import static org.assertj.core.api.Assertions.assertThat;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.Mockito.when;


import java.util.Iterator;
import java.util.List;
import java.util.Optional;
import java.util.Set;

@SpringBootTest
@Sql(scripts = "/clean-db.sql")
public class EquipoServiceTest {

    @Autowired
    EquipoService equipoService;

    @Autowired
    EquipoRepository equipoRepository;

    @Autowired
    private UsuarioService usuarioService;
    @Autowired
    private UsuarioRepository usuarioRepository;
    @MockBean
    private TareaRepository tareaRepository;

    @Autowired
    private TareaService tareaService;

    @Test
    public void crearRecuperarEquipo() {
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");
        assertThat(equipo.getId()).isNotNull();

        EquipoData equipoBd = equipoService.recuperarEquipo(equipo.getId());
        assertThat(equipoBd).isNotNull();
        assertThat(equipoBd.getNombre()).isEqualTo("Proyecto 1");
    }

    @Test
    public void crearEquipoUsuarioAnonimo() {
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");
        assertThat(equipo.getId()).isNotNull();

        EquipoData equipoBd = equipoService.recuperarEquipo(equipo.getId());
        assertThat(equipoBd).isNotNull();
        assertThat(equipoBd.getNombre()).isEqualTo("Proyecto 1");
        assertThat(equipoBd.getCreador()).isEqualTo("creador_anonimo");
    }

    @Test
    public void crearEquipoUsuarioNoAnonimo() {

        UsuarioData usuario = new UsuarioData();
        usuario.setEmail("user@ua");
        usuario.setPassword("123");
        usuario.setNombre("Goku");
        usuario = usuarioService.registrar(usuario);


        EquipoData equipo = equipoService.crearEquipoConCreador("Proyecto 1", usuario.getId());
        assertThat(equipo.getId()).isNotNull();

        EquipoData equipoBd = equipoService.recuperarEquipo(equipo.getId());
        assertThat(equipoBd).isNotNull();
        assertThat(equipoBd.getNombre()).isEqualTo("Proyecto 1");
        assertThat(equipoBd.getCreador()).isEqualTo("user@ua");
    }

    @Test
    public void listadoEquiposOrdenAlfabetico() {
        // GIVEN
        // Dos equipos en la base de datos
        equipoService.crearEquipo("Proyecto BBB");
        equipoService.crearEquipo("Proyecto AAA");

        // WHEN
        // Recuperamos los equipos
        List<EquipoData> equipos = equipoService.findAllOrdenadoPorNombre();

        // THEN
        // Los equipos están ordenados por nombre
        assertThat(equipos).hasSize(2);
        assertThat(equipos.get(0).getNombre()).isEqualTo("Proyecto AAA");
        assertThat(equipos.get(1).getNombre()).isEqualTo("Proyecto BBB");
    }

    @Test
    public void añadirUsuarioAEquipo() {
        // GIVEN
        // Un usuario y un equipo en la base de datos
        UsuarioData usuario = new UsuarioData();
        usuario.setEmail("user@ua");
        usuario.setPassword("123");
        usuario = usuarioService.registrar(usuario);
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");

        // WHEN
        // Añadimos el usuario al equipo
        equipoService.añadirUsuarioAEquipo(equipo.getId(), usuario.getId());

        // THEN
        // El usuario pertenece al equipo
        List<UsuarioData> usuarios = equipoService.usuariosEquipo(equipo.getId());
        assertThat(usuarios).hasSize(1);
        assertThat(usuarios.get(0).getEmail()).isEqualTo("user@ua");
    }

    @Test
    public void recuperarEquiposDeUsuario() {
        // GIVEN
        // Un usuario y dos equipos en la base de datos
        UsuarioData usuario = new UsuarioData();
        usuario.setEmail("user@ua");
        usuario.setPassword("123");
        usuario = usuarioService.registrar(usuario);
        EquipoData equipo1 = equipoService.crearEquipo("Proyecto 1");
        EquipoData equipo2 = equipoService.crearEquipo("Proyecto 2");
        equipoService.añadirUsuarioAEquipo(equipo1.getId(), usuario.getId());
        equipoService.añadirUsuarioAEquipo(equipo2.getId(), usuario.getId());

        // WHEN
        // Recuperamos los equipos del usuario
        List<EquipoData> equipos = equipoService.equiposUsuario(usuario.getId());

        // THEN
        // El usuario pertenece a los dos equipos
        assertThat(equipos).hasSize(2);
        assertThat(equipos.get(0).getNombre()).isEqualTo("Proyecto 1");
        assertThat(equipos.get(1).getNombre()).isEqualTo("Proyecto 2");
    }

    @Test
    public void comprobarExcepciones() {
        // Comprobamos las excepciones lanzadas por los métodos
        // recuperarEquipo, añadirUsuarioAEquipo, usuariosEquipo y equiposUsuario
        assertThatThrownBy(() -> equipoService.recuperarEquipo(1L))
                .isInstanceOf(EquipoServiceException.class);
        assertThatThrownBy(() -> equipoService.añadirUsuarioAEquipo(1L, 1L))
                .isInstanceOf(EquipoServiceException.class);
        assertThatThrownBy(() -> equipoService.usuariosEquipo(1L))
                .isInstanceOf(EquipoServiceException.class);
        assertThatThrownBy(() -> equipoService.equiposUsuario(1L))
                .isInstanceOf(EquipoServiceException.class);

        // Creamos un equipo pero no un usuario y comprobamos que también se lanza una excepción
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");
        assertThatThrownBy(() -> equipoService.añadirUsuarioAEquipo(equipo.getId(), 1L))
                .isInstanceOf(EquipoServiceException.class);
    }

    @Test
    public void eliminarUsuarioDeEquipo() {
        // GIVEN
        UsuarioData usuario = new UsuarioData();
        usuario.setEmail("user@ua");
        usuario.setPassword("123");
        usuario = usuarioService.registrar(usuario);
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");
        equipoService.añadirUsuarioAEquipo(equipo.getId(), usuario.getId());

        // WHEN
        equipoService.eliminarUsuarioDeEquipo(equipo.getId(), usuario.getId());

        // THEN
        List<UsuarioData> usuarios = equipoService.usuariosEquipo(equipo.getId());
        assertThat(usuarios).isEmpty();
    }

    @Test
    public void eliminarUsuarioDeEquipoInexistenteLanzaExcepcion() {
        assertThatThrownBy(() -> equipoService.eliminarUsuarioDeEquipo(999L, 1L))
                .isInstanceOf(EquipoServiceException.class)
                .hasMessage("Equipo no encontrado");
    }

    @Test
    public void excepcionCrearEquipoNombreVacio() {
        // THEN
        assertThatThrownBy(() -> equipoService.crearEquipo(""))
                .isInstanceOf(EquipoServiceException.class)
                .hasMessage("El nombre del equipo no puede estar vacío");
    }

    @Test
    public void excepcionAñadirUsuarioYaMiembro() {
        // GIVEN
        UsuarioData usuario = new UsuarioData();
        usuario.setEmail("user@ua");
        usuario.setPassword("123");
        usuario = usuarioService.registrar(usuario);
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");

        // Almacenar el ID en una variable final
        final Long usuarioId = usuario.getId();
        final Long equipoId = equipo.getId();

        // Añadimos el usuario al equipo por primera vez
        equipoService.añadirUsuarioAEquipo(equipoId, usuarioId);

        // THEN
        // Intentamos añadirlo nuevamente y verificamos que se lanza una excepción
        assertThatThrownBy(() -> equipoService.añadirUsuarioAEquipo(equipoId, usuarioId))
                .isInstanceOf(EquipoServiceException.class)
                .hasMessage("El usuario ya pertenece al equipo");
    }

    @Test
    public void excepcionEliminarUsuarioQueNoPertenece() {
        // GIVEN
        UsuarioData usuario = new UsuarioData();
        usuario.setEmail("user@ua");
        usuario.setPassword("123");
        usuario = usuarioService.registrar(usuario);
        EquipoData equipo = equipoService.crearEquipo("Proyecto 1");

        // Almacenar el ID en variables finales
        final Long usuarioId = usuario.getId();
        final Long equipoId = equipo.getId();

        // THEN
        // Intentamos eliminar al usuario que no pertenece al equipo y verificamos la excepción
        assertThatThrownBy(() -> equipoService.eliminarUsuarioDeEquipo(equipoId, usuarioId))
                .isInstanceOf(EquipoServiceException.class)
                .hasMessage("El usuario no pertenece al equipo");
    }

    @Test
    public void renombrarEquipo() {
        // GIVEN
        EquipoData equipo = equipoService.crearEquipo("Proyecto Inicial");
        Long equipoId = equipo.getId();

        // WHEN
        equipoService.renombrarEquipo(equipoId, "Proyecto Renombrado");

        // THEN
        Equipo equipoActualizado = equipoRepository.findById(equipoId).orElse(null);
        assertThat(equipoActualizado).isNotNull();
        assertThat(equipoActualizado.getNombre()).isEqualTo("Proyecto Renombrado");
    }

    @Test
    public void renombrarEquipoConNombreVacio() {
        // GIVEN
        EquipoData equipo = equipoService.crearEquipo("Equipo Original");

        // WHEN & THEN
        assertThatThrownBy(() -> equipoService.renombrarEquipo(equipo.getId(), ""))
                .isInstanceOf(EquipoServiceException.class)
                .hasMessage("El nombre del equipo no puede estar vacío.");
    }


    @Test
    public void eliminarEquipoExistente() {
        // GIVEN
        // Un equipo guardado en la base de datos
        Equipo equipo = new Equipo("Proyecto X");
        equipo = equipoRepository.save(equipo);

        // WHEN
        // Eliminamos el equipo
        equipoService.eliminarEquipo(equipo.getId());

        // THEN
        // Verificamos que ya no existe en la base de datos
        assertThat(equipoRepository.findById(equipo.getId())).isEmpty();
    }
    

}