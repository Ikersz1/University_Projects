package madstodolist.controller;

import madstodolist.controller.LoginController;
import madstodolist.service.UsuarioService;
import madstodolist.authentication.ManagerUserSession;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.mockito.MockitoAnnotations;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.test.web.servlet.MockMvc;

import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.view;

@WebMvcTest(LoginController.class) // Cambiar al controlador adecuado
public class UsuarioAdminTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private UsuarioService usuarioService;

    @MockBean
    private ManagerUserSession managerUserSession; // Simular la sesión de usuario

    @BeforeEach
    void setup() {
        MockitoAnnotations.openMocks(this);
    }

    @Test
    public void testRegistroUsuarioAdministradorYaExiste() throws Exception {
        // Simulamos que ya existe un administrador en el sistema
        when(usuarioService.existeAdministrador()).thenReturn(true);

        mockMvc.perform(get("/registro"))
                .andExpect(status().isOk())
                .andExpect(view().name("formRegistro")); // Verificar que el nombre de la vista es "formRegistro"
    }

    @Test
    public void testRegistroNuevoAdministrador() throws Exception {
        // Simulamos que no existe un administrador
        when(usuarioService.existeAdministrador()).thenReturn(false);

        // Realizamos la petición POST para registrar un nuevo administrador
        mockMvc.perform(post("/registro")
                        .param("email", "admin@test.com")
                        .param("nombre", "Admin")
                        .param("password", "password123")
                        .param("administrador", "true"))
                .andExpect(status().is3xxRedirection())
                .andExpect(view().name("redirect:/login"));
    }
}
