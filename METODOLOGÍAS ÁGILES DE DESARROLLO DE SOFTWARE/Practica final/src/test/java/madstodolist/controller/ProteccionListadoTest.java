package madstodolist.controller;

import madstodolist.authentication.ManagerUserSession;
import madstodolist.service.EquipoService;
import madstodolist.service.UsuarioService;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.mockito.MockitoAnnotations;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.test.web.servlet.MockMvc;

import static org.mockito.ArgumentMatchers.anyLong;
import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.model;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.view;

@WebMvcTest(UsuarioController.class)
public class ProteccionListadoTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private EquipoService equipoService;

    @MockBean
    private UsuarioService usuarioService;

    @MockBean
    private ManagerUserSession managerUserSession;

    @BeforeEach
    void setup() {
        // Inicializar los mocks antes de cada test
        MockitoAnnotations.openMocks(this);
    }

    @Test
    public void testListadoUsuariosSinPermisos() throws Exception {
        // Simulamos que el usuario no tiene permisos de administrador
        when(usuarioService.esAdministrador(anyLong())).thenReturn(false);

        mockMvc.perform(get("/registrados")
                        .sessionAttr("idUsuarioLogeado", 1L)
                        .sessionAttr("esAdministrador", true))
                .andExpect(status().isForbidden())
                .andExpect(view().name("error"))
                .andExpect(model().attribute("errorMessage", "No tienes permisos para acceder a esta página."));
    }

    @Test
    public void testDescripcionUsuarioSinPermisos() throws Exception {
        // Simulamos que el usuario no tiene permisos de administrador
        when(usuarioService.esAdministrador(anyLong())).thenReturn(false);

        mockMvc.perform(get("/registrados/1")
                        .sessionAttr("idUsuarioLogeado", 1L)
                        .sessionAttr("esAdministrador", true))
                .andExpect(status().isForbidden())
                .andExpect(view().name("error"))
                .andExpect(model().attribute("errorMessage", "No tienes permisos para acceder a esta página."));
    }
}
