package madstodolist.controller;

import madstodolist.dto.EquipoData;
import madstodolist.dto.UsuarioData;
import madstodolist.service.EquipoService;
import madstodolist.authentication.ManagerUserSession;
import madstodolist.service.EquipoServiceException;
import madstodolist.service.UsuarioService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.mock.web.MockHttpSession;
import org.springframework.test.web.servlet.MockMvc;

import javax.servlet.http.HttpSession;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import static org.hamcrest.CoreMatchers.containsString;
import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.mockito.Mockito.*;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(EquipoController.class)
class EquipoControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private EquipoService equipoService;

    @MockBean
    private UsuarioService usuarioService;

    @MockBean
    private ManagerUserSession managerUserSession;

    private MockHttpSession session;

    @BeforeEach
    void setup() {
        session = new MockHttpSession();
        session.setAttribute("idUsuarioLogeado", 1L); // Configuración de sesión simulada

        // Mock para el usuario logeado
        when(managerUserSession.usuarioLogeado()).thenReturn(1L);
    }

    @Test
    void testListadoEquipos() throws Exception {
        EquipoData equipo1 = new EquipoData();
        equipo1.setId(1L);
        equipo1.setNombre("Proyecto AAA");

        EquipoData equipo2 = new EquipoData();
        equipo2.setId(2L);
        equipo2.setNombre("Proyecto BBB");

        List<EquipoData> equipos = Arrays.asList(equipo1, equipo2);

        when(equipoService.findAllOrdenadoPorNombre()).thenReturn(equipos);

        mockMvc.perform(get("/equipos").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("listado"))
                .andExpect(model().attributeExists("equipos"))
                .andExpect(model().attribute("equipos", equipos));
    }

    @Test
    void testDetalleEquipo() throws Exception {
        // Configuración del equipo
        EquipoData equipo = new EquipoData();
        equipo.setId(1L);
        equipo.setNombre("Proyecto AAA");
        equipo.setCreador("creador@example.com");

        // Configuración del usuario logueado
        UsuarioData usuarioLogueado = new UsuarioData();
        usuarioLogueado.setId(1L);
        usuarioLogueado.setEmail("usuario1@example.com");

        // Usuarios en el equipo
        UsuarioData usuario2 = new UsuarioData();
        usuario2.setId(2L);
        usuario2.setEmail("usuario2@example.com");

        List<UsuarioData> usuarios = Arrays.asList(usuarioLogueado, usuario2);

        // Mock de dependencias
        when(equipoService.recuperarEquipo(1L)).thenReturn(equipo);
        when(equipoService.usuariosEquipo(1L)).thenReturn(usuarios);
        when(managerUserSession.usuarioLogeado()).thenReturn(usuarioLogueado.getId());
        when(usuarioService.findById(1L)).thenReturn(usuarioLogueado);

        // Simular sesión del usuario logueado
        session.setAttribute("idUsuarioLogeado", usuarioLogueado.getId());

        // Realización del test
        mockMvc.perform(get("/equipos/1").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("detalle"))
                .andExpect(model().attributeExists("equipo"))
                .andExpect(model().attributeExists("usuarios"))
                .andExpect(model().attribute("equipo", equipo))
                .andExpect(model().attribute("usuarios", usuarios));
    }


    @Test
    void testDetalleEquipoNotFound() throws Exception {
        when(equipoService.recuperarEquipo(1L)).thenReturn(null);

        mockMvc.perform(get("/equipos/1").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("404"));
    }

    @Test
    void testFormularioNuevoEquipo() throws Exception {
        mockMvc.perform(get("/equipos/nuevo").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("formNuevoEquipo"))
                .andExpect(model().attributeExists("equipoData"));
    }

    @Test
    void testCrearNuevoEquipo() throws Exception {
        UsuarioData usuario1 = new UsuarioData();
        usuario1.setId(1L);
        usuario1.setEmail("usuario1@example.com");
        usuarioService.registrar(usuario1);
        EquipoData equipoData = new EquipoData();
        equipoData.setId(1L);
        equipoData.setNombre("Nuevo Equipo");
        when(equipoService.crearEquipo("Nuevo Equipo")).thenReturn(equipoData);

        mockMvc.perform(post("/equipos/nuevo")
                        .param("nombre", "Nuevo Equipo")
                        .session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/equipos"));

        verify(equipoService).crearEquipoConCreador("Nuevo Equipo", 1L);
    }

    @Test
    void testCrearNuevoEquipoConError() throws Exception {
        // Simulación de datos
        EquipoData equipoData = new EquipoData();
        equipoData.setNombre(""); // Nombre vacío

        // Ejecución y verificación
        mockMvc.perform(post("/equipos/nuevo")
                        .session(session)
                        .flashAttr("equipoData", equipoData))
                .andExpect(status().isOk())
                .andExpect(view().name("formNuevoEquipo"))
                .andExpect(model().attributeExists("error"))
                .andExpect(model().attribute("error", "El nombre del equipo no puede estar vacío"));

        // Verificamos que el servicio no fue llamado
        verify(equipoService, times(0)).crearEquipo(anyString());
    }
    


    @Test
    void testAñadirmeAlEquipo() throws Exception {
        mockMvc.perform(post("/equipos/1/añadirme").session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/equipos/1"));

        verify(equipoService).añadirUsuarioAEquipo(1L, 1L);
    }

    @Test
    void testSalirmeDelEquipo() throws Exception {
        mockMvc.perform(post("/equipos/1/salir").session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/equipos/1"));

        verify(equipoService).eliminarUsuarioDeEquipo(1L, 1L);
    }

    @Test
    void testGestionEquiposVista() throws Exception {
        // GIVEN
        UsuarioData adminUsuario = new UsuarioData();
        adminUsuario.setId(1L);
        adminUsuario.setAdministrador(true); // Asegúrate de que es administrador

        EquipoData equipo1 = new EquipoData();
        equipo1.setId(1L);
        equipo1.setNombre("Equipo A");

        EquipoData equipo2 = new EquipoData();
        equipo2.setId(2L);
        equipo2.setNombre("Equipo B");

        List<EquipoData> equipos = Arrays.asList(equipo1, equipo2);

        when(usuarioService.findById(1L)).thenReturn(adminUsuario); // Mock para el administrador
        when(equipoService.findAllOrdenadoPorNombre()).thenReturn(equipos);

        // WHEN & THEN
        mockMvc.perform(get("/equipos/gestion-equipos").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("gestionEquipos"))
                .andExpect(model().attributeExists("equipos"))
                .andExpect(model().attribute("equipos", equipos));
    }


    @Test
    void testEliminarEquipo() throws Exception {
        // GIVEN
        doNothing().when(equipoService).eliminarEquipo(1L);

        // WHEN & THEN
        mockMvc.perform(post("/equipos/1/eliminar").session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/equipos/gestion-equipos"));

        verify(equipoService, times(1)).eliminarEquipo(1L);
    }

    @Test
    void testRenombrarEquipo() throws Exception {
        // GIVEN
        doNothing().when(equipoService).renombrarEquipo(1L, "Nuevo Nombre");

        // WHEN & THEN
        mockMvc.perform(post("/equipos/1/editar")
                        .param("nuevoNombre", "Nuevo Nombre")
                        .session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/equipos/gestion-equipos"));

        verify(equipoService, times(1)).renombrarEquipo(1L, "Nuevo Nombre");
    }

    @Test
    void testErrorRenombrarEquipoNombreVacio() throws Exception {
        // GIVEN
        EquipoData equipo = new EquipoData();
        equipo.setId(1L);
        equipo.setNombre("Equipo A");

        // Mock del servicio para devolver el equipo
        when(equipoService.recuperarEquipo(1L)).thenReturn(equipo);

        // WHEN & THEN
        mockMvc.perform(post("/equipos/1/editar")
                        .param("nuevoNombre", "") // Nombre vacío
                        .session(session))
                .andExpect(status().isOk()) // Debe devolver 200 (OK)
                .andExpect(view().name("formRenombrarEquipo")) // Vista del formulario
                .andExpect(model().attributeExists("error")) // Atributo de error en el modelo
                .andExpect(model().attribute("error", "El nombre del equipo no puede estar vacío"))
                .andExpect(model().attributeExists("equipo")) // Asegúrate de que el equipo está en el modelo
                .andExpect(model().attribute("equipo", equipo));
    }

    @Test
    void testErrorEliminarEquipoInexistente() throws Exception {
        // GIVEN
        doThrow(new EquipoServiceException("Equipo no encontrado"))
                .when(equipoService).eliminarEquipo(99L);

        // WHEN & THEN
        mockMvc.perform(post("/equipos/99/eliminar").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("gestionEquipos"))
                .andExpect(model().attributeExists("error"))
                .andExpect(model().attribute("error", "Equipo no encontrado"));

        verify(equipoService, times(1)).eliminarEquipo(99L);
    }

    @Test
    void testCreadorVeBotonesDeEdicion() throws Exception {
        // Configuración del equipo con creador
        EquipoData equipo = new EquipoData();
        equipo.setId(1L);
        equipo.setNombre("Proyecto AAA");
        equipo.setCreador("creador@example.com");

        // Configuración del usuario logueado como creador
        UsuarioData usuarioLogueado = new UsuarioData();
        usuarioLogueado.setId(1L);
        usuarioLogueado.setEmail("creador@example.com");

        List<UsuarioData> usuarios = new ArrayList<>();
        usuarios.add(usuarioLogueado);

        // Mock del equipo y usuario logueado
        when(equipoService.recuperarEquipo(1L)).thenReturn(equipo);
        when(equipoService.usuariosEquipo(1L)).thenReturn(usuarios);
        when(managerUserSession.usuarioLogeado()).thenReturn(usuarioLogueado.getId());
        when(usuarioService.findById(1L)).thenReturn(usuarioLogueado);

        // Simulación de sesión del usuario logueado
        session.setAttribute("idUsuarioLogeado", usuarioLogueado.getId());

        // Realización del test
        mockMvc.perform(get("/equipos/1").session(session))
                .andExpect(status().isOk())
                .andExpect(view().name("detalle"))
                .andExpect(model().attributeExists("equipo"))
                .andExpect(model().attributeExists("usuarios"))
                .andExpect(model().attribute("equipo", equipo))
                .andExpect(model().attribute("usuarios", usuarios))
                .andExpect(model().attribute("esCreador", true)) // Verifica que el creador se detecta correctamente
                .andExpect(content().string(containsString("Guardar"))) // Verifica que aparece el botón de guardar
                .andExpect(content().string(containsString("Cancelar"))); // Verifica que aparece el botón de cancelar
    }

    @Test
    void testRenombrarEquipoPorCreador() throws Exception {
        // Configuración del equipo
        EquipoData equipo = new EquipoData();
        equipo.setId(1L);
        equipo.setNombre("Proyecto AAA");
        equipo.setCreador("creador@example.com");

        UsuarioData usuarioLogueado = new UsuarioData();
        usuarioLogueado.setId(1L);
        usuarioLogueado.setEmail("creador@example.com");

        // Mock de dependencias
        when(managerUserSession.usuarioLogeado()).thenReturn(usuarioLogueado.getId());
        when(equipoService.recuperarEquipo(1L)).thenReturn(equipo);
        when(usuarioService.findById(usuarioLogueado.getId())).thenReturn(usuarioLogueado);

        // Realizar test
        mockMvc.perform(post("/equipos/1/renombrarCreador")
                        .param("nuevoNombre", "Proyecto Renombrado")
                        .session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/equipos/1"));

        // Verificar que el servicio se llamó con los parámetros correctos
        verify(equipoService, times(1)).renombrarEquipo(1L, "Proyecto Renombrado");
    }

    @Test
    void testRenombrarEquipoPorUsuarioNoCreador() throws Exception {
        // Configuración del equipo
        EquipoData equipo = new EquipoData();
        equipo.setId(1L);
        equipo.setNombre("Proyecto AAA");
        equipo.setCreador("otro@example.com");

        UsuarioData usuarioLogueado = new UsuarioData();
        usuarioLogueado.setId(1L);
        usuarioLogueado.setEmail("creador@example.com");

        // Mock de dependencias
        when(managerUserSession.usuarioLogeado()).thenReturn(usuarioLogueado.getId());
        when(equipoService.recuperarEquipo(1L)).thenReturn(equipo);
        when(usuarioService.findById(usuarioLogueado.getId())).thenReturn(usuarioLogueado);

        // Realizar test
        mockMvc.perform(post("/equipos/1/renombrarCreador")
                        .param("nuevoNombre", "Proyecto Renombrado")
                        .session(session))
                .andExpect(status().isForbidden());
    }

}

