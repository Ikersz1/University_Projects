package madstodolist.controller;

import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.web.servlet.MockMvc;

import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;
import static org.hamcrest.Matchers.*;

@SpringBootTest
@AutoConfigureMockMvc
public class MenuControllerTest {

    @Autowired
    private MockMvc mockMvc;

    // Test para verificar la barra de menú
    @Test
    public void testMenuSinSesion() throws Exception {
        mockMvc.perform(get("/about"))
                .andExpect(status().isOk())
                // Verifica que los enlaces de Iniciar sesión y Registrarse aparecen
                .andExpect(content().string(containsString("Iniciar sesión")))
                .andExpect(content().string(containsString("Registrarse")))
                // Verifica que los enlaces "Tareas" y "Cerrar sesión" NO están disponibles
                .andExpect(content().string(not(containsString("Tareas"))))
                .andExpect(content().string(not(containsString("Cerrar sesión"))));
    }

    @Test
    public void testMenuConSesion() throws Exception {
        mockMvc.perform(get("/about")
                        .sessionAttr("idUsuarioLogeado", 1L) // Simulando que el usuario está logueado
                        .sessionAttr("nombreUsuario", "Juan")) // Nombre del usuario para probar el desplegable
                .andExpect(status().isOk())
                // Verifica que los enlaces "ToDoList", "Tareas" y el nombre del usuario "Juan" aparecen
                .andExpect(content().string(containsString("ToDoList")))
                .andExpect(content().string(containsString("Tareas")))
                .andExpect(content().string(containsString("Juan")))
                // Verifica que el enlace "Cerrar sesión" está disponible
                .andExpect(content().string(containsString("Cerrar sesión")))
                // Verifica que los enlaces "Login" y "Registro" NO aparezcan
                .andExpect(content().string(not(containsString("Login"))))
                .andExpect(content().string(not(containsString("Registro"))));
    }
}
