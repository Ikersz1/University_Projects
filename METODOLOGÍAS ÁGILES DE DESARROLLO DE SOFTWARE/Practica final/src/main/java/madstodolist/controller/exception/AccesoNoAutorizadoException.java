package madstodolist.controller.exception;

import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

@ResponseStatus(HttpStatus.FORBIDDEN)
public class AccesoNoAutorizadoException extends RuntimeException {

    public AccesoNoAutorizadoException(String message) {
        super(message);
    }
}
