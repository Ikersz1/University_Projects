package madstodolist.controller;

import madstodolist.controller.LoginController;
import madstodolist.controller.UsuarioController;
import madstodolist.service.EquipoService;
import madstodolist.service.UsuarioService;
import madstodolist.authentication.ManagerUserSession;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.mockito.MockitoAnnotations;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.mock.web.MockHttpSession;
import org.springframework.test.web.servlet.MockMvc;

import javax.servlet.http.HttpSession;

import static org.mockito.ArgumentMatchers.anyLong;
import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest({LoginController.class, UsuarioController.class})
public class BloqueoTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private UsuarioService usuarioService;

    @MockBean
    private EquipoService equipoService;

    @MockBean
    private ManagerUserSession managerUserSession;

    private MockHttpSession session;

    @BeforeEach
    void setup() {
        MockitoAnnotations.openMocks(this);
        session = new MockHttpSession();
    }

    @Test
    public void testBloquearUsuarioComoAdministrador() throws Exception {
        // Simulamos que el administrador está logueado
        Long idAdmin = 1L;
        when(managerUserSession.usuarioLogeado()).thenReturn(idAdmin);
        when(usuarioService.esAdministrador(idAdmin)).thenReturn(true);

        // Añadimos el ID del usuario logueado a la sesión
        session.setAttribute("idUsuarioLogeado", idAdmin);

        // Simulamos el bloqueo del usuario
        Long idUsuarioBloquear = 2L;
        mockMvc.perform(get("/usuarios/bloquear/" + idUsuarioBloquear).session(session))
                .andExpect(status().is3xxRedirection())
                .andExpect(view().name("redirect:/registrados"));
    }

    @Test
    public void testBloquearUsuarioSinPermisos() throws Exception {
        // Simulamos que un usuario no administrador está logueado
        Long idUsuario = 2L;
        when(managerUserSession.usuarioLogeado()).thenReturn(idUsuario);
        when(usuarioService.esAdministrador(idUsuario)).thenReturn(false);

        // Añadimos el ID del usuario logueado a la sesión
        session.setAttribute("idUsuarioLogeado", idUsuario);

        // Simulamos el intento de bloquear a otro usuario sin permisos
        Long idUsuarioBloquear = 3L;
        mockMvc.perform(get("/usuarios/bloquear/" + idUsuarioBloquear).session(session))
                .andExpect(status().isForbidden()) // Esperamos un estado 403 (forbidden)
                .andExpect(view().name("error")) // Verificamos que se muestre la vista de error
                .andExpect(model().attributeExists("errorMessage"))
                .andExpect(model().attribute("errorMessage", "No tienes permisos para acceder a esta página."));
    }
}
