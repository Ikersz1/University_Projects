package madstodolist.controller;

import madstodolist.authentication.ManagerUserSession;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class HomeController {

    @Autowired
    private ManagerUserSession managerUserSession;

    @GetMapping("/about")
    public String about(Model model) {
        // Comprobar si el usuario está logueado
        Long idUsuarioLogeado = managerUserSession.usuarioLogeado();
        if (idUsuarioLogeado != null) {
            // Añadir los atributos al modelo
            model.addAttribute("idUsuarioLogeado", idUsuarioLogeado);
            model.addAttribute("nombreUsuario", managerUserSession.getNombreUsuario());
        }
        return "about";
    }
}
