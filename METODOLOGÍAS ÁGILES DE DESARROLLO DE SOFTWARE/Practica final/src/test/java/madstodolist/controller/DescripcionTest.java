package madstodolist.controller;

import madstodolist.authentication.ManagerUserSession;
import madstodolist.controller.UsuarioController;
import madstodolist.dto.UsuarioData;
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
public class DescripcionTest {

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
    public void testDescripcionUsuario() throws Exception {
        // Simulamos un usuario
        UsuarioData usuarioData = new UsuarioData();
        usuarioData.setId(1L);
        usuarioData.setEmail("juan@example.com");
        usuarioData.setNombre("Juan");
        usuarioData.setAdministrador(false);
        usuarioData.setBloqueado(false);

        // Configuramos el mock del servicio para devolver el usuario simulado
        when(usuarioService.findById(anyLong())).thenReturn(usuarioData);
        when(usuarioService.esAdministrador(anyLong())).thenReturn(true);

        // Realizamos la petición y verificamos el resultado
        mockMvc.perform(get("/registrados/1")
                        .sessionAttr("idUsuarioLogeado", 1L)
                        .sessionAttr("esAdministrador", true))
                .andExpect(status().isOk())
                .andExpect(view().name("descripcionUsuario"))
                .andExpect(model().attribute("usuario", usuarioData))
                .andExpect(model().attributeDoesNotExist("password"));
    }
}
