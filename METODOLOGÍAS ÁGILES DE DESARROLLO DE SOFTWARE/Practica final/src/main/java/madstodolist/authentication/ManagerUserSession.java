package madstodolist.authentication;

import madstodolist.model.Usuario;
import madstodolist.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import javax.servlet.http.HttpSession;

@Component
public class ManagerUserSession {

    private final HttpSession session;

    public ManagerUserSession(HttpSession session) {
        this.session = session;
    }

    public void logearUsuario(Long idUsuario) {
        session.setAttribute("idUsuarioLogeado", idUsuario);
    }

    public Long usuarioLogeado() {
        return (Long) session.getAttribute("idUsuarioLogeado");
    }

    public void logout() {
        session.invalidate();
    }

    public String getNombreUsuario() {
        return (String) session.getAttribute("nombreUsuario");
    }

}
