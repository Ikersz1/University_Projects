package madstodolist.controller;

import madstodolist.authentication.ManagerUserSession;
import madstodolist.dto.EquipoData;
import madstodolist.dto.RegistroData;
import madstodolist.dto.UsuarioData;
import madstodolist.service.EquipoService;
import madstodolist.service.UsuarioService;
import madstodolist.controller.exception.UsuarioNotFoundException;
import madstodolist.controller.exception.AccesoNoAutorizadoException;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;


import javax.servlet.http.HttpSession;
import java.util.List;

@Controller
public class UsuarioController {

    @Autowired
    UsuarioService usuarioService;

    @Autowired
    EquipoService equipoService;

    @Autowired
    private ManagerUserSession managerUserSession;

    @GetMapping("/registrados")
    public String listadoUsuarios(Model model, HttpSession session) {
        Long idUsuarioLogeado = (Long) session.getAttribute("idUsuarioLogeado");

        if (idUsuarioLogeado == null || !usuarioService.esAdministrador(idUsuarioLogeado)) {
            throw new AccesoNoAutorizadoException("No tienes permiso para acceder a esta página.");
        }

        List<UsuarioData> usuarios = usuarioService.findAllUsuarios();
        model.addAttribute("usuarios", usuarios);
        return "listaUsuarios";
    }

    @GetMapping("/registrados/{id}")
    public String descripcionUsuario(@PathVariable Long id, Model model, HttpSession session) {
        System.out.println("Entrando a la ruta /gestion-equipos");
        Long idUsuarioLogeado = (Long) session.getAttribute("idUsuarioLogeado");

        if (!usuarioService.esAdministrador(idUsuarioLogeado)) {
            throw new AccesoNoAutorizadoException("No tienes permiso para acceder a esta página.");
        }

        UsuarioData usuario = usuarioService.findById(id);
        if (usuario == null) {
            throw new UsuarioNotFoundException("Usuario no encontrado");
        }

        model.addAttribute("usuario", usuario);
        return "descripcionUsuario";
    }

    @GetMapping("/usuarios/bloquear/{id}")
    public String bloquearUsuario(@PathVariable Long id, HttpSession session) {
        Long idUsuarioLogeado = (Long) session.getAttribute("idUsuarioLogeado");
        if (idUsuarioLogeado == null || !usuarioService.esAdministrador(idUsuarioLogeado)) {
            throw new AccesoNoAutorizadoException("No tienes permiso para realizar esta acción.");
        }
        usuarioService.cambiarEstadoBloqueoUsuario(id, true);
        return "redirect:/registrados";
    }

    @GetMapping("/usuarios/desbloquear/{id}")
    public String desbloquearUsuario(@PathVariable Long id, HttpSession session) {
        Long idUsuarioLogeado = (Long) session.getAttribute("idUsuarioLogeado");
        if (idUsuarioLogeado == null || !usuarioService.esAdministrador(idUsuarioLogeado)) {
            throw new AccesoNoAutorizadoException("No tienes permiso para realizar esta acción.");
        }
        usuarioService.cambiarEstadoBloqueoUsuario(id, false);
        return "redirect:/registrados";
    }

    // Mostrar la vista de perfil del usuario
    @GetMapping("/usuario/perfil")
    public String verPerfilUsuario(Model model) {
        Long usuarioId = managerUserSession.usuarioLogeado();
        UsuarioData usuario = usuarioService.findById(usuarioId);
        model.addAttribute("usuario", usuario);
        return "perfil";
    }

    // Actualizar los datos del usuario
    @PostMapping("/usuario/perfil")
    public String actualizarPerfil(@ModelAttribute UsuarioData usuarioActualizado, RedirectAttributes redirectAttributes) {
        Long usuarioId = managerUserSession.usuarioLogeado();
        usuarioService.actualizarUsuario(usuarioId, usuarioActualizado);
        redirectAttributes.addFlashAttribute("success", "Perfil actualizado correctamente.");
        return "redirect:/usuario/perfil";
    }
}

