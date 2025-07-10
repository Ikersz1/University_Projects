package madstodolist.controller;

import madstodolist.dto.EquipoData;
import madstodolist.dto.TareaData;
import madstodolist.dto.UsuarioData;
import madstodolist.model.Equipo;
import madstodolist.model.Usuario;
import madstodolist.service.EquipoService;
import madstodolist.service.EquipoServiceException;
import madstodolist.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import madstodolist.authentication.ManagerUserSession;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@Controller
@RequestMapping("/equipos")
public class EquipoController {

    @Autowired
    private EquipoService equipoService;

    @Autowired
    private UsuarioService usuarioService;

    @Autowired
    ManagerUserSession managerUserSession;

    // Listado de equipos ordenados alfabéticamente
    @GetMapping
    public String listadoEquipos(Model model) {
        List<EquipoData> equipos = equipoService.findAllOrdenadoPorNombre();
        model.addAttribute("equipos", equipos);
        return "listado"; // Nombre del template para el listado de equipos
    }
    @GetMapping("/{id}")
    public String detalleEquipo(@PathVariable Long id, Model model) {
        EquipoData equipo = equipoService.recuperarEquipo(id);
        if (equipo == null) {
            return "404";
        }

        List<UsuarioData> usuarios = equipoService.usuariosEquipo(id);
        Long usuarioId = managerUserSession.usuarioLogeado();
        UsuarioData usuarioLogueado = usuarioService.findById(usuarioId);

        boolean esMiembro = usuarios.stream().anyMatch(u -> u.getId().equals(usuarioId));
        boolean esCreador = usuarioLogueado.getEmail().equals(equipo.getCreador());

        // Obtener las tareas del equipo
        List<TareaData> tareas = equipoService.listarTareasDeEquipo(id);

        // Añadir atributos al modelo
        model.addAttribute("equipo", equipo);
        model.addAttribute("usuarios", usuarios);
        model.addAttribute("tareas", tareas); // Añadir tareas al modelo
        model.addAttribute("esMiembro", esMiembro);
        model.addAttribute("esCreador", esCreador);

        // Añadir la fecha de creación al modelo
        model.addAttribute("fechaCreacion", equipo.getFechaCreacion());

        return "detalle"; // Nombre del template
    }

    @GetMapping("/nuevo")
    public String formularioNuevoEquipo(Model model) {
        model.addAttribute("equipoData", new EquipoData());
        return "formNuevoEquipo";
    }

    // Procesar la creación de un nuevo equipo
    @PostMapping("/nuevo")
    public String crearNuevoEquipo(@ModelAttribute EquipoData equipoData, Model model) {
        if (equipoData.getNombre() == null || equipoData.getNombre().trim().isEmpty()) {
            model.addAttribute("error", "El nombre del equipo no puede estar vacío");
            return "formNuevoEquipo";
        }
        try {
            Long usuarioId = managerUserSession.usuarioLogeado();
            equipoService.crearEquipoConCreador(equipoData.getNombre(), usuarioId);
            return "redirect:/equipos";
        } catch (EquipoServiceException e) {
            model.addAttribute("error", e.getMessage());
            return "formNuevoEquipo";
        }
    }


    // Añadirse al equipo
    @PostMapping("/{id}/añadirme")
    public String añadirmeAlEquipo(@PathVariable Long id) {
        Long usuarioId = managerUserSession.usuarioLogeado();
        equipoService.añadirUsuarioAEquipo(id, usuarioId);
        return "redirect:/equipos/" + id;
    }

    // Salir del equipo
    @PostMapping("/{id}/salir")
    public String salirDelEquipo(@PathVariable Long id) {
        Long usuarioId = managerUserSession.usuarioLogeado();
        equipoService.eliminarUsuarioDeEquipo(id, usuarioId);
        return "redirect:/equipos/" + id;
    }

    @GetMapping("/gestion-equipos")
    public String gestionEquipos(Model model) {
        Long usuarioId = managerUserSession.usuarioLogeado();
        UsuarioData usuario = usuarioService.findById(usuarioId);

        System.out.println("Entrando a la ruta /gestion-equipos");
        System.out.println("¿Es administrador?: " + usuario.isAdministrador());
        List<EquipoData> equipos = equipoService.findAllOrdenadoPorNombre();
        model.addAttribute("equipos", equipos);
        return "gestionEquipos"; // Nombre del archivo Thymeleaf en templates
    }

    // Eliminar un equipo
    @PostMapping("/{id}/eliminar")
    public String eliminarEquipo(@PathVariable Long id, Model model) {
        System.out.println("Eliminando");
        try {
            equipoService.eliminarEquipo(id);
            return "redirect:/equipos/gestion-equipos"; // Redirige a la gestión de equipos
        } catch (EquipoServiceException e) {
            model.addAttribute("error", e.getMessage());
            return "gestionEquipos"; // Vuelve a cargar la página con un mensaje de error
        }
    }

    // Mostrar formulario para renombrar un equipo
    @GetMapping("/{id}/editar")
    public String formularioRenombrarEquipo(@PathVariable Long id, Model model) {
        EquipoData equipo = equipoService.recuperarEquipo(id);
        if (equipo == null) {
            return "404"; // Redirige a una página de error si el equipo no existe
        }
        model.addAttribute("equipo", equipo);
        return "formRenombrarEquipo"; // Nombre de la nueva vista para renombrar
    }

    @PostMapping("/{id}/editar")
    public String renombrarEquipo(@PathVariable Long id, @RequestParam String nuevoNombre, Model model) {
        if (nuevoNombre == null || nuevoNombre.trim().isEmpty()) {
            EquipoData equipo = equipoService.recuperarEquipo(id);
            model.addAttribute("equipo", equipo); // Asegúrate de que el modelo incluya el equipo
            model.addAttribute("error", "El nombre del equipo no puede estar vacío");
            return "formRenombrarEquipo"; // Vuelve a la vista del formulario
        }
        try {
            equipoService.renombrarEquipo(id, nuevoNombre);
            return "redirect:/equipos/gestion-equipos";
        } catch (EquipoServiceException e) {
            EquipoData equipo = equipoService.recuperarEquipo(id);
            model.addAttribute("equipo", equipo);
            model.addAttribute("error", e.getMessage());
            return "formRenombrarEquipo";
        }
    }

    @PostMapping("/{id}/expulsar")
    public String expulsarUsuario(@PathVariable Long id, @RequestParam Long usuarioId) {
        Long usuarioLogueadoId = managerUserSession.usuarioLogeado();
        EquipoData equipo = equipoService.recuperarEquipo(id);
        if (!usuarioService.findById(usuarioLogueadoId).getEmail().equals(equipo.getCreador())) {
            throw new RuntimeException("No tienes permisos para esta acción");
        }
        equipoService.eliminarUsuarioDeEquipo(id, usuarioId);
        return "redirect:/equipos/" + id;
    }


    @PostMapping("/{id}/renombrarCreador")
    public String renombrarEquipoCreador(@PathVariable Long id, @RequestParam String nuevoNombre) {
        Long usuarioLogueadoId = managerUserSession.usuarioLogeado();
        EquipoData equipo = equipoService.recuperarEquipo(id);

        if (equipo == null) {
            throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Equipo no encontrado");
        }

        if (!usuarioService.findById(usuarioLogueadoId).getEmail().equals(equipo.getCreador())) {
            throw new ResponseStatusException(HttpStatus.FORBIDDEN, "No tienes permisos para esta acción");
        }

        equipoService.renombrarEquipo(id, nuevoNombre);
        return "redirect:/equipos/" + id;
    }



}
