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

import java.util.Arrays;
import java.util.List;

import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.model;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.view;

@WebMvcTest(UsuarioController.class)
public class ListaUsuariosTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private UsuarioService usuarioService;

    @MockBean
    private EquipoService equipoService;

    @MockBean
    private ManagerUserSession managerUserSession;

    @BeforeEach
    void setup() {
        // Inicializar los mocks antes de cada test
        MockitoAnnotations.openMocks(this);
    }

    @Test
    public void testListadoUsuariosConPermisos() throws Exception {
        // Datos simulados de usuarios
        UsuarioData usuario1 = new UsuarioData();
        usuario1.setId(1L);
        usuario1.setEmail("usuario1@example.com");
        UsuarioData usuario2 = new UsuarioData();
        usuario2.setId(2L);
        usuario2.setEmail("usuario2@example.com");
        List<UsuarioData> usuarios = Arrays.asList(usuario1, usuario2);

        // Simulamos que el usuario tiene permisos de administrador y que hay usuarios en la lista
        when(usuarioService.esAdministrador(1L)).thenReturn(true);
        when(usuarioService.findAllUsuarios()).thenReturn(usuarios);

        mockMvc.perform(get("/registrados")
                        .sessionAttr("idUsuarioLogeado", 1L))
                .andExpect(status().isOk())
                .andExpect(view().name("listaUsuarios"))
                .andExpect(model().attributeExists("usuarios"))
                .andExpect(model().attribute("usuarios", usuarios));
    }
}
